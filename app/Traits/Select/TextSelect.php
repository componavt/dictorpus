<?php namespace App\Traits\Select;

use DB;

use App\Library\Grammatic\KarGram;

use App\Models\Corpus\Word;
use App\Models\Corpus\Sentence;
use App\Models\Corpus\SentenceFragment;
use App\Models\Corpus\SentenceTranslation;

use App\Models\Dict\Gramset;
use App\Models\Dict\Meaning;

trait TextSelect
{
    public static function concordanceForIds($text_ids) {
        $texts = self::whereIn('id', $text_ids)->get();
        
        $table = [];
        foreach ($texts as $text) {   
            $table = $text->concordance($table);
        }
        return $table;
    }
    
    public function concordance($table=[]) {
        $cyr_words = !empty($this->cyrtext) ? $this->cyrtext->getWordsFromXML(true) : [];
        $table = $this->concordanceForChecked($table, $cyr_words);
        $table = $this->concordanceForNew($table, $cyr_words);
        ksort($table);
        return $table;        
    }
    
    public function concordanceForChecked($table, $cyr_words) {
        $words = Word::where('words.text_id', $this->id)
                     ->join('meaning_text', 'words.id', '=', 'meaning_text.word_id')
                     ->join('meanings', 'meanings.id', '=', 'meaning_text.meaning_id')
                     ->join('lemmas', 'lemmas.id', '=', 'meanings.lemma_id')
                     ->join('meaning_texts', 'meanings.id', '=', 'meaning_texts.meaning_id')
                     ->join('parts_of_speech', 'parts_of_speech.id', '=', 'lemmas.pos_id')
                     ->where('meaning_texts.lang_id', 2)
                     ->where('meaning_text.relevance', '>', 1)
                     ->selectRaw('words.id as word_id, code, word, lemma, meaning_text, words.w_id as w_id')->get();
        $text_id = $this->id;
        foreach ($words as $word) {
            $gramset = Gramset::whereIn('id', function ($q) use ($word, $text_id) {
                $q->select('gramset_id')->from('text_wordform')
                  ->whereWId($word->w_id)
                  ->whereTextId($text_id)
                  ->where('relevance', '>', 1);
            })->first();
//dd($word, to_sql($gramset));      
            $cyr_word = empty($cyr_words[$word->w_id]) ? '' : $cyr_words[$word->w_id];
            $gramset_info = !empty($gramset) ? $gramset->gramsetString() : '';
                  //часть речи  грам. признаки             слово норм.  исход. написание         лемма        значение
            $table[$word->code][$gramset_info][$word->word][$cyr_word][$word->lemma][$word->meaning_text] = 
                    empty ($table[$word->code][$gramset_info][$word->word][$cyr_word][$word->lemma][$word->meaning_text]) ? 1
                    : 1+ $table[$word->code][$gramset_info][$word->word][$cyr_word][$word->lemma][$word->meaning_text];
        }
        return $table;        
    }
// select count(*) from text_wordform where word_id >0 and word_id not in (select id from words);
    public function concordanceForNew($table, $cyr_words) {
        $words = Word::where('words.text_id', $this->id)
                     ->whereNotIn('id', function ($q) {
                         $q->select('word_id')->from('meaning_text');
                     })->get();
        foreach ($words as $word) {
            $cyr_word = empty($cyr_words[$word->w_id]) ? '' : $cyr_words[$word->w_id];
            $table[''][''][$word->word][$cyr_word][''][''] 
                    = empty($table[''][''][$word->word][$cyr_word]['']['']) ? 1
                    : 1+$table[''][''][$word->word][$cyr_word][''][''];
        }
        return $table;        
    }
    
    /**
     * возвращает массив предложений с переводами, первым элементом - заголовки текста
     * @return array
     */
    public function sentencesWithTranslation($sentences) {
        $transtext = $this->transtext;
        if (empty($transtext)) {
            return $sentences;
        }
        $trans_sentences = $transtext->getSentencesFromXML();
        if (empty($trans_sentences)) {
            return $sentences;
        }
        // либо нет цифр в переводе, либо цифры есть в оригинале
        if (!preg_match("/\d+/", $transtext->title) || preg_match("/\d+/", $this->title)) {
            $sentences[$this->title] = $transtext->title;
        }
        $sents = Sentence::whereTextId($this->id)->orderBy('s_id')->get();
        foreach ($sents as $sentence) {
            $s = KarGram::changeLetters(self::clearText($sentence->text_xml));
            $ts = self::clearText($trans_sentences[$sentence->s_id]['sentence']);
//            $sentences[!empty($s) ? $s : $this->id.'_'.$sentence->s_id] = $ts;
            if (!empty($s)) {
                $sentences[$s] = $ts;
            }
        }
        return $sentences;
    }

// select text_id, s_id from meaning_text where relevance=10 and meaning_id in (select id from meanings where lemma_id in 
// (select lemma_id from label_lemma where label_id=3 and status=1)) group by text_id, s_id;
    public static function sentencesFromOlodict($sentences, $without_text_ids=[]) {
        $label_id=3;
        $sids = [];
        $meanings = Meaning::whereIn('lemma_id', function ($q) use ($label_id) {
            $q->select('lemma_id')->from('label_lemma')
              ->whereLabelId($label_id)
              ->whereStatus(1);
        })->get();
        foreach ($meanings as $meaning) {
            $mtexts = DB::table('meaning_text')->whereMeaningId($meaning->id)
                        ->whereNotIn('text_id', $without_text_ids)
                        ->whereRelevance(10)->get();
            foreach ($mtexts as $mtext) {
                $sids[$mtext->text_id][$mtext->s_id][]=$mtext->w_id;
            }
        }
        foreach ($sids as $text_id => $sents) {
            $text = self::find($text_id);
            if (empty($text)) {
                continue;
            }
            $trans_sentences = !empty($text->transtext) ? $text->transtext->getSentencesFromXML() : [];
            foreach ($sents as $s_id=>$w_ids) {
                $sentence = Sentence::whereTextId($text_id)->whereSId($s_id)->first();
                foreach ($w_ids as $w_id) {
                    $fragment = SentenceFragment::getBySW($sentence->id, $w_id);
                    $s = KarGram::changeLetters(self::clearText($fragment ? $fragment->text_xml: $sentence->text_xml));
                    $ts = process_text(SentenceTranslation::getTextForLocale($sentence->id, $w_id));
                    if (empty($ts) && !empty($trans_sentences[$s_id])) {
                        $ts = $trans_sentences[$s_id]['sentence'];
                    }
                    if (empty($ts)) {
                        continue;
                    }
                    $sentences[!empty($s) ? $s : $text_id.'_'.$s_id] = self::clearText($ts);
                }
            }
        }
        return $sentences;
    }
    
    public static function clearText($text) {
        return preg_replace("/^\[*\s*[\.\,\–\–\—\-\‒:\d\s\*]+/u", "", 
                    preg_replace("/[\^\|¦]/u", "", 
                            preg_replace("/\s+/", " ", 
                                    trim(
                                            strip_tags($text)))));
    }
}