<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;
use LaravelLocalization;

use App\Models\Corpus\Text;
//use App\Models\Corpus\Transtext;
use App\Models\Corpus\SentenceFragment;
use App\Models\Corpus\SentenceTranslation;

use App\Models\Dict\Lang;
use App\Models\Dict\Relation;

//use App\Models\Corpus\Word;

class Meaning extends Model
{
    use \App\Traits\MeaningModify;
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

    //Scopes
    use \App\Traits\Scopes\MeaningsForLdl;
    
    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Lemma;

    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Concepts;
    use \App\Traits\Relations\BelongsToMany\Dialects;
    use \App\Traits\Relations\BelongsToMany\MeaningRelations;
    use \App\Traits\Relations\BelongsToMany\Places;
    use \App\Traits\Relations\BelongsToMany\Translations;
    
    public function labels()
    {
        return $this->belongsToMany(Label::class);
    }
    
    public function phrases()
    {
        return $this->belongsToMany(Lemma::class,'meaning_phrase','meaning_id','lemma_id');
    }
    
    public function texts(){
        return $this->belongsToMany(Text::class,'meaning_text')
                ->withPivot('w_id')
                ->withPivot('relevance');
    }
    
    // Has Many Relations
    use \App\Traits\Relations\HasMany\Examples;
    use \App\Traits\Relations\HasMany\MeaningTexts;

    public function textByLangCode($lang_code, $default_code='') {
        $lang_id = Lang::getIDByCode($lang_code);
        $meaning_text_obj = MeaningText::where('lang_id',$lang_id)->where('meaning_id',$this->id)->first();
        if ($meaning_text_obj) {
            return $meaning_text_obj->meaning_text;
        }
        if ($default_code && $lang_code != $default_code) {
            return $this->textByLangCode($default_code);
        }
    }
    
    public function showShortLabels() {
        $out = [];
        foreach ($this->labels()->where('visible',1)->get() as $label) {
            $out[] = $label->short;
        }
        return $out;
    }
    
    /** Gets list of meanings for lemma $lemma_id,
     * if $lang_id is empty, gets null
     * 
     * @param $lemma_id - lemma ID
     * @return Array
     */
    public static function getList($lemma_id=NULL)
    {     
        $lemma = Lemma::find($lemma_id);
        if (!$lemma) {
            return;
        }
//        $locale = LaravelLocalization::getCurrentLocale();
        
        $meanings = $lemma->meanings;
        
        $list = array();
        foreach ($meanings as $row) {
            $list[$row->id] = $row->getMultilangMeaningTextsStringLocale();
        }
        
        return $list;         
    }
    
    public function hasPhoto() {
        if (isset($this->concepts[0]) && $this->concepts[0]->wiki_photo) {
            return true;
        }
    }
    
    public function photoInfo() {
        return $this->concepts[0]->photoInfo();
    }

        /**
     * Gets total number of sentences for examples in Lemma show page
     *
     * @param $for_edit Boolean: true - for edition, output all sentences, 
     *                           false - for view, output all positive examples (relevance>0)
     * @return array
     */
    public function countSentences($for_edit=false, $relevance=0)
    {    
        $sentence_builder = DB::table('meaning_text')
                              ->where('meaning_id',$this->id);
        if (!$for_edit) {
            $sentence_builder = $sentence_builder->where('relevance','>',$relevance);
        }
        return $sentence_builder->count();
    }
    
