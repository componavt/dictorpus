<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

use DB;

class Label extends Model
{
    public $timestamps = false;
    protected $fillable = ['name_en', 'name_ru'];
    const OlodictLabel = 3;
    
    public function identifiableName()
    {
        return $this->name;
    }    

    /** Gets name of this lang, takes into account locale.
     * 
     * @return String
     */
    public function getNameAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        return $this->{$column};
    }
    
    public static function checkedOloLemmas() {
        return DB::table('label_lemma')->whereLabelId(self::OlodictLabel)
//                 ->whereStatus(1)
                 ->select('lemma_id');
    }
}
