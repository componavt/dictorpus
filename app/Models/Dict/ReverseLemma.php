<?php

namespace App\Models\Dict;

use DB;

use Illuminate\Database\Eloquent\Model;

class ReverseLemma extends Model
{
    public $timestamps = false;
    protected $fillable = ['reverse_lemma','id','lang_id','inflexion'];//lemma_
    
    // Lemma __belongs_to__ Lang
    public function lemma()
    {
        return $this->belongsTo(Lemma::class,'id');
    }    
    
    // Lemma __belongs_to__ Lang
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }    
    
    public static function search(Array $url_args) {
        $lemmas = self::orderBy('reverse_lemma');
        if (!$url_args['search_lang']) {
            return NULL;
        }
        $lemmas = self::searchByLang($lemmas, $url_args['search_lang']);

        return $lemmas;
    }
    
    public static function searchByLang($lemmas, $lang) {
        if (!$lang) {
            return $lemmas;
        }
        return $lemmas->where('lang_id',$lang);
    }
    
    
}
