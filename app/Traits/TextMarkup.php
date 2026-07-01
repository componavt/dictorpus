<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Corpus\Sentence;

trait TextMarkup
{
    public static function preProcessText($text)
    {
        $end1 = ['.', '?', '!', '…', '|'];
        $end2 = ['.»', '?»', '!»', '."', '?"', '!"', '.”', '?”', '!”', '.“'];
        $pseudo_end = false;
        if (!in_array(mb_substr($text, -1, 1), $end1) && !in_array(mb_substr($text, -1, 2), $end2)) {
            $text .= '.';
            $pseudo_end = true;
        }
        $text = str_replace("\r\n", "\n", nl2br($text));
        return [$text, $pseudo_end];
    }


    /**
     * Gets a markup text with sentences
     * 
     * ^ - to ignore end of sentence
     *
     * @param string $text  text without mark up
     * @param boolean $with_words  if it is true, sentences divided into words
     * @param boolean $by_sentences  if it is true, return only text structure and the array of sentences
     *                                        false, return full marked text
     * return string text with markup (split to sentences and words) if $by_sentences=false
     *      OR [<markup text>, <sentences>] if $by_sentences=true
     */
    public static function markupText($text, $with_words = true, $by_sentences = false)
    {
        list($text, $pseudo_end) = self::preProcessText(trim($text));
        $text = convert_quotes($text);

        $text_xml = '';
        $sen_count = $word_count = 1;
        $sentences = [];
        $prev = '';

        if (preg_match_all(
            "/(.+?)(\||\.|\?|!|\.»|\?»|!»|\.\"|\?\"|!\"|\.”|\?”|!”|…{1,})(\s|(\<br \/\>\n)+?|$)/is", // :| //
            $text,
            $desc_out
        )) {
            for ($k = 0; $k < sizeof($desc_out[1]); $k++) {
                $sentence = $prev . trim($desc_out[1][$k]);

                if ($k == sizeof($desc_out[1]) - 1 && $pseudo_end || $desc_out[2][$k] == '|') {
                    $desc_out[2][$k] = '';
                }


                if ($k < sizeof($desc_out[1]) - 1 && preg_match("/^\s*\^/", $desc_out[1][$k + 1])) {
                    $prev = $sentence . $desc_out[2][$k];
                    continue;
                }

                $prev = '';

                // <br> in in the beginning of the string is moved before the sentence
                while (preg_match("/^(<br(| \/)>)(.+)$/is", $sentence, $regs)) {
                    $text_xml .= $regs[1] . "\n";
                    $sentence = trim($regs[3]);
                }
                // division on words
                list($str, $word_count) = Sentence::markup($sentence, $word_count);
                //                $str = str_replace('¦', '', $str);
                $sentences[$sen_count] = "<s id=\"" . $sen_count . '">' . $str . $desc_out[2][$k] . "</s>\n";
                $text_xml .= $by_sentences ? "<s id=\"" . $sen_count . '"/>'
                    : $sentences[$sen_count];
                $sen_count++;
                $div = trim($desc_out[3][$k]);
                $text_xml .= $div ? $div . "\n" : '';
            }
        }
        //dd($text_xml);
        return $by_sentences ? [trim($text_xml), $sentences] : trim($text_xml);
    }

    /**
     * Sets text_xml as a markup text with sentences
     * эта функция вызывается из контроллера на 2м этапе разметки
     */
    public function markup()
    {
        DB::listen(function ($query) {
            if ($query->time >= 20) {
                \Log::info('SLOW SQL', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time_ms' => $query->time,
                ]);
            }
        });

        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        $ts = microtime(true);

        list($this->text_structure, $sentences) = self::markupText($this->text, true, true);
        Log::info('markupText done', ['sec' => microtime(true) - $ts]);

        $sentencesData = [];
        $last_s_id = null;

        foreach ($sentences as $s_id => $text_xml) {
            $last_s_id = $s_id;
            $t1 = microtime(true);

            $sentence = Sentence::store($this->id, $s_id, $text_xml);
            Log::info('Sentence::store', ['s_id' => $s_id, 'sec' => microtime(true) - $t1]);

            list($sxe, $error_message) = self::toXML($text_xml, $s_id);

            if ($error_message) {
                print $error_message;
                continue;
            }

            // ВАЖНО: вызываем checkedWords ДО удаления старых записей,
            // т.к. w_id стабильно переиспользуется между старой и новой разметкой
            $checked_words = $this->checkedWords($text_xml);

            $sentencesData[] = [
                'sentence' => $sentence,
                'sxe' => $sxe,
                'checked_words' => $checked_words,
            ];
        }

        $t3 = microtime(true);
        $this->updateMeaningAndWordformForText($sentencesData);
        Log::info('updateMeaningAndWordformForText', ['sec' => microtime(true) - $t3]);

        if ($last_s_id !== null) {
            DB::statement("DELETE FROM sentences WHERE s_id>$last_s_id and text_id=" . (int)$this->id);
        }
    }

    public function cyrToSentence($sentence, $words)
    {
        if (empty($sentence) || empty($words)) {
            return $sentence;
        }
        foreach ($words as $i => $word) {
            $sentence = preg_replace("/(<w\s+id=\"" . $i . "\"\>[^<]+)(\<\/w\>)/", '${1}<sup>' . $word . '</sup>${2}', $sentence);
        }
        return $sentence;
    }
}
