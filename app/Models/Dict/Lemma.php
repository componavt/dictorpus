<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class Lemma extends Model
{
    
    // Lemma __belongs_to__ Lang
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }
    
    
    // Lemma __belongs_to__ PartOfSpeech
    public function lang()
    {
        return $this->belongsTo(PartOfSpeech::class);
    }
    
    // Lemma __belongs_to_many__ Meanings
    /*public function meanings()
    {
    return $this->belongsToMany(Meaning::class);
    }*/
    
}
