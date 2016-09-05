<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Models\Corpus\Place;
use App\Models\Corpus\Informant;

class Event extends Model
{
    public $timestamps = false;
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }
    
    /** 
     * Event belongs_to Informant
     * 
     * @return Relationship, Query Builder
     */
    public function informant()
    {
        return $this->belongsTo(Informant::class);
    }    
    
    /** 
     * Event belongs_to Place
     * 
     * @return Relationship, Query Builder
     */
    public function place()
    {
        return $this->belongsTo(Place::class);
    }    
}
