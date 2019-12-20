<?php namespace App\Traits\Relations\BelongsTo;

trait Lang
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lang()
    {
        return $this->belongsTo('App\Models\Dict\Lang');
    }    
    
    public static function getLangIDbyID($id) {
        $obj=self::find($id);
        if (!$obj) {
            return null;
        }
        return $obj->lang_id;
    }

}