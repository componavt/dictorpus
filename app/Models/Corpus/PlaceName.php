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
