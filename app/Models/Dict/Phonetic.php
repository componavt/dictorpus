<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;
use URL;
use LaravelLocalization;

class Phonetic extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = ['lemma_id','phonetic'];

    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Lemma;
    
    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Dialects;
    use \App\Traits\Relations\BelongsToMany\Places;
    
    /**
     * 
     * @param Array $dialects [<dialect1_id>=>[<place1_id>, ...], ...]
     */
    public function updateDialects($dialects) {
        foreach ($dialects as $dialect_id => $places) {
            if (!$this->dialects()->where('dialect_id', $dialect_id)->first()) {            
                $this->dialects()->attach($dialect_id);
            }
            foreach ($places as $place_id) {
                if (!$this->places()->where('place_id', $place_id)->first()) {
                    $this->places()->attach($place_id);
                }
            }
        }
    }
    
    public function remove() {
        $this->dialects()->detach();
        $this->places()->detach();

        $this->delete();
    }
    
}
