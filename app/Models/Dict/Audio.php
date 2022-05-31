<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

use App\Models\Dict\Lemma;

class Audio extends Model
{
    protected $table = 'audios';
    protected $fillable = ['filename', 'informant_id'];
    const DISK = 'audios';
    const DIR = 'audio/lemmas/';
    
    public $timestamps = false;
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }
    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Informant;
    
    // Belongs To Many Relations
    public function lemmas(){
        return $this->belongsToMany(Lemma::class);
    }   
    
}
