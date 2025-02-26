<?php namespace App\Traits\Select;

use \Venturecraft\Revisionable\Revision;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gram;
use App\Models\Dict\Gramset;
use App\Models\Dict\GramsetCategory;
use App\Models\User;

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
        
    public function toUniMorph($dialect_id) {
        $pos_id = $this->pos_id;
        
        if (!in_array($pos_id, PartOfSpeech::getNameIDs()) && $pos_id != PartOfSpeech::getVerbID()) {
            return false;
        } 
        $pos = PartOfSpeech::find($pos_id);
        if (!$pos) { return false; }
        
        $pos_code = $pos->unimorph;
        if ($pos_code == 'V' && $this->features && $this->features->reflexive) {
            $pos_code .= ';REFL';
        }
//dd($this->wordforms);              
        $wordforms = $this->wordforms()->wherePivot('dialect_id',$dialect_id)->get();//wordformsWithGramsets();
//dd($dialect_id, $wordforms);
        if (!$wordforms) { return false; }
        $lines = [];
        foreach ($wordforms as $wordform) {
            $gramset=$wordform->gramsetPivot();
            if (!$gramset) { continue; }
            $features = $gramset->toUniMorph($pos_code);
            if (!$features) { continue; }
            $lines[] = $this->lemma."\t".$wordform->wordform."\t".$features;
        }
        return join("\n", $lines);
    }
    
    public function compoundToUniMorph() {
        if ($this->features && $this->features->comptype_id) {
            $comptype = $this->features->comptype_id;
        } else {
            $comptype = '';
        }
        $lemmas = $this->phraseLemmas;
        if (!$lemmas) { return false; }
        $tmp = [];
        foreach ($lemmas as $lemma) {
            $tmp[] = $lemma->lemma;
        }
        return $this->lemma. "\t$comptype\t". join(";", $tmp);
    }
 
    public static function lastCreated($limit='') {
        $lemmas = self::latest();
        if ($limit) {
            $lemmas = $lemmas->take($limit);
        }
        $lemmas = $lemmas->get();
        foreach ($lemmas as $lemma) {
            $revision = Revision::where('revisionable_type','like','%Lemma')
                                ->where('key','created_at')
                                ->where('revisionable_id',$lemma->id)
                                ->latest()->first();
            if ($revision) {
                $lemma->user = User::getNameByID($revision->user_id);
            }
        }
        return $lemmas;
    }
    
    public static function lastUpdated($limit='',$is_grouped=0) {
        $revisions = Revision::where('revisionable_type','like','%Lemma')
                            ->where('key','updated_at')
                            ->groupBy('revisionable_id')
                            ->latest()->take($limit)->get();
        $lemmas = [];
        foreach ($revisions as $revision) {
            $lemma = self::find($revision->revisionable_id);
            if ($lemma) {
                $lemma->user = User::getNameByID($revision->user_id);
                if ($is_grouped) {
                    $updated_date = $lemma->updated_at->formatLocalized(trans('main.date_format'));            
                    $lemmas[$updated_date][] = $lemma;
                } else {
                    $lemmas[] = $lemma;
                }
            }
        }
        
        return $lemmas;
    }
    
    public function allHistory() {
        $all_history = $this->revisionHistory->filter(function ($item) {
                            return $item['key'] != 'updated_at'
                                 && !($item['key'] == 'reflexive' && $item['old_value'] == null && $item['new_value'] == 0);
                        });
        foreach ($all_history as $history) {
            $history->what_created = trans('history.lemma_accusative');
        }
        foreach ($this->meanings as $meaning) {
            foreach ($meaning->revisionHistory as $history) {
                $history->what_created = trans('history.meaning_accusative', ['num'=>$meaning->meaning_n]);
            }
            $all_history = $all_history -> merge($meaning->revisionHistory);
            foreach($meaning->meaningTexts as $meaning_text) {
               foreach ($meaning_text->revisionHistory as $history) {
                   $lang = $meaning_text->lang->name;
                   $fieldName = $history->fieldName();
                   $history->field_name = trans('history.'.$fieldName.'_accusative'). ' '
                           . trans('history.meaning_genetiv',['num'=>$meaning->meaning_n])
                           . " ($lang)";
               }
               $all_history = $all_history -> merge($meaning_text->revisionHistory);
            }
        }
         
        $all_history = $all_history->sortByDesc('id')
                      ->groupBy(function ($item, $key) {
                            return (string)$item['updated_at'];
                        });
//dd($all_history);                        
        return $all_history;
    }
    
    
}