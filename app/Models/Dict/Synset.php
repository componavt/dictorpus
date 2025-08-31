<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

use App\Models\Dict\Meaning;

class Synset extends Model
{
    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = ['lang_id','pos_id', 'status', 'comment'];

    const RELATION_FULL = 7;
    const RELATION_NEAR = 11;

    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Lang;
    use \App\Traits\Relations\BelongsTo\POS;

        public function meanings(){
        return $this->belongsToMany(Meaning::class)
                ->withPivot('syntype_id');
    }

    public static function newSetFounded($lang_id, $pos_id) {
        return Meaning::whereIn('lemma_id', function ($q) use ($lang_id, $pos_id) {
                $q->select('id')->from('lemmas')
                    ->whereLangId($lang_id)
                    ->wherePosId($pos_id);
                })->whereIn('id', function($q) { // полные или частичные синонимы
                    $q->select('meaning1_id')->from('meaning_relation')
                   ->whereIn('relation_id', [self::RELATION_FULL, self::RELATION_NEAR]);
                })->whereNotIn('id', function($q) {        // не в синсетах
                    $q->select('meaning_id')->from('meaning_synset');
                })
                ->count() > 1;                 
    }

    public static function findSynset($lang_id, $pos_id) {
        $first_meaning = Meaning::join('lemmas', 'lemmas.id', '=', 'meanings.lemma_id')
                ->whereLangId($lang_id)
                ->wherePosId($pos_id)
                ->whereIn('meanings.id', function($q) { // полные синонимы
                    $q->select('meaning1_id')->from('meaning_relation')
                      ->whereRelationId(self::RELATION_FULL);
                })->whereNotIn('meanings.id', function($q) {        // не в синсетах
                    $q->select('meaning_id')->from('meaning_synset');
                })
                ->select('meanings.*', 'lemmas.lemma')
                ->orderBy('lemma')->with('meaningTexts')->first();
                 
        return self::findSet($lang_id, $pos_id, $first_meaning->id, [$first_meaning->id=>['meaning'=>$first_meaning, 'type'=>Synset::RELATION_FULL]]);
    }
    
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
                ->orderBy('lemma')->with('meaningTexts')->get();

        foreach ($meanings as $meaning) {
            if (empty($set[$meaning->id])) {
                $set[$meaning->id] = ['meaning'=>$meaning, 'type'=>$meaning->relation_id];
                $set = $set + self::findSet($lang_id, $pos_id, $meaning->id, $set);
            }
        }
        return $set;
    }
    
}
