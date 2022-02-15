<?php

namespace App\Http\Controllers\Library\Experiments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Library\Experiments\BibleLanguage;

use App\Models\Corpus\Text;

class BibleLanguageController extends Controller
{
/* нет редактирования, можно открыть
 *     public function __construct()
    {
        $this->middleware('auth:dict.edit,/'); 
    }
*/
    public function index() {
        return view('experiments.bible_language.index');
    }

    public function forAll(Request $request) {
//        $lang_id=4;
        $lang_id = (int)$request->input('lang_id');
        $for_selection = (int)$request->input('for_selection');
        if ($for_selection) {
            $text_ids = BibleLanguage::getTextSelection('all');
            if (!$text_ids) {
                    return;
            }
            $lang_id = Text::find($text_ids[0])->lang_id;
        } else {
            $text_ids = null;
        }
        
        $corpuses = [2=>'библейские',
                     3=>'публицистические',
                     8=>'художественные',
                    ];
        $stats = [];
        foreach (array_keys($corpuses) as $corpus_id) {
            $text_total = BibleLanguage::textTotal($corpus_id, $lang_id, $text_ids);
            $stats['text_total'][$corpus_id] = $text_total;
            
            $word_total = BibleLanguage::wordTotal($corpus_id, $lang_id, $text_ids);
            $stats['word_total'][$corpus_id] = number_format($word_total, 0, ',', ' ');
            
            $stats['words_to_texts'][$corpus_id] = round($word_total/$text_total, 2);
            
            $linked_words = BibleLanguage::linkedWordTotal($corpus_id, $lang_id, $text_ids);
            $stats['linked_words'][$corpus_id] = number_format($linked_words, 0, ',', ' ');
            
            $stats['linked_words_to_all'][$corpus_id] = number_format(100*$linked_words/$word_total, 0, ',', ' ');
            
            $inf3_total = BibleLanguage::inf3Total($corpus_id, $lang_id, $text_ids);
            $stats['inf3_total'][$corpus_id] = $inf3_total;
            $stats['inf3_to_all'][$corpus_id] = round(100*$inf3_total/$word_total, 2);
            $stats['inf3_to_linked'][$corpus_id] = round(100*$inf3_total/$linked_words, 2);
            
            $cond_total = BibleLanguage::condTotal($corpus_id, $lang_id, $text_ids);
            $stats['cond_total'][$corpus_id] = $cond_total;
            $stats['cond_to_all'][$corpus_id] = round(100*$cond_total/$word_total, 2);
            $stats['cond_to_linked'][$corpus_id] = round(100*$cond_total/$linked_words, 2);
            
            $pot_total = BibleLanguage::potTotal($corpus_id, $lang_id, $text_ids);
            $stats['pot_total'][$corpus_id] = $pot_total;
            $stats['pot_to_all'][$corpus_id] = round(100*$pot_total/$word_total, 2);
            $stats['pot_to_linked'][$corpus_id] = round(100*$pot_total/$linked_words, 2);
            
            $ta_total = BibleLanguage::taTotal($corpus_id, $lang_id, $text_ids);
            $stats['ta_total'][$corpus_id] = $ta_total;
            $stats['ta_to_all'][$corpus_id] = round(100*$ta_total/$word_total, 2);
            $stats['ta_to_linked'][$corpus_id] = round(100*$ta_total/$linked_words, 2);
            
            $a_total = BibleLanguage::aTotal($corpus_id, $lang_id, $text_ids);
            $stats['a_total'][$corpus_id] = $a_total;
            $stats['a_to_all'][$corpus_id] = round(100*$a_total/$word_total, 2);
            $stats['a_to_linked'][$corpus_id] = round(100*$a_total/$linked_words, 2);
            
            $ni_total = BibleLanguage::niTotal($corpus_id, $lang_id, $text_ids);
            $stats['ni_total'][$corpus_id] = $ni_total;
            $stats['ni_to_all'][$corpus_id] = round(100*$ni_total/$word_total, 2);
            $stats['ni_to_linked'][$corpus_id] = round(100*$ni_total/$linked_words, 2);
            
            $no_total = BibleLanguage::noTotal($corpus_id, $lang_id, $text_ids);
            $stats['no_total'][$corpus_id] = $no_total;
            $stats['no_to_all'][$corpus_id] = round(100*$no_total/$word_total, 2);
            $stats['no_to_linked'][$corpus_id] = round(100*$no_total/$linked_words, 2);
            
            $voi_total = BibleLanguage::voiTotal($corpus_id, $lang_id, $text_ids);
            $stats['voi_total'][$corpus_id] = $voi_total;
            $stats['voi_to_all'][$corpus_id] = round(100*$voi_total/$word_total, 2);
            $stats['voi_to_linked'][$corpus_id] = round(100*$voi_total/$linked_words, 2);
        }
        return view('experiments.bible_language.for_all',
                compact('corpuses', 'for_selection', 'lang_id', 'stats'));
    }
    
    public function forSelection(int $corpus_id) {
        $text_ids = BibleLanguage::getTextSelection($corpus_id);
        if (!$text_ids) {
                return;
        }
        $texts = Text::whereIn('id', $text_ids)->get();
        
        list($grams, $words) = BibleLanguage::researchFormsforTexts($text_ids);
        
        list($a_words, $ni_words, $no_words, $tap_words, $ta_positions, $voi_words) 
                = BibleLanguage::researchServiceWordsforTexts($text_ids);
                
        return view('experiments.bible_language.for_selection', 
                compact('a_words', 'grams', 'ni_words', 'no_words', 'tap_words', 
                        'ta_positions', 'texts', 'voi_words', 'words'));
    }
    
}
