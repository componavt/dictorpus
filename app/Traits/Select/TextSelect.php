<?php namespace App\Traits\Select;

use App\Models\Corpus\Word;
use App\Models\Dict\Gramset;

trait TextSelect
{
    public static function concordanceForIds($text_ids) {
        $texts = Text::whereIn('id', $ids)->get();
        
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
            $gramset_info = !empty($gramset) ? $gramset->gramsetString() : '';
                  //часть речи  грам. признаки             слово норм.  исход. написание         лемма        значение
            $table[$word->code][$gramset_info][$word->word][$cyr_words[$word->w_id]][$word->lemma][$word->meaning_text] = 
                    empty ($table[$word->code][$gramset_info][$word->word][$cyr_words[$word->w_id]][$word->lemma][$word->meaning_text]) ? 1
                    : 1+ $table[$word->code][$gramset_info][$word->word][$cyr_words[$word->w_id]][$word->lemma][$word->meaning_text];
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
            $table[''][''][$word->word][$cyr_words[$word->w_id]][''][''] 
                    = empty($table[''][''][$word->word][$cyr_words[$word->w_id]]['']['']) ? 1
                    : 1+$table[''][''][$word->word][$cyr_words[$word->w_id]][''][''];
        }
        return $table;        
    }
}