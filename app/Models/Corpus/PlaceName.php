<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

class PlaceName extends Model
{
    public $timestamps = false;
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }

    /** Gets lang, takes into account locale.
     * 
     * Corpus belongs_to Lang
     * 
     * @return Relationship, Query Builder
     */
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }    
    
    /** Gets place, takes into account locale.
     * 
     * Corpus belongs_to Lang
     * 
     * @return Relationship, Query Builder
     */
    public function place()
    {
        return $this->belongsTo(Place::class);
    }    
}
