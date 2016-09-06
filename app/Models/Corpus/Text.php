<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Models\Corpus\Corpus;
use App\Models\Corpus\Event;
use App\Models\Dict\Lang;
use App\Models\Corpus\Source;
use App\Models\Corpus\Transtext;

class Text extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    protected $fillable = ['corpus_id','lang_id','source_id','event_id','title','text'];
    
    // Text __belongs_to__ Lang
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }    
    
    // Text __belongs_to__ Corpus
    public function corpus()
    {
        return $this->belongsTo(Corpus::class);
    }    
    
    // Text __belongs_to__ Event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }    
    
    // Text __belongs_to__ Source
    public function source()
    {
        return $this->belongsTo(Source::class);
    }    
    
    // Text __belongs_to__ Transtext
    public function transtext()
    {
        return $this->belongsTo(Transtext::class);
    }    
}
