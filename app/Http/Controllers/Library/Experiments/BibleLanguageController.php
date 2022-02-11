<?php

namespace App\Http\Controllers\Library\Experiments;

use App\Http\Controllers\Controller;

use App\Models\Corpus\Text;
use App\Models\Corpus\Word;

use App\Models\Dict\Gram;
use App\Models\Dict\Gramset;

class BibleLanguageController extends Controller
{
/* нет редактирования, можно открыть
 *     public function __construct()
    {
        $this->middleware('auth:dict.edit,/'); 
    }
*/
    /**
     */
    public function index() {
        $text_ids = [3601, 3635, 3629, 3636, 3634, 3643, 3591];
        $texts = Text::whereIn('id', $text_ids)->get();
        
        $gram_ids = [42=>'gram_id_infinitive', 48=>'gram_id_mood', 28=>'gram_id_mood'];
        $grams = Gram::whereIn('id', array_keys($gram_ids))->get();
//dd($grams);        
        foreach ($gram_ids as $gram_id=>$gram_field) {
//        $inf3_ids = Gramset::where('gram_id_infinitive',42)->pluck('id')->toArray();
            $words[$gram_id] = Word::whereIn('text_id', $text_ids)
                              ->whereIn('id', function ($q) use ($gram_id, $gram_field){
                                  $q->select('word_id')->from('text_wordform')
                                    ->whereIn('gramset_id', function ($q2) use ($gram_id, $gram_field){
                                        $q2->select('id')->from('gramsets')
                                           ->where($gram_field, $gram_id);
                                    });
                              })
                              ->orderBy('text_id', 'w_id')->get();
        }       
        
        $ta_id = 15795;
        $ta_positions = [
            1=>'В начале предложения',
            2=>'После запятой', 
            3=>'После союза а', 
            4=>'После частицы ni',
            5=>'Другие'];
        $ta_words = Word::whereIn('text_id', $text_ids)
                         ->whereIn('id', function ($q) use ($ta_id){
                             $q->select('word_id')->from('meaning_text')
                                    ->whereIn('meaning_id', function ($q2) use ($ta_id){
                                        $q2->select('id')->from('meanings')
                                           ->where('lemma_id', $ta_id);
                                    });
                         })
                         ->orderBy('text_id', 'w_id')->get();
        $tap_words = [];
        foreach ($ta_words as $word) {
            if ($word->word_number == 1) {
                $tap_words[1][]=$word;
            } elseif ($word->getPrevSign() == ',') {
                $tap_words[2][]=$word;
            } elseif ($word->getPrevWord() == 'a') {
                $tap_words[3][]=$word;
            } elseif ($word->getPrevWord() == 'ni') {
                $tap_words[4][]=$word;
            } else {
                $sentence = $word->getClearSentence();
                $tap_words[5][]=$word;
            }
        }
        return view('experiments/bible_language/index', 
                compact('grams', 'tap_words', 'ta_positions', 'texts', 'words'));
    }
    
}
