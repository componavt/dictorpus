<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class Gramset extends Model
{
    public $timestamps = false;
    
    // Gramset __belongs_to__ PartOfSpeech
    public function pos()
    {
        return $this->belongsTo(PartOfSpeech::class);
    }
    
    // Gramset __belongs_to__ Dialect
    public function dialect()
    {
        return $this->belongsTo(Dialect::class);
    }
    

}
