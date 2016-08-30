<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class MeaningText extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }

    protected $fillable = ['lang_id','meaning_id','meaning_text'];

    // MeaningText __belongs_to__ Meaning
    public function meaning()
    {
        return $this->belongsTo(Meaning::class);
    }
    
    // MeaningText __belongs_to__ Lang
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }
}
