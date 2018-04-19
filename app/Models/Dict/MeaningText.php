<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class MeaningText extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = false; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = ['lang_id','meaning_id','meaning_text'];

    // MeaningText __belongs_to__ Meaning
    public function meaning()
    {
        return $this->belongsTo(Meaning::class);
    }
    
    // MeaningText __belongs_to__ Lang
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }
}
