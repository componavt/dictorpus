<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['text_id', 'youtube_id'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }
    
    // Dialect __belongs_to__ Lang
    public function text()
    {
        return $this->belongsTo(Text::class);
    }    

}
