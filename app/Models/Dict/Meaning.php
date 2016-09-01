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

    /**
     * Stores array of new meanings for the lemma
     * 
     * @return NULL
     */
    public static function storeLemmaMeanings($meanings, $lemma_id){
        if (!$meanings || !is_array($meanings)) {
            return;
        }
        foreach ($meanings as $meaning) {
            $meaning_texts = $meaning['meaning_text'];
            foreach ($meaning_texts as $lang=>$meaning_text) {
                if (!$meaning_text) {
                    unset($meaning_texts[$lang]);
                }
            }

            if (sizeof($meaning_texts)){
                $meaning_obj = self::firstOrCreate(['lemma_id' => $lemma_id, 'meaning_n' => (int)$meaning['meaning_n']]);

                foreach ($meaning_texts as $lang=>$meaning_text) {                        
                    $meaning_text_obj = MeaningText::firstOrCreate(['meaning_id' => $meaning_obj->id, 'lang_id' => $lang]);
                    $meaning_text_obj -> meaning_text = $meaning_text;
                    $meaning_text_obj -> save();
                }
            }
            
        }
    }

    /**
     * Updates array of meanings and remove meanings without meaning texts
     * 
     * @return NULL
     */
    public static function updateLemmaMeanings($meanings){
        if (!$meanings || !is_array($meanings)) {
            return;
        }
        foreach ($meanings as $meaning_id => $meaning) {
            $meaning_obj = Meaning::find($meaning_id);

            foreach ($meaning['meaning_text'] as $lang=>$meaning_text) {   
                if ($meaning_text) {
                    $meaning_text_obj = MeaningText::firstOrCreate(['meaning_id' => $meaning_id, 'lang_id' => $lang]);
                    $meaning_text_obj -> meaning_text = $meaning_text;
                    $meaning_text_obj -> save(); 
                } else {
                    // delete if meaning_text exists in DB but it's empty in form
                    $meaning_text_obj = MeaningText::where('meaning_id',$meaning_id)->where('lang_id',$lang)->first();
                    if ($meaning_text_obj) {
                        $meaning_text_obj -> delete();
                    }    
                }
            }

            if ($meaning_obj->meaningTexts()->count()) { // is meaning has any meaning texts
                $meaning_obj -> meaning_n = $meaning['meaning_n'];
                $meaning_obj -> save();                    
            } else {
                $meaning_obj -> delete();
                
            }
        }
    }
    
}
