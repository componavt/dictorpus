<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

use DB;
use LaravelLocalization;

use App\Models\Dict\Syntype;

class Label extends Model
{
    public $timestamps = false;
    protected $fillable = ['name_en', 'name_ru', 'short_en', 'short_ru', 'visible'];
    const OlodictLabel = 3;
    const ZaikovLabel = 5;
    const LDLLabel = 12;
    
    public function identifiableName()
    {
        return $this->name;
    }    

    // Methods
    use \App\Traits\Methods\getNameAttribute;
    use \App\Traits\Methods\getShortAttribute;
    
    public function syntypes(){
        return $this->belongsToMany(Syntype::class,'label_syntype');
    }
    
    public static function checkedOloLemmas() {
        return DB::table('label_lemma')->whereLabelId(self::OlodictLabel)
                 ->whereStatus(1)
                 ->select('lemma_id');
    }
    
    public static function ldlLemmas() {
        return DB::table('label_lemma')->whereLabelId(self::LDLLabel)
//                 ->whereStatus(1)
                 ->select('lemma_id');
    }
    
    public static function getList()
    {     
        $locale = LaravelLocalization::getCurrentLocale();
        
        return self::whereVisible(1)->orderBy('name_'.$locale)
                ->pluck('name_'.$locale,'id')->toArray();        
    }
    
    public static function store($data) {
        if (!$data['name_ru']) {
            return;
        }
        if (!$data['short_ru']) {
            $data['short_ru'] = $data['name_ru'];
        }
        if (!$data['name_en']) {                
            $data['name_en'] = $data['short_en'] ? $data['short_en'] : $data['name_ru'];
        }
        if (!$data['short_en']) {                
            $data['short_en'] = $data['name_en'];
        }
        $label = Label::create($data);
        return $label;        
    }
}
