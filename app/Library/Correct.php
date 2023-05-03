<?php

namespace App\Library;

use App\Models\Dict\Concept;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\LemmaWordform;
use App\Models\Dict\PartOfSpeech;

use App\Models\Corpus\Word;

class Correct
{
    /**
     * select all lemmas having wordforms without affixes;
     * select id from lemmas where lang_id=1 and id in (select lemma_id from lemma_wordform where affix is NULL and gramset_id is not NULL);
     * select id from lemmas where lang_id=1 and id in (select lemma_id from lemma_wordform where affix is NULL and gramset_id is not NULL and wordform_id in (select id from wordforms where wordform not like '% %'));
     * 
     * @param INT $lang_id
     */
    public static function addWordformAffixesForLang($lang_id) {
        $lemmas = Lemma::where('lang_id',$lang_id)
                       ->whereIn('id',function($q) use ($lang_id){
                            $q->select('lemma_id')->from('lemma_wordform')
                              ->whereNull('affix')->whereNotNull('gramset_id')
                              ->whereIn('wordform_id',function($query){
                                    $query->select('id')->from('wordforms')
                                      ->where('wordform','NOT LIKE','% %');
                               });
                       })
                       ->orderBy('id')//->take(10)
//                       ->whereId(17)        
                       ->get(); 
        foreach ($lemmas as $lemma) {
            if (!$lemma->updateWordformAffixes()) {
print '<p><a href="/dict/lemma/'.$lemma->id.'">'.$lemma->lemma.'</a> - WRONG STEM</p>';                
            }
print "<p>".$lemma->lemma."</p>";                 
        }
    }    
    
    /**
     * Обойти все леммы, посчитать количество словоформ и записать в поле lemmas.wordform_total
     */
    public static function calculateLemmaWordforms() {
        $is_all_checked = false;
        while (!$is_all_checked) {        
            $lemmas = Lemma::whereNull('wordform_total')
                    ->take(10);
            if ($lemmas->count()) {               
                foreach ($lemmas->get() as $lemma) {
                    $lemma->wordform_total = LemmaWordform::whereLemmaId($lemma->id)->count();
                    $lemma->save();
                }
            } else {
                $is_all_checked = true;
            }
        }        
    }
    
    /**
     * Создать массив пар <количество_словоформ у леммы> - <количество лемм с таким числом словоформ>
     * select lemma_id, count(*) as count from lemma_wordform group by lemma_id order by ;
     */
    public static function countLemmaWordforms($lang_id) {
        $counts = [];
        $lemma_counts = Lemma::whereLangId($lang_id)
                             ->whereIn('pos_id', PartOfSpeech::changeablePOSIdList())
                             ->groupBy('wordform_total','pos_id')
                             ->selectRaw('pos_id, wordform_total, count(*) as count')
                             ->orderBy('wordform_total')
                             ->get();
//dd($lemma_counts);        
        foreach ($lemma_counts as $count) {
            $counts[$count->pos_id][$count->wordform_total] = $count->count;
        }
        return $counts;
    }
    
    public static function generateWordforms($lang_id, $pos_code, $w_count) {
        if ($lang_id == 5) {
            $dialect_id=44;
        } else {return;}
        
        if ($pos_code != 'NOUN') {
            $pos_code = 'ADJ';
        }
        $pos = PartOfSpeech::getByCode($pos_code);
        $pos_id = $pos->id;
        $right_counts = [4=>37, 5=>55];
        
        $is_all_checked = false;
        while (!$is_all_checked) {        
            $lemmas = Lemma::lemmasWithWordformsByCount($lang_id, $pos_id, $w_count)
                    ->take(10)
                    ->orderBy('lemma');
//  ->count();
//dd($lemmas);   
            if ($lemmas->count()) {               
                foreach ($lemmas->get() as $lemma) {
                    $lemma->reloadWordforms($dialect_id, true);
                    $w_count_res = $lemma->countWordformsByDialect($dialect_id);                
                    print "<p><a href=\"/dict/lemma/".$lemma->id."\">".$lemma->lemma."</a> ".$w_count."->".$w_count_res."</p>";    
                    if ($w_count_res != $right_counts[$w_count]) {
                        dd("INCORRECT WORDFORMS' COUNT!!!");
                    }
                }
            } else {
                $is_all_checked = true;
            }
        }
        print "<p>done.</p>\n";
    }
    
    public static function moveCharOutWord($char) {
        $words = Word::where('word', 'like', $char.'%')->get();
//dd($words);        
        foreach ($words as $word) {
//dd($word);            
            $word->moveCharOut($char);
        }
    }
    
    public static function addSynonyms() {
        $concepts = Concept::all();
        foreach ($concepts as $concept) {
print $concept->text.'<ul>';            
            foreach (Lang::projectLangs() as $lang) {
print $lang->code.'<ul>';                
                $meanings = $concept->meanings()->whereIn('lemma_id', function ($q) use ($lang) {
                    $q->select('id')->from('lemmas')->whereLangId($lang->id);
                })->get();
                $lemmas = [];
                foreach ($meanings as $meaning) {
print '<li><a href="/ru/dict/lemma/'.$meaning->lemma_id.'">'.$meaning->lemma->lemma.'</a></li>';     
                    $lemmas[$meaning->id] = $meaning->lemma;
                }
                foreach ($lemmas as $meaning1 => $lemma1) {
                    foreach ($lemmas as $meaning2 => $lemma2) {
                        if ($lemma1->id == $lemma2->id) {
                            continue;
                        }
                        if ($lemma1->variants()->whereLemma2Id($lemma2->id)->count()) {
                            continue;
                        }
print '<p><a href="/ru/dict/lemma/'.$lemma1->id.'">'.$lemma1->lemma.'</a> - СИНОНИМ - <a href="/ru/dict/lemma/'.$lemma2->id.'">'.$lemma2->lemma.'</a>';                        
                    }
                }
print "</ul>";                
            }
print "</ul>";            
//exit(0);            
        }
    }
    
    public static function addSrcForConcepts() {
        $concepts = Concept::whereNotNull('wiki_photo')
                           ->where('wiki_photo', '<>', '')
                           ->where(function($q) {
                               $q->whereNull('src')
                                 ->orWhereNull('src');
                           })->get();
        foreach ($concepts as $concept) {
            $concept->updateWikiSrc();
        }
    }
}
