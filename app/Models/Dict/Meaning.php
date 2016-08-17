<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class Meaning extends Model
{
    // Meaning __belongs_to__ Lemma
    public function lemma()
    {
        return $this->belongsTo(Lemma::class);
    }
    
        // Meaning __has_many__ MeaningTexts
    public function meaningTexts()
    {
        return $this->hasMany(MeaningText::class);
    }

}
