<?php namespace App\Traits\Relations\HasMany;

use App\Models\Dict\Phonetic;

trait Phonetics
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function phonetics()
    {
        return $this->hasMany(Phonetic::class);
    }
    
    public function phoneticListToString() {
//dd($this->phonetics);        
        $out = [];
        $out = $this->phonetics->pluck('phonetic')->toArray();
        if (!sizeof($out)) {
            return NULL;
        }
        return join(', ',$out);
    }
    
    public function phoneticListWithDialectsToString() {
//dd($this->phonetics);        
        $out = [];
        foreach ($this->phonetics as $phonetic) {
            $dialects = $phonetic->dialects->pluck('name')->toArray();
            $item = '<i>'. $phonetic->phonetic. '</i> ';
            if (sizeof($dialects)) {
                $item .= ' ('. join(', ',$dialects). ')';
            }
            $out[] = $item;
            
        }
        return join(', ',$out);
    }
}