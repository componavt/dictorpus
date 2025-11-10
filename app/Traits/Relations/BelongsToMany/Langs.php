<?php namespace App\Traits\Relations\BelongsToMany;

use App\Models\Dict\Lang;

trait Langs
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function langs()
    {
        return $this->belongsToMany(Lang::class);
    }
    
    /**
     * Gets IDs of langs for lang's form field
     *
     * @return Array
     */
    public function langValue():Array{
        $value = [];
        if ($this->langs) {
            foreach ($this->langs as $lang) {
                $value[] = $lang->id;
            }
        }
        return $value;
    }

    /**
     * Gets a list of languages for model.
     *
     * @return string
     */
    public function langsToString()
    {
        $list = [];
        
        foreach ($this->langs as $lang) {
            $list[] = $lang->name;
        }
        return join(', ', $list);
    }
}