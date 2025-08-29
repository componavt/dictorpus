<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class Synset extends Model
{
    const RELATION_FULL = 7;
    const RELATION_NEAR = 11;
    
    public static function findSet($lang_id, $pos_id, $first_id, $set=[]) {
        
        $meanings = Meaning::join('lemmas', 'lemmas.id', '=', 'meanings.lemma_id')
                ->join('meaning_relation', 'meaning1_id', '=', 'meanings.id')
                ->whereNotIn('meanings.id', array_keys($set))
                ->whereLangId($lang_id)
                ->wherePosId($pos_id)
                ->whereMeaning2Id($first_id)
                ->whereIn('relation_id', [self::RELATION_FULL, self::RELATION_NEAR])
                ->whereNotIn('meanings.id', function($q) {        // не в синсетах
                    $q->select('meaning_id')->from('meaning_synset');
                })
                ->select('meanings.*', 'lemmas.lemma', 'meaning_relation.relation_id')
                ->orderBy('lemma')->get();

        foreach ($meanings as $meaning) {
            if (empty($set[$meaning->id])) {
                $set[$meaning->id] = [$meaning, $meaning->relation_id];
                $set = $set + self::findSet($lang_id, $pos_id, $meaning->id, $set);
            }
        }
        return $set;
    }
}
