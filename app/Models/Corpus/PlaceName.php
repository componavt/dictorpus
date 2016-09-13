<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use App\Models\Dict\Lang;
use App\Models\Corpus\Place;


class PlaceName extends Model
{
    public $timestamps = false;
    protected $fillable = ['place_id','lang_id','name'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    /** Gets lang, takes into account locale.
     * 
     * PlaceName belongs_to Lang
     * 
     * @return Relationship, Query Builder
     */
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }    
    
    /** Gets place, takes into account locale.
     * 
     * PlaceName belongs_to Place
     * 
     * @return Relationship, Query Builder
     */
    public function place()
    {
        return $this->belongsTo(Place::class);
    }    
}
