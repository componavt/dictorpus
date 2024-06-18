<?php namespace App\Traits;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gram;
use App\Models\Dict\Gramset;
use App\Models\Dict\GramsetCategory;

trait LemmaSelect
{
    /**
     * Wordforms array for one dialect.
     * Output in a table:
     * 
     * |           | единственное | множественное |
     * | номинатив |              |               |
     * 
     * or
     * 
     * |               | положительные | отрицательные |
     * | Индикатив, презенс
     * | 1 л., ед. ч.  |               |               |
     * 
     * @param int $dialect_id -- ID of dialect
     */
    public function wordformsForTable(int $dialect_id, $without_empty=false) {
        if ($this->pos->isName()) {
            $wordforms = $this->wordformsForTableName($dialect_id, $without_empty);
        } elseif ($this->pos->isVerb()) {
            $wordforms = $this->wordformsForTableVerb($dialect_id, $without_empty);
        }
//dd($wordforms);        
        return $wordforms;
    }

    public function wordformsForTableName(int $dialect_id, $without_empty=false) {
        $lang_id = Dialect::getLangIDByID($dialect_id);
        $numbers = Gram::getByCategory(2);
        $wordforms = [];
        $cases = Gram::getByCategory(1);
        foreach ($cases as $case) {
            foreach ($numbers as $number) {
                $gramset = Gramset::gramsetsLangPOS($lang_id, $this->pos_id)
                                  ->whereGramIdCase($case->id)
                                  ->whereGramIdNumber($number->id)
                                  ->first();
                if (!$gramset) { 
                    continue;                         
                }
                $wordforms[$case->name][$number->id]
                        =$this->wordform($gramset->id, $dialect_id);
            }
        }
        return $wordforms;
    }
    
    public function wordformsForTableVerb(int $dialect_id, $without_empty=false) {
        $lang_id = Dialect::getLangIDByID($dialect_id);
        $numbers = Gram::getByCategory(2);
        $wordforms = [];
//      $gramsets = Gramset::getGroupedList($this->pos_id, $lang_id);
//dd($gramsets);            
        $negations = Gram::getByCategory(6);
        foreach (Gram::getByCategory(5) as $mood) {
            foreach (Gram::getByCategory(3) as $tense) {
                foreach ($numbers as $number) {
                    foreach (Gram::getByCategory(4) as $person) {
                        foreach ($negations as $negation) {
                            $gramset = Gramset::gramsetsLangPOS($lang_id, $this->pos_id)
                                              ->whereGramIdMood($mood->id)
                                              ->whereGramIdTense($tense->id)
                                              ->whereGramIdPerson($person->id)
                                              ->whereGramIdNumber($number->id)
                                              ->whereGramIdNegation($negation->id)
                                              ->first();
                            if (!$gramset) { 
                                continue;                         
                            }
                            if (!$without_empty || $without_empty && $this->wordform($gramset->id, $dialect_id)) {
                                $wordforms[$mood->name. ', ' .$tense->name][$person->short_name. ', '. $number->short_name][$negation->id]
                                    =$this->wordform($gramset->id, $dialect_id);
                            }
                        }
                    }
                }                
            }
        }
        $infinite_category_id = 26;
        $gramsets = Gramset::gramsetsLangPOS($lang_id, $this->pos_id)
                  ->where('gramset_category_id', $infinite_category_id)->get();
        foreach ($gramsets as $gramset) {
            if (!$without_empty || $without_empty && $this->wordform($gramset->id, $dialect_id)) {
              $wordforms[GramsetCategory::getNameById($infinite_category_id)][$gramset->inCategoryString()] 
                      = $this->wordform($gramset->id, $dialect_id);
            }
        }
        return $wordforms;
    }
    
    public function hasEssentialWordforms($without_dialect=null){
        $wordforms = $this->wordforms()
                    ->wherePivot('gramset_id', '<>', 1) // nomSg
                    ->wherePivot('gramset_id', '<>', 56) // accSg
                    ->wherePivot('gramset_id', '<>', 170); // Inf
        
        if (!empty($without_dialect)) {
            $wordforms->wherePivot('dialect_id', '<>', $without_dialect);
        }
        return $wordforms->count();
    }
    
    public function hasEssentialDialectWordforms($dialect_id){
        return $this->wordforms()->orderBy('wordform')
                    ->wherePivot('dialect_id',$dialect_id)
                    ->wherePivot('gramset_id', '<>', 1) // nomSg
                    ->wherePivot('gramset_id', '<>', 56) // accSg
                    ->wherePivot('gramset_id', '<>', 170) // Inf
                    ->count();
    }
        
}