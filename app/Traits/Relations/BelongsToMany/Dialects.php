<?php namespace App\Traits\Relations\BelongsToMany;

use LaravelLocalization;

trait Dialects
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dialects(){
        $locale = LaravelLocalization::getCurrentLocale();
        return $this->belongsToMany('App\Models\Dict\Dialect')
                    ->orderBy('name_'.$locale);
    }
    
    /**
     * Gets IDs of dialects for dialect's form field
     *
     * @return Array
     */
    public function dialectValue():Array{
        $value = [];
        if ($this->dialects) {
            foreach ($this->dialects as $dialect) {
                $value[] = $dialect->id;
            }
        }
        return $value;
    }

    public function dialectListToString() {
        $out = [];
        foreach ($this->dialects as $dialect) {
            $out[] = $dialect->name;
        }
        return join(', ',$out);
    }
}