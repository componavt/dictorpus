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
    
    // Gramset __belongs_to__ Gram
    public function gramNumber()
    {
        return $this->belongsTo(Gram::class, 'gram_id_number');
    }
    
    public function gramCase()
    {
        return $this->belongsTo(Gram::class, 'gram_id_case');
    }
    
    public function gramTense()
    {
        return $this->belongsTo(Gram::class, 'gram_id_tense');
    }
    

    public function gramsetList()
    {
        $list = array();
        if ($this->gram_id_number){
            $list[] = $this->gramNumber->name_short;
        }
            
        if ($this->gram_id_case){
            $list[] = $this->gramCase->name_short;
        }
            
        if ($this->gram_id_tense){
            $list[] = $this->gramTense->name_short;
        }
            
        return join(', ', $list);
    }

}
