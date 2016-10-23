<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;

use App\Models\Corpus\Text;
use App\Models\Corpus\Transtext;

use App\Models\Dict\Lang;
use App\Models\Dict\Relation;

class Meaning extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

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
     * Meaning __has_many__ Meaning
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function meaningRelations(){
/*        return $this->belongsToMany(Meaning::class,'meaning_relation','meaning1_id','meaning2_id')
                    ->withPivot('relation_id');     */

        return $this->belongsToMany(Relation::class,'meaning_relation','meaning1_id','relation_id')
                    ->withPivot('meaning2_id');
    }

    /**
     * Meaning __has_many__ Lang
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function translations(){
/*        return $this->belongsToMany(Meaning::class,'meaning_relation','meaning1_id','meaning2_id')
                    ->withPivot('relation_id');     */

        return $this->belongsToMany(Lang::class,'meaning_translation','meaning1_id','lang_id')
                    ->withPivot('meaning2_id');
    }

    /**
     * Meaning __has_many__ Texts
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function texts(){
        return $this->belongsToMany(Text::class,'meaning_text');
    }
    
    /**
     * Gets sentences for examples in Lemma show page
     *
     * @param $for_edit Boolean: true - for edition, output all sentences, 
     *                           false - for view, output all positive examples (relevance>0)
     * @return array
     */
    public function sentences($for_edit=false){
        $sentences = [];
        $sentence_builder = DB::table('meaning_text')
                              ->where('meaning_id',$this->id)
                              ->orderBy('relevance','desc')
                              ->orderBy('text_id')
                              ->orderBy('sentence_id')
                              ->orderBy('word_id');
        if (!$for_edit) {
            $sentence_builder = $sentence_builder->where('relevance','>',0);
        }
//print "<p>". $sentence_builder->count()."</p>";       
        
        foreach ($sentence_builder->get() as $sentence) {
            $text = Text::find($sentence->text_id);
            if (!$text) {
//print "<p>text error</p>";
                continue;
            }
            list($sxe,$error_message) = Text::toXML($text->text_xml,$text->id);
            if ($error_message) {
//print "<p>$error_message</p>";                
                continue;
            }
            $s = $sxe->xpath('//s[@id="'.$sentence->sentence_id.'"]');
            if (isset($s[0])) {
                $transtext = Transtext::find($text->transtext_id);
                $trans_s = '';
                if ($transtext) {
                    list($trans_sxe,$trans_error) = Text::toXML($transtext->text_xml,'trans: '.$transtext->id);
                    if (!$trans_error) {
                        $trans_sent = $trans_sxe->xpath('//s[@id="'.$sentence->sentence_id.'"]');
                        if (isset($trans_sent[0])) {
                            $trans_s = $trans_sent[0]->asXML();
                        }
                    }                    
                }
                $sentences[] = ['s' => $s[0]->asXML(), 
                                's_id' => $sentence->sentence_id,
                                'text' => $text, 
                                'trans_s' => $trans_s,
                                'w_id' => $sentence->word_id, 
                                'relevance' => $sentence->relevance]; 
} else {
dd("!s: meaning_id=".$this->id.' and text_id='.$sentence->text_id.' and sentence_id='.$sentence->sentence_id.' and word_id='.$sentence->word_id);                    
            }
        }
        
//        $sentences = array_slice($sentences,0,$limit);
//print "<p>". sizeof($sentences)."</p>";       
        return $sentences;
    }
    
    /**
     * Gets all meaning texts and returns string:
     * 
     * <meaning_n>. <lang1_code>: <meaning_on_lang1>; <lang2_code>: <meaning_on_lang2>; ...
     * OR
     *              <lang1_code>: <meaning_on_lang1>; <lang2_code>: <meaning_on_lang2>; ...
     *              if lemma has one meaning 
     * 
     * @param $code String
     * @return String
     */
    public function getMultilangMeaningTextsString($code='') :String
    {
        $mean_langs = [];
        $meaning_texts = $this->meaningTexts();
        if ($code) {
            $lang = Lang::where('code',$code)->first();
            if ($lang) {
                $meaning_texts_by_code = $meaning_texts->where('lang_id',$lang->id);
                if ($meaning_texts_by_code->count()) {
                    $meaning_texts = $meaning_texts_by_code;
                }
            }
        }
        if ($meaning_texts->count()) {
            foreach ($meaning_texts->get() as $meaning_text_obj) {
                $meaning_text = $meaning_text_obj->meaning_text;
                if ($meaning_text) {
                    if ($meaning_text_obj->lang->code != $code) {
                        $meaning_text = $meaning_text_obj->lang->code .': '. $meaning_text;
                    }
                    $mean_langs[] = $meaning_text;  
                } 
            }
        } 
        
        $out = join(', ',$mean_langs);

        if ($this->lemma->meanings()->count()>1) {
            $out = $this->meaning_n. '. '.$out;
        }
        return $out;
    }


    /**
     * Gets all meaning texts and returns string:
     * 
     * <lemma> (<meaning_n>. <lang1_code>: <meaning_on_lang1>; <lang2_code>: <meaning_on_lang2>; ...)
     * OR
     * <lemma> (<lang1_code>: <meaning_on_lang1>; <lang2_code>: <meaning_on_lang2>; ...)
     *              if lemma has one meaning 
     * 
     * @return String
     */
    public function getLemmaMultilangMeaningTextsString() :String
    {
        return $this->lemma->lemma . ' ('. $this->getMultilangMeaningTextsString() . ')';
    }
    
    /**
     * Gets an array of meaning texts for ALL languages and sorted by lang_id
     *
     * @return Array
     */
    public function meaningTextsWithAllLangs()
    {
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
     * Gets relations missing in this meaning
     * 
     * @return Array [1=>'synonyms'...]
     */
    public function missingRelationsList() :Array
    {
        $relations = [];
        
        foreach (Relation::getList() as $relation_id=>$relation_text) {
            if ($this->meaningRelations()->wherePivot('relation_id',$relation_id)->count() == 0) {
                $relations[$relation_id] = $relation_text;
            }
        }
        return $relations;
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
            $meaning_obj->updateMeaningRelations(isset($meaning['relation']) ? $meaning['relation'] : []);

            $meaning_obj->updateMeaningTranslations(isset($meaning['translation']) ? $meaning['translation'] : []);

            // is meaning has any meaning texts or any relations
            if ($meaning_obj->meaningTexts()->count() || $meaning_obj->meaningRelations()->count()) { 
                $meaning_obj -> meaning_n = $meaning['meaning_n'];
                $meaning_obj -> save();
            } else {
                $meaning_obj -> delete();
            }
        }
    }

    /**
     * Updates array of meaning relations 
     *
     * @return NULL
     */
    public function updateMeaningRelations($relations)
    {
        // removes all relations to this meaning
        DB::table('meaning_relation')
          ->where('meaning2_id',$this->id)->delete();
        // removes all relations from this meaning
        $this->meaningRelations()->detach();
        
        if (!is_array($relations)) {
            return;
        }
        foreach ($relations as $relation_id=>$rel_means) {
            foreach ($rel_means as $rel_mean_id) {
                $this->meaningRelations()
                     ->attach($relation_id,['meaning2_id'=>$rel_mean_id]);
                
                // reverse relation
                $mean2_obj = self::find($rel_mean_id);
                $relation_obj = Relation::find($relation_id);
                $mean2_rels = $mean2_obj->meaningRelations();
//                if (!$mean2_rels->wherePivot('relation_id',$relation_obj->reverse_relation_id)
  //                              ->wherePivot('meaning2_id',$this->id)->count()) {
                 $mean2_rels->attach($relation_obj->reverse_relation_id,
                                        ['meaning2_id'=>$this->id]);
    //            }
            }
        }
    }
    
    /**
     * Updates array of meaning relations 
     *
     * @return NULL
     */
    public function updateMeaningTranslations($translations)
    {
        // removes all translations to this meaning
        DB::table('meaning_translation')
          ->where('meaning2_id',$this->id)->delete();
        // removes all translations from this meaning
        $this->translations()->detach();
        
        if (!is_array($translations)) {
            return;
        }
        foreach ($translations as $lang_id=>$trans_means) {
            foreach ($trans_means as $trans_mean_id) {
                $this->translations()
                     ->attach($lang_id,['meaning2_id'=>$trans_mean_id]);
                
                // reverse translation
                $mean2_obj = self::find($trans_mean_id);
                $mean2_transls = $mean2_obj->translations();
                $mean2_transls->attach($this->lemma->lang_id,
                                       ['meaning2_id'=>$this->id]);
            }
        }
    }
}
