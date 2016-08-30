<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use App\Models\Dict\Lang;

class Meaning extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }
 
    protected $fillable = ['lemma_id','meaning_n'];

    /**
     * Meaning __belongs_to__ Lemma
     * 
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function lemma()
    {
        return $this->belongsTo(Lemma::class);
    }
    
    /**
     * Meaning __has_many__ MeaningTexts
     * 
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function meaningTexts()
    {
        return $this->hasMany(MeaningText::class);
    }

    /**
     * Gets a collection of meaning texts for ALL languages and sorted by lang_id
     * 
     * @return Wordform Object
     */
    public function meaningTextsWithAllLangs(){
        $own_lang_id = $this->lemma->lang_id;

        $langs = Lang::getListWithPriority($own_lang_id);
        $meaning_texts = array();
        
        foreach ($langs as $lang_id => $lang_text) {
                $meaning_text_obj = $this->meaningTexts()->where('lang_id', $lang_id)->first();
                if (!$meaning_text_obj) {
                    $meaning_text_obj = new MeaningText;
                    $meaning_text_obj -> meaning_text = NULL;
                } 
                $meaning_text_obj -> lang_name = $lang_text;
                $meaning_texts[$lang_id] = $meaning_text_obj;
        }
        
        return $meaning_texts;
    }
    
}
