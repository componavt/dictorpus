<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class Lang extends Model
{
    //
    
    public $timestamps = false;
    
    
    // Lang __has_many__ Lemma
    public function lemmas()
    {
        return $this->hasMany(Lemma::class);
    }
}