    /**
     * Gets sentences for examples in Lemma show page
     *
     * @param $for_edit Boolean: true - for edition, output all sentences, 
     *                           false - for view, output all positive examples (relevance>0)
     * @return array
     */
    public function sentences($for_edit=false, $limit='', $start=0, $relevance=''){
        $sentences = [];
        $sentence_builder = DB::table('meaning_text')
                              ->where('meaning_id',$this->id);
        
        if ($relevance !== '') {
             $sentence_builder = $sentence_builder->whereRelevance($relevance);
        }
        
        $sentence_builder = $sentence_builder->orderBy('relevance','desc')
                              ->orderBy('text_id')
                              ->orderBy('s_id')
                              ->orderBy('word_id');
        if (!$for_edit) {
            $sentence_builder = $sentence_builder->where('relevance','>',$relevance);
        }
        
        if ($limit) {
            if ($start) {
                $sentence_builder = $sentence_builder->skip($start);
            }
            $sentence_builder = $sentence_builder->take($limit);
        }
//print "<p>". $sentence_builder->count()."</p>";       
        
        foreach ($sentence_builder->get() as $meaning_text) {
            $sentence = Text::extractSentence($meaning_text->text_id, 
                                              $meaning_text->s_id, 
                                              $meaning_text->w_id, 
                                              $meaning_text->relevance);
            if ($sentence) {
                $fragment = SentenceFragment::getBySW($sentence['sent_obj']->id,
                                                      $meaning_text->w_id);
                if ($fragment) {
                    $sentence['s'] = $fragment->text_xml;
                }
                $translation_text = //preg_replace("/\r?\n/", "",
                        process_text(
                        SentenceTranslation::getTextForLocale($sentence['sent_obj']->id,
                                                              $meaning_text->w_id));
/*                if (preg_match("/^(\<s id=\"\d+\"\>)\<br\>(.+)$/", $translation_text, $regs)) {
                    $translation_text = $regs[1].$regs[2];
                }*/
                if ($translation_text) {
                    $sentence['trans_s'] = $translation_text;
                }
                $sentences[] = $sentence;
            }
        }
        
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
     * @param $lang_code String - language code
     * @return String
     */
    public function getMultilangMeaningTextsString($lang_code='') :String
    {
        $mean_langs = [];
        $meaning_texts = $this->meaningTexts()->get();
        if ($lang_code) {
            $lang = Lang::where('code',$lang_code)->first();
            if ($lang) {
                $meaning_texts_by_code = $this->meaningTexts()->where('lang_id',$lang->id);
                if ($meaning_texts_by_code->count() > 0) {
                    $meaning_texts = $meaning_texts_by_code->get();
                }
            }
        }
        foreach ($meaning_texts as $meaning_text_obj) {
            $meaning_text = $meaning_text_obj->meaning_text;
            if ($meaning_text) {
                if ($meaning_text_obj->lang->code != $lang_code) {
                    $meaning_text = $meaning_text_obj->lang->code .': '. $meaning_text;
                }
                $mean_langs[] = $meaning_text;  
            } 
        }
        
        $out = join(', ',$mean_langs);

        if ($this->lemma->meanings()->count()>1) {
            $out = $this->meaning_n. ') '.$out;
        }
        return $out;
    }
    
    public function getMeaningTextByLangCode($lang_code) {
        $lang = Lang::where('code',$lang_code)->first();
        if (!$lang) {
            return;
        }
        $meaning_text = $this->meaningTexts()->where('lang_id',$lang->id)->first();
        if ($meaning_text) {
            return $meaning_text->meaning_text;
        }
        
    }
    public function getLemmaRelation($relation_id, $label_id=null, $label_status=1) {
        $relation_meanings = $this->meaningRelations()->wherePivot('relation_id', $relation_id);
        if ($label_id) {
            $relation_meanings->wherePivotIn('meaning2_id', function ($q) use ($label_id, $label_status) {
                $q->select('id')->from('meanings')
                  ->whereIn('lemma_id', function ($q2 )use ($label_id, $label_status) {
                      $q2->select('lemma_id')->from('label_lemma')
                         ->whereLabelId($label_id)
                         ->whereStatus($label_status);
                  });
            });
        }
        $relation_meanings = $relation_meanings->get();
        if (!$relation_meanings) {
            return null;
        }
        $meaning_relations=[];
        foreach ($relation_meanings as $relation_meaning) {
            $meaning2_id = $relation_meaning->pivot->meaning2_id;
            $relation_id = $relation_meaning->pivot->relation_id;
            $relation_meaning_obj = self::find($meaning2_id);
            $relation_lemma_obj = $relation_meaning_obj->lemma;
            $relation_lemma = $relation_lemma_obj->lemma;
            $meaning_relations[$relation_lemma_obj->id]  
                    = $relation_lemma;
        }
        return $meaning_relations;
    }
    
    public function getLemmaRelations($label_id=null, $label_status=1) {
        $relations = Relation::getList();
        $relation_meanings = $this->meaningRelations();
        if ($label_id) {
            $relation_meanings->wherePivotIn('meaning2_id', function ($q) use ($label_id, $label_status) {
                $q->select('id')->from('meanings')
                  ->whereIn('lemma_id', function ($q2 )use ($label_id, $label_status) {
                      $q2->select('lemma_id')->from('label_lemma')
                         ->whereLabelId($label_id)
                         ->whereStatus($label_status);
                  });
            });
        }
        $relation_meanings = $relation_meanings->get();
        if (!$relation_meanings) {
            return null;
        }
        $meaning_relations=[];
        foreach ($relation_meanings as $relation_meaning) {
            $meaning2_id = $relation_meaning->pivot->meaning2_id;
            $relation_id = $relation_meaning->pivot->relation_id;
            $relation_text = $relations[$relation_id];
            $relation_meaning_obj = self::find($meaning2_id);
            $relation_lemma_obj = $relation_meaning_obj->lemma;
            $relation_lemma = $relation_lemma_obj->lemma;
            $meaning_relations[$relation_text][$relation_lemma_obj->id]  
                    = /*['lemma' =>*/ $relation_lemma/*,
                       'meaning' => $relation_meaning_obj->getMultilangMeaningTextsString()]*/;
        }
        return $meaning_relations;
    }
    
    /**
     * Значения с примерами для школьного словаря
     * <meaning_n>. <meaning_on_ru>; <example1> <example1_ru>; <example2> <example2_ru>;
     * OR
     *              <meaning_on_ru>; <example1> <example1_ru>; <example2> <example2_ru>;
     */
    public function getMeaningWithExamples() {
        $out = $this->getMultilangMeaningTextsString('ru');
        foreach ($this->examples as $example) {
            $out .= '; '.$example->example. ' '.$example->example_ru;
        }
        return $out;
    }

    public function getMultilangMeaningTextsStringLocale() :String {
        return $this->getMultilangMeaningTextsString(LaravelLocalization::getCurrentLocale());
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
    
    public function upMeaningN() {
        if ($this->meaning_n == 1) {
            return;
        }
        $prev_n = $this->meaning_n - 1;
        $lemma = $this->lemma;
        
        $prev = $lemma->meanings()->where('id','<>',$this->id)->whereMeaningN($prev_n)->first();
        if (!$prev) {
            $this->meaning_n = $prev_n;
            $this->save();
            return;
        }
        
        $nextN = $lemma->getNewMeaningN();
        $prev->meaning_n = $nextN;
        $prev->save();
        
        $this->meaning_n = $prev_n;
        $this->save();
        
        $prev->meaning_n = 1+ $prev_n;
        $prev->save();
    }

    public function downMeaningN() {
        $lemma = $this->lemma;
        if ($this->meaning_n == $lemma->maxMeaningN()) {
            return;
        }
        $next_n = $this->meaning_n + 1;
        
        $next = $lemma->meanings()->where('id','<>',$this->id)->whereMeaningN($next_n)->first();
        if (!$next) {
            $this->meaning_n = $next_n;
            $this->save();
            return;
        }
        
        $nextN = $lemma->getNewMeaningN();
        $next->meaning_n = $nextN;
        $next->save();
        
        $this->meaning_n = $next_n;
        $this->save();
        
        $next->meaning_n = $next_n - 1;
        $next->save();
    }

    /**
     * Ckeck if any checked meaning exists
     * Return 0, if it exists
     * 
     * @param int $text_id
     * @param int $w_id
     * @param int $old_relevance
     * @return int
     */
    public function checkRelevance($text_id, $w_id, $old_relevance=1) {
        if ($old_relevance == 0 ||
        // if some another meaning has positive evaluation with this sentence, 
        // it means that this meaning is not suitable for this example
            DB::table('meaning_text')->where('meaning_id','<>',$this->id)
              ->whereTextId($text_id)->whereWId($w_id)
              ->where('relevance','>',1)->count()>0) {
            return 0;
        } 
/*if ($text_id==1548 && $w_id==7) {
dd($relevance);
} */       
        return $old_relevance;
    }

    /**
     * Saves relevances <> 1 into array 
     * 
     * @return Array
     */
    public function getRelevances() {
        $relevances = [];
        foreach ($this->texts as $text) {
            if ($text->pivot->relevance != 1) {
                $relevances[$text->id][$text->pivot->w_id] = $text->pivot->relevance;
            }
        }
        return $relevances;
    }

    public static function countTranslations(){
        return DB::table('meaning_translation')->count();
    }
    
    public static function countRelations(){
        return DB::table('meaning_relation')->count();
    }
        
    
}
