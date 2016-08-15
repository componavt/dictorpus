<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class PartOfSpeech extends Model
{
    protected $table = 'parts_of_speech';
    
    public $timestamps = false;
    
    
    // PartOfSpeech __has_many__ Lemma
    public function lemmas()
    {
        return $this->hasMany(Lemma::class);
    }
    
}
