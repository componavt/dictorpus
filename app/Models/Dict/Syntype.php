<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

use App\Models\Dict\Label;

class Syntype extends Model
{
    public $timestamps = false;
    protected $fillable = ['name_en', 'name_ru', 'comment'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    const TYPE_FULL = 1;
    const TYPE_PART = 2;
    
    public static function boot()
    {
        parent::boot();
    }
    
    // Methods
    use \App\Traits\Methods\getNameAttribute;

    public function labels()
    {
        return $this->belongsToMany(Label::class);
    }
    public static function getList($full=false)
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        $types = self::orderBy('id')->get();
        
        $list = array();
        foreach ($types as $row) {
            $list[$row->id] = $row->name. ($full ? ': '.$row->comment : '');
        }
        
        return $list;         
    }
}
