<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monument extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = false; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 999999; //Stop tracking revisions after 999999 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public $graphics = [
        1 => 'кириллица (устав)',
        2 => 'кириллица (полуустав)',
        3 => 'кириллица (скоропись)',
        4 => 'кириллица (гражданский шрифт)',
        5 => 'кириллица (гражданское письмо)',
        6 => 'латиница'
    ];
    
    public static function boot()
    {
        parent::boot();
    }
    
    public function getGraphicNameAttribute() {
        if (!empty($this->graphic_id) && !empty($this->graphics[$this->graphic_id])) {
            return $this->graphics[$this->graphic_id];
        }
    }
}
