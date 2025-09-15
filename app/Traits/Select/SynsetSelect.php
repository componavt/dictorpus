<?php namespace App\Traits\Select;

use DB;

use App\Models\Dict\Lang;
use App\Models\Synset;
use App\Models\Dict\Meaning;

trait SynsetSelect
{
    /* return true if a new synset founded */
    public static function newSetFounded($lang_id, $pos_id=null) {
        return Meaning::whereIn('lemma_id', function ($q) use ($lang_id, $pos_id) {
                $q->select('id')->from('lemmas')
                    ->whereLangId($lang_id);
                if (!empty($pos_id)) {
                    $q->wherePosId($pos_id);
                }
                })->whereIn('id', function($q) { // полные или частичные синонимы
                    $q->select('meaning1_id')->from('meaning_relation')
                   ->whereIn('relation_id', [self::RELATION_FULL, self::RELATION_NEAR]);
                })->whereNotIn('id', function($q) {        // не в синсетах
                    $q->select('meaning_id')->from('meaning_synset');
                })
                ->count() > 1;                 
    }

    /* find all possible synsets */
    public static function findSynsets($lang_id, $pos_id=null) {
        $sets = [];
        $except = [];
        $count=1;
        do {
            $new_set = self::findSynset($lang_id, $pos_id, $except);
            if (!empty($new_set)) {
                $except = array_merge($except, array_keys($new_set));
                $firstValue = $new_set[array_key_first($new_set)];
                $sets[$count]['pos_name'] = $firstValue['meaning']->lemma->pos->name;
                $new_set = collect($new_set);
                $sets[$count]['core'] = $new_set->where('type', self::RELATION_FULL);
                $sets[$count++]['periphery'] = $new_set->where('type', self::RELATION_NEAR);
            }
        } while (!empty($new_set)); 
        return $sets;
    }

    public static function findSynset($lang_id, $pos_id=null, $except=[]) {
        $first_meaning = Meaning::join('lemmas', 'lemmas.id', '=', 'meanings.lemma_id')                
                ->whereLangId($lang_id)
                ->whereIn('meanings.id', function($q) { // полные синонимы
                    $q->select('meaning1_id')->from('meaning_relation')
                      ->whereRelationId(self::RELATION_FULL);
                })->whereNotIn('meanings.id', function($q) {        // не в синсетах
                    $q->select('meaning_id')->from('meaning_synset');
                });
        if (!empty($except)) {
            $first_meaning->whereNotIn('meanings.id', $except);
        }        
        if (!empty($pos_id)) {
            $first_meaning->wherePosId($pos_id);
        }        
        $first_meaning = $first_meaning->select('meanings.*')
                ->orderBy('lemma')->with('meaningTexts')->with('lemma')->first();
        
        if (!$first_meaning) {
            return null;
        }
        if (empty($pos_id)) {
            $pos_id = $first_meaning->lemma->pos_id;
        }

        return self::findSet($lang_id, $pos_id, $first_meaning->id, 
                [$first_meaning->id=>['meaning'=>$first_meaning, 'type'=>self::RELATION_FULL]]);
    }
    
    public static function findSet($lang_id, $pos_id, $first_id, $set=[], $except=[]) {        
        $meanings = Meaning::join('lemmas', 'lemmas.id', '=', 'meanings.lemma_id')
                ->join('meaning_relation', 'meaning1_id', '=', 'meanings.id')
                ->whereNotIn('meanings.id', array_keys($set)+$except)
                ->whereLangId($lang_id)
                ->wherePosId($pos_id)
                ->whereMeaning2Id($first_id)
                ->whereIn('relation_id', [self::RELATION_FULL, self::RELATION_NEAR])
                ->whereNotIn('meanings.id', function($q) {        // не в синсетах
                    $q->select('meaning_id')->from('meaning_synset');
                })
                ->select('meanings.*', 'meaning_relation.relation_id')
                ->orderBy('lemma')->with('meaningTexts')->with('lemma')->get();

        foreach ($meanings as $meaning) {
            if (empty($set[$meaning->id])) {
                $set[$meaning->id] = ['meaning'=>$meaning, 'type'=>$meaning->relation_id];
                $set = $set + self::findSet($lang_id, $pos_id, $meaning->id, $set);
            }
        }
        return $set;
    }
    
    /* Найти для синсета Meanings, близкие по значению, кандидатов на включение
     * 
     */ 
    public function searchPotentialMembers($comment='', $without = []) {
        $terms = split_definition(empty($comment) ? $this->comment : $comment);
        $lang = Lang::where('code','ru')->first();
        
        foreach ($this->meanings as $meaning) {
            $meaning_text = $meaning->meaningTexts()->whereLangId($lang->id)->first();
            $terms = array_merge($terms, split_definition($meaning_text->meaning_text));
        }
        
        $terms = join(' ',array_unique($terms));

        $excludedMeaningIds = array_merge($without, $this->meanings()->pluck('id')->toArray());

        return self::searchRelevantMeanings($terms, $this->lang_id, $this->pos_id, $excludedMeaningIds);            
    }
    
    /* Найти близкие по значению слова
     * 
     */
    public static function searchRelevantMeanings($terms, $lang_id, $pos_id, $excludedMeaningIds) {
        $terms = self::removeStopWords($terms);
//dd($terms);        

        $lang = Lang::where('code','ru')->first();
        return Meaning::select('meanings.*')
            ->join('meaning_texts', 'meaning_texts.meaning_id', '=', 'meanings.id')
            ->selectRaw('MATCH(meaning_texts.meaning_text) AGAINST(? IN NATURAL LANGUAGE MODE) AS score', [$terms])
            ->whereRaw('MATCH(meaning_texts.meaning_text) AGAINST(? IN NATURAL LANGUAGE MODE)', [$terms])
            ->where('meaning_texts.lang_id', $lang->id)
            ->whereNotIn('meanings.id', $excludedMeaningIds)
            ->whereIn('meanings.lemma_id', function($query) use ($lang_id, $pos_id) {
                $query->select('id')->from('lemmas')
                    ->where('lang_id', $lang_id)
                    ->where('pos_id', $pos_id);
            })
            ->orderBy('score', 'desc')
            ->limit(20)
            ->with('meaningTexts')
            ->get();            
    }
}