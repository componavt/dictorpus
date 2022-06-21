<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class Example extends Model
{
    protected $fillable = ['meaning_id','example','example_ru'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.
    protected $revisionFormattedFields = array(
        'updated_at' => 'datetime:m/d/Y g:i A'
    );
    
    /**
    * Атрибуты, которые должны быть преобразованы к датам.
    *
    * @var array
    */
    protected $dates = [
        'created_at',
        'updated_at',
    ];    
    public static function boot()
    {
        parent::boot();
    }
}
