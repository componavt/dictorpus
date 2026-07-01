<?php

namespace App\Traits\Modify;

use App\Library\Grammatic;

use App\Models\Corpus\Sentence;

trait WordModify
{
    public static function wordAddToSentence($is_word, $word, $str, $word_count)
    {
        if ($is_word) { // the previous char is part of a word, the word ends
            if (!preg_match("/([a-zA-ZА-Яа-яЁё])/u", $word, $regs)) {
                $str .= $word;
            } else {
                //dd($regs);
                $str .= '<w id="' . $word_count++ . '">' . $word . '</w>';
            }
            $is_word = false;
        }
        return [$is_word, $str, $word_count];
    }

    /**
     * Divides string on words
     * Suppose that the first and the last symbols of the string are the word chars, they are not special
     *
     * @param string $token  text without mark up
     * @param integer $w_id  initial w_id
     *
     * @return array text with markup (split to words) and array of words
     */
    public static function splitWord($token, $w_id): array
    {
        $str = ''; // Строка, которая будет собрана заново: текст + служебная разметка <w id="...">...</w>
        $i = 0; // Позиция в исходном токене
        $is_word = TRUE; // Флаг: сейчас мы внутри слова или нет. Считаем, что первый символ токена принадлежит слову
        $words = []; // Массив найденных слов: ключ = w_id, значение = текст слова
        $word = ''; // Буфер текущего слова

        while ($i < mb_strlen($token)) { // Посимвольный проход по токену
            $char = mb_substr($token, $i, 1);
            if ($char == '<') { // begin of a tag 
                // Если перед тегом мы были внутри слова, закрываем текущее слово
                list($is_word, $str, $word, $words) = self::endWord($is_word, $str, $word, $words, $w_id);
                // Выносим тег целиком во внешний текст, не включая его в слово
                list($i, $str) = self::tagOutWord($token, $i, $str);

                // the char is a delimeter or white space
            } elseif (
                mb_strpos(Sentence::word_delimeters(), $char) !== false || preg_match("/\s/", $char)
                // if word is ending with a dash, the dash is putting out of the word
                || $is_word && Sentence::dashIsOutOfWord($char, $token, $i)
            ) {

                // Закрываем текущее слово, если оно было открыто
                list($is_word, $str, $word, $words) = self::endWord($is_word, $str, $word, $words, $w_id);
                $str .= $char; // Сам разделитель добавляем в результирующую строку как есть

            } else {
                // Если сейчас мы вне слова и текущий символ может начинать новое слово, открываем новый тег <w id="...">
                if (!$is_word && !Sentence::dashIsOutOfWord($char, $token, $i)) {
                    $is_word = true;
                    $str .= '<w id="' . $w_id++ . '">';
                }
                if ($is_word) {
                    $word .= $char; // Если мы внутри слова, копим символ в буфер слова
                } else {
                    $str .= $char; // Иначе символ просто идёт во внешний текст без word-разметки           
                }
            }
            $i++;
        }
        if ($word !== '') {
            $str .= $word; // После завершения цикла дописываем остаток последнего слова
            $words[$w_id - 1] = $word; // И сохраняем его в массив слов под последним использованным w_id
        }
        return [$str, $words];
    }

    public function splitInSentence($word, $cyr_word = '')
    {
        $text_obj = $this->text;
        $word_obj = $this; // Текущий объект слова; дальше он может стать "левой" частью после split

        $sent_obj = Sentence::whereTextId($this->text_id)
            ->whereSId($this->s_id)->first();
        if (!$sent_obj) {
            return;
        }

        $sentence = $sent_obj->text_xml;

        $new_w_id = self::nextWId($this->text_id); // Получаем следующий свободный w_id в пределах текста
        //        $left_w_id = $word_obj->w_id;

        list($str, $words) = self::splitWord($word, $new_w_id);

        $i = mb_strpos($sentence, '<w id="' . $this->w_id . '">');
        $j = mb_strpos($sentence, '</w>', $i + 7);
        $new_sentence = mb_substr($sentence, 0, $i) . '<w id="' . $this->w_id . '">' . $str . mb_substr($sentence, $j);
        $sent_obj->text_xml = $new_sentence;
        $sent_obj->save();

        $lang_id = $text_obj->lang_id;

        foreach ($words as $k => $w) {
            $word_for_search = Grammatic::changeLetters($words[$k], $lang_id);
            if ($k >= $new_w_id) {
                $word_obj = self::create(['text_id' => $this->text_id, 'sentence_id' => $sent_obj->id, 's_id' => $sent_obj->s_id, 'w_id' => $k, 'word' => $word_for_search]);
            } else {
                $word_obj->word = $word_for_search;
                $word_obj->save();
            }
            $word_obj->setMeanings([], $lang_id);
            $text_obj->setWordforms([], $word_obj);
        }

        if ($cyr_word) {
            $this->splitCyrWord($cyr_word, $new_w_id);
        }

        // пересчитываем word_number у слов в предложении
        $sent_obj->recountWordNumbers();

        // исправляем ссылку на левое слово у пунктуации
        $sent_obj->fixPunctsAfterSplit($this->id, $new_w_id);
    }

    public function splitCyrWord($cyr_word, $next_word_count)
    {
        $text_obj = $this->text;
        list($str, $words) = self::splitWord($cyr_word, $next_word_count);

        $cyrtext_obj = $this->text->cyrtext;
        if (empty($cyrtext_obj)) {
            return;
        }
        $text = $cyrtext_obj->text_xml;
        $i = mb_strpos($text, '<w id="' . $this->w_id . '">');
        $j = mb_strpos($text, '</w>', $i + 7);
        $new_text = mb_substr($text, 0, $i) . '<w id="' . $this->w_id . '">' . $str . mb_substr($text, $j);
        $cyrtext_obj->text_xml = $new_text;
        $cyrtext_obj->save();
    }
}
