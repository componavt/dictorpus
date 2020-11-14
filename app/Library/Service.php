<?php

namespace App\Library;

//use DB;

use App\Models\Dict\Gramset;
//use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\LemmaFeature;
use App\Models\Dict\LemmaWordform;
use App\Models\Dict\Meaning;
use App\Models\Dict\MeaningText;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Wordform;

class Service
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
     * Choose all lemmas with wordforms having affix=#.
     * Calculate stem and lemma affix by wordforms.
     * Update lemma wordform affixes.
     * 
     * @param int $lang_id
     */
    public static function reloadStemAffixesForLang($lang_id) {
        $lemmas = Lemma::where('lang_id',$lang_id)
                       ->whereIn('id',function($q) use ($lang_id){
                            $q->select('lemma_id')->from('lemma_wordform')
                              ->whereAffix('#');
                       })
                       ->orderBy('id')//->take(1)
//                       ->whereId(17)        
                       ->get(); 
        foreach ($lemmas as $lemma) {
            list($max_stem, $affix) = $lemma->getStemAffixByWordforms();
//dd($max_stem, $affix, $lemma->reverseLemma);            
            if ($max_stem!=$lemma->reverseLemma->stem || $affix!=$lemma->reverseLemma->affix) {
                $lemma->reverseLemma->stem = $max_stem;
                $lemma->reverseLemma->affix = $affix;
                $lemma->reverseLemma->save();
            }

            $is_success = $lemma->updateWordformAffixes(true);
print '<p><a href="/dict/lemma/'.$lemma->id.'">'.$lemma->stemAffixForm().'</a>';                
            if (!$is_success) { print ' - WRONG STEM'; }
print "</p>";                 
        }
    }
    
    public static function checkWordformsByRules($lang_id) {
/*        
        $table_name = 'search_gramset';
        
        $wordforms = DB::table($table_name)
                   ->whereLangId($lang_id)
                   ->*/
        $gramsets = LemmaWordform::join('lemmas', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                         ->whereLangId($lang_id)
                         ->whereNotNull('gramset_id')
                         ->select('gramset_id')
                         ->groupBy('gramset_id')
                         ->get();
        
        foreach ($gramsets as $gramset) {
            $affixes = Grammatic::getAffixesForGramset($gramset->gramset_id, $lang_id);
            if (!sizeof($affixes)) {
                continue;
            }
            $query = [];
            foreach($affixes as $affix) {
                $query[] = "wordform like '%".$affix."'";
            }
            $query = "!(".join(" OR ", $query).")";
            $wordforms = Wordform::join('lemma_wordform', 'wordforms.id', '=', 'lemma_wordform.wordform_id')
                         ->join('lemmas', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                         ->whereLangId($lang_id)
                         ->whereGramsetId($gramset->gramset_id)
                         ->whereRaw($query)
                         ->groupBy('wordform','gramset_id')
                         ->get();
 //dd($wordforms->toSql());       
            if (!$wordforms || !sizeof($wordforms)) {
                continue;
            }
            print "<h2>".Gramset::getStringByID($gramset->gramset_id)."</h2>\n";
            foreach ($wordforms as $wordform) {
                if (preg_match("/^([^\-]+)\-/", $wordform->wordform, $regs)) {
                    if (preg_match("/[".join('$|',$affixes)."$]/",$regs[1])) {
                        continue;
                    }
                }
                print "<p><a href=\"/dict/wordform?search_gramset=".$gramset->gramset_id."&search_lang=".$lang_id."&search_wordform=".$wordform->wordform."\">".$wordform->wordform."</a>, ". PartOfSpeech::getNameById($wordform->pos_id)."</p>\n";
            }
        }
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
    
    public static function copyLemmas($lang_to, $lemmas, $new_lemmas, $dialect_id) {
        if (!is_array($lemmas) || !sizeof($lemmas)) {
            return;
        }
        
        $added_lemmas = [];
        foreach ($lemmas as $lemma_id) {
            $lemma = Lemma::find($lemma_id);
            $new_lemma = self::copyLemma($lang_to, $lemma, $new_lemmas[$lemma_id], $dialect_id);
            if ($new_lemma) {
                $added_lemmas[] = $new_lemma;
            } 
//print "<p>".$new_lemma->id."</p>";            
        }
        return $added_lemmas;
    }
    
    public static function copyLemma($lang_to, $lemma, $lemma_field, $dialect_id) {
        if (!$lemma) { return; }
        
        $data = ['lemma'=>$lemma_field, 'lang_id'=>$lang_to, 'pos_id'=>$lemma->pos_id, 'wordform_dialect_id'=>$dialect_id];
        list($new_lemma_str, $wordforms_list, $stem, $affix, $gramset_wordforms, $stems) 
                = Grammatic::parseLemmaField($data);
        
        $new_lemma_obj = Lemma::create([
            'lemma' => $new_lemma_str,
            'lemma_for_search' => Grammatic::toSearchForm($new_lemma_str),
            'pos_id' => $lemma->pos_id,
            'lang_id' => $lang_to
        ]);
        if (!$new_lemma_obj) { return; }

        $lemma_feature = LemmaFeature::find($lemma->id);
        $features = [];
        if ($lemma_feature) {
            foreach ($lemma_feature->getFillable() as $field) {
                $features[$field] = $lemma_feature->$field;
            }
        }
        $new_lemma_obj->storeAddition($wordforms_list, $stem, $affix, $gramset_wordforms, $features, $dialect_id, $stems);           
        
        foreach ($lemma->meanings as $meaning) {
            $new_meaning = self::copyMeaning($meaning, $new_lemma_obj);
        }
        
        $new_lemma_obj->updateTextLinks();
        return $new_lemma_obj;
    }
    
    public static function copyMeaning($meaning, $new_lemma) {
        if (!$meaning) { return; }
        
        $new_meaning = Meaning::create([
            'lemma_id' => $new_lemma->id,
            'meaning_n' => $meaning->meaning_n]);
        if (!$new_meaning) { return; }
        
        foreach ($meaning->meaningTexts as $meaningText) {
            MeaningText::create([
                'lang_id' => $meaningText->lang_id,
                'meaning_id' => $new_meaning->id,
                'meaning_text' => $meaningText->meaning_text]);
        }
        
        $new_meaning->translations()->attach($meaning->lemma->lang_id, ['meaning2_id'=>$meaning->id]);
        $meaning->translations()->attach($new_lemma->lang_id, ['meaning2_id'=>$new_meaning->id]);
    }
    
}
