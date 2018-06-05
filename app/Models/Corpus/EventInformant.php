<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

class EventInformant extends Model
{
    protected $fillable = ['informant_id','event_id'];
    public $timestamps = false;
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.
    protected $revisionFormattedFields = array(
//        'name_ru'  => 'string:<strong>%s</strong>',
    );
    protected $revisionFormattedFieldNames = array(
//        'title' => 'Title',
//        'small_name' => 'Nickname',
//        'deleted_at' => 'Deleted At'
    );

    public static function boot()
    {
        parent::boot();
    }
    
    /** 
     * Event belongs_to Informant
     * 
     * @return Relationship, Query Builder
     */
    public function informants()
    {
        return $this->belongsToMany(Informant::class);
    }    
    
    
}
