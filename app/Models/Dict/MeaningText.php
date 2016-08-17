<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class MeaningText extends Model
{
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
