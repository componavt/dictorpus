<?php
namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;
use LaravelLocalization;

use App\Models\Corpus\Sentence;
use App\Models\Corpus\Text;

//use App\Library\Grammatic;
use App\Models\Dict\PartOfSpeech;

class Gramset extends Model
{
    public $timestamps = false;
    protected $fillable = ['gram_id_number', 'gram_id_case', 'gram_id_tense', 'gram_id_person', 'gram_id_mood', 'gramset_category_id',
                           'sequence_number', 'gram_id_negation', 'gram_id_infinitive', 'gram_id_voice','gram_id_participle','gram_id_reflexive'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Dialect;
    use \App\Traits\Relations\BelongsTo\GramsetCategory;
    use \App\Traits\Relations\BelongsTo\GramR;
    
    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\PartsOfSpeech;

    public function langs()
    {
        return $this->belongsToMany(Lang::class,'gramset_pos','gramset_id','lang_id');
    }
    
    public function lemmas($pos_id='', $lang_id=''){
        $builder = $this->belongsToMany(Lemma::class,'lemma_wordform');
        if ($pos_id) {
            $builder = $builder->whereIn('lemma_id',function($query) use ($pos_id){
                                $query->select('id')
                                ->from(with(new Lemma)->getTable())
                                ->where('pos_id', $pos_id);
                            });
        }
        if ($lang_id) {
            $builder = $builder->whereIn('lemma_id',function($query) use ($lang_id){
                                $query->select('id')
                                ->from(with(new Lemma)->getTable())
                                ->where('lang_id', $lang_id);
                            });
        }
//dd($builder->toSql());        
        return $builder;
    }
    
    public function wordforms($pos_id='', $lang_id=''){
        $builder = $this->belongsToMany(Wordform::class,'lemma_wordform');
        if ($pos_id) {
            $builder = $builder->whereIn('lemma_id',function($query) use ($pos_id){
                                $query->select('id')
                                ->from(with(new Lemma)->getTable())
                                ->where('pos_id', $pos_id);
                            });
        }
        if ($lang_id) {
            $builder = $builder->whereIn('lemma_id',function($query) use ($lang_id){
                                $query->select('id')
                                ->from(with(new Lemma)->getTable())
                                ->where('lang_id', $lang_id);
                            });
        }
        return $builder;
    }

    
    // Gramset __belongs_to__ Gram
/*    public function gramReflexive()
    {
        return $this->belongsTo(Gram::class, 'gram_id_reflexive');
    }*/
    
    public static function gramsetsLangPOS($lang_id, $pos_id) {
        return self::whereIn('id', function ($query) use ($lang_id, $pos_id) {
                                        $query ->select('gramset_id')->from('gramset_pos')
                                               ->where('lang_id', $lang_id)
                                               ->where('pos_id', $pos_id);
                                    })->orderBy('sequence_number');
    }
    
    public static function getGroupedListByPOS($pos_category_id, $lang_id, $pos_id) {
        $groups = [];
//dd(GramsetCategory::getList($pos_category_id), $lang_id, $pos_id);        
        foreach (GramsetCategory::getList($pos_category_id) as $category_id => $category_name) {
            $gramsets = self::gramsetsLangPOS($lang_id, $pos_id)
                    ->where('gramset_category_id', $category_id)->get();
//dd($gramsets);
            foreach ($gramsets as $gramset) {
                $groups[$category_name][$gramset->id] = $gramset->inCategoryString();
            }
        }
//dd($groups);        
        return $groups;
    }

    public static function getGroupedList($pos_id, $lang_id) {
        $groups = [];
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) {
            $groups = self::getGroupedListByPOS(1, $lang_id, $pos_id);
        } elseif ($pos_id == PartOfSpeech::getVerbID()) {
            $groups = self::getGroupedListByPOS(2, $lang_id, $pos_id);
        } else {
            $gramsets = self::gramsetsLangPOS($lang_id, $pos_id)->get();
            foreach ($gramsets as $gramset) {
                $groups[NULL][$gramset->id] = $gramset->gramsetString();
            }
        }
//dd($groups);        
        return $groups;
    }

    public function toCONLL() {
            $feats = [];
            $fields = ['Number', 'Case', 'Tense', 'Person', 'Mood', 'Negation', 'Infinitive', 'Voice', 'Participle'];
            foreach ($fields as $field) {
                $name = 'gram'.$field;
                if ($this->$name && $this->$name->conll) {
                    $feats[] = $this->$name->conll;
                }
            }
        return $feats;
    }
     
    public function inCategoryString($with_number=false) : String
    {
        if (isset($this->gramsetCategory->pos_category_id) && $this->gramsetCategory->pos_category_id ==1) {
            if ($this->gram_id_case){
//                $out = $this->gramCase->name_short;
                $out = $this->gramCase->name;
            }
        } elseif (isset($this->gramsetCategory->id) &&  $this->gramsetCategory->id == 27) { // passive
            if ($this->gram_id_tense){
                $out = $this->gramTense->name_short;
            }
        } else {
            $list = array();
            if ($this->gram_id_infinitive){
                $list[] = $this->gramInfinitive->name_short;
            }

            if ($this->gram_id_person){
                $list[] = $this->gramPerson->name_short;
            }

            if ($this->gram_id_number){
                $list[] = $this->gramNumber->name_short;
            }

            if ($this->gram_id_case){
                $list[] = $this->gramCase->name_short;
            }

            if ($this->gram_id_negation == 53){ // connegative
                $list[] = $this->gramNegation->name_short;
            }
        
            if ($this->gram_id_voice){
                $list[] = $this->gramVoice->name_short;
            }

            if ($this->gram_id_participle){
                $list[] = $this->gramParticiple->name_short;
            }
            
            $out = join(', ', $list);
        }
        if ($with_number) {
            $out = $this->sequence_number .') '.$out;
        }
        
        if ($this->id == 282) {
            $out .= ' ('. (LaravelLocalization::getCurrentLocale() == 'ru' ? 'крат. ф.' : 'short form'). ')';
        }
        return $out;
    }

    /** Gets concatenated list of grammatical attributes for this gramset
     * 
     * @param String $glue
     * @return String concatenated list of grammatical attributes (e.g. 'ед. ч., номинатив')
     */
    public function gramsetString(String $glue=', ', $with_number=false) : String
    {
        $list = array();
        if ($this->gram_id_reflexive){
            $list[] = $this->gramReflexive->name_short;
        }
            
        if ($this->gram_id_infinitive){
            $list[] = $this->gramInfinitive->name_short;
        }
            
        if ($this->gram_id_mood){
            $list[] = $this->gramMood->name_short;
        }
            
        if ($this->gram_id_tense){
            $list[] = $this->gramTense->name_short;
        }
            
        if ($this->gram_id_person){
            $list[] = $this->gramPerson->name_short;
        }
            
        if ($this->gram_id_case){
            $list[] = $this->gramCase->name_short;
        }
            
        if ($this->gram_id_number){
            $list[] = $this->gramNumber->name_short;
        }
            
        if ($this->gram_id_negation){
            $list[] = $this->gramNegation->name_short;
        }
            
        if ($this->gram_id_voice){
            $list[] = $this->gramVoice->name_short;
        }
            
        if ($this->gram_id_participle){
            $list[] = $this->gramParticiple->name_short;
        }
           
        $out = join($glue, $list);
        if ($with_number) {
            $out = $this->sequence_number .'. '.$out;
        }
        
        if ($this->id == 282) {
            $out .= ' ('. (LaravelLocalization::getCurrentLocale() == 'ru' ? 'крат. ф.' : 'short form'). ')';
        }
        return $out;
    }
    
    public static function countForPosLang($pos_id, $lang_id) {
        return Gramset::whereIn('id', function ($q) use ($pos_id, $lang_id) {
            $q->select('gramset_id')->from('gramset_pos')
              ->wherePosId($pos_id)
              ->whereLangId($lang_id);
        })->count();
    }


    public static function getStringByID($gramset_id) {
        $gramset = self::find($gramset_id);
        if (!$gramset) {
            return;
        }
        return $gramset->gramsetString();
    }
    
    /** Gets ordered list of gramsets for the part of speech and the language
     * 
     * @param int $pos_id
     * @param int $lang_id
     * @param boolean $with_number - with sequence_number
     * @return Array [1=>'ед. ч., номинатив',..]
     */
    public static function getList(int $pos_id, int $lang_id=NULL, $with_number=false)
    {
        // select id from gramsets,gramset_pos where gramset_pos.gramset_id=gramsets.id and gramset_pos.pos_id=5 group by id order by sequence_number;

        $gramsets = self::orderBy('sequence_number');
        if ($pos_id || $lang_id) {
            $gramsets->whereIn('id', function ($q) use ($lang_id, $pos_id) {
                    $q->select('gramset_id')->from('gramset_pos');
                    if ($pos_id) {
                        $q->wherePosId($pos_id);
                    }
                    if ($lang_id) {
                        $q->whereLangId($lang_id);
                    }
                });
        }         
        
        $list = array();
        foreach ($gramsets->get() as $row) {
            $gramset = self::find($row->id);
            $list[$row->id] = $gramset->gramsetString();
            if ($with_number) {
                $list[$row->id] = $gramset->sequence_number .'. '.$list[$row->id];
            }
        }
        
        return $list;         
    }
    
    public static function isIdForName($id) {
        $gramset = self::find($id);
        if (!$gramset) {return NULL;}
        
        return $gramset->parts_of_speech[0]->isName();
    }

    public static function isIdForVerb($id) {
        $gramset = self::find($id);
        return $gramset->parts_of_speech[0]->isVerb();
    }

        /**
     * 1 - nominative, singular
     * 3 - genetive, singular
     * 4 - partitive, singular
     * 10 - illative, singular
     * 2 - nominative, plural
     * 22 - partitive, plural
     * 24 - genetive, plural
     * 
     * 26 - indicative, presence, 1st, sg
     * 28 - indicative, presence, 3st, sg
     * 31 - indicative, presence, 3st, pl
     * 170 - infinite
     * 
     * @param INT $pos_id - part of speech ID
     * @param BOOLEAN $is_plural = 1, if lemma is plural noun
     * @param INT $lang_id - language ID, 5 - livvic
     * @return ARRAY or NULL
     */
    public static function dictionaryGramsets($pos_id, $number=NULL, $lang_id=5) {
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) {
            if ($lang_id==4 || $lang_id==6) { // proper karelian, ludic пока не известно какие нужны формы для plural tantum
                return [0=>3, 1=>10, 2=>4, 3=>1];    // 3=gen sg, 10=ill sg, 4=part sg, 1=nom sg          
            } elseif ($number==1) { // plural
                if ($lang_id == 1) { // vepsian
                    return [1=>22, 3=>2];
                } else {
                    return [0=>24, 1=>22, 3=>2];
                }
            } elseif ($number==2) { // single
                return [0=>3, 1=>4, 3=>1];
            } else {
                return [0=>3, 1=>4, 2=>22, 3=>1];
            }
        } elseif ($pos_id == PartOfSpeech::getVerbID()) { 
            if ($lang_id == 1) { // vepsian
                return [0=>28, 1=>34, 2=>52, 3=>170];
            } elseif ($lang_id == 5) { // livvi
                return [0=>26, 1=>28, 2=>31, 3=>34, 4=>37, 5=>170];
            } elseif($lang_id == 4 || $lang_id==6) { // proper
                return [0=>26, 3=>170];
//                return [0=>28, 3=>170];
            }
        }
        return NULL;
    }

    public static function dictionaryGramsetNames($lang_id, $pos_id) {
        $names = [];
        $gramsets = self::dictionaryGramsets($pos_id, NULL, $lang_id);
//        $last = array_pop($gramsets);   
//        array_unshift($gramsets,$last); 
        
        if (!$gramsets) {
            return $names;
        }
        foreach ($gramsets as $gramset_id) {
            $names[] = self::getStringByID($gramset_id);
        }
        return $names;
    }
    
    public static function search(Array $url_args) {
        $gramsets = Gramset::orderBy('sequence_number')
                  ->join('gramset_pos', 'gramsets.id', '=', 'gramset_pos.gramset_id');

        $gramsets = self::searchByLang($gramsets, $url_args['search_lang']);
        $gramsets = self::searchByPOS($gramsets, $url_args['search_pos']);
        $gramsets = self::searchByCategory($gramsets, $url_args['search_category']);

        return $gramsets->groupBy('gramsets.id');
    }

    public static function searchByLang($gramsets, $lang) {
        if (!$lang) {
            return $gramsets;
        }
        return $gramsets->where('lang_id',$lang);
    }

    public static function searchByPOS($gramsets, $pos) {
        if (!$pos) {
            return $gramsets;
        }
        return $gramsets->where('pos_id',$pos);
    }
    
    public static function searchByCategory($gramsets, $category) {
        if (!$category) {
            return $gramsets;
        }
        return $gramsets->where('gramset_category_id',$category);
    }
    
    public function countTexts($lang_id=null, $pos_id=null){
        $search_words[1]['p'] = $pos_id ? [$pos_id] : [];
        $search_words[1]['gs'] = $this->id;
        $texts = Text::whereIn('id',array_unique(Sentence::searchWords($search_words)
                   ->pluck('text1_id')));
        if ($lang_id) {
            $texts = $texts->whereLangId($lang_id);
        }
        return $texts->count();
    }

    public function countWords($lang_id=null, $pos_id=null){
        $search_words[1]['p'] = $pos_id ? [$pos_id] : [];
        $search_words[1]['gs'] = $this->id;
        $words = Sentence::searchWords($search_words)
                ->when($lang_id, function ($q) use ($lang_id) {
                    return $q->whereIn('text_id', function ($q2) use ($lang_id) {
                                $q2->select('id')->from('texts')
                                   ->whereLangId($lang_id);
                    });
                });
        return $words->count();
    }

    /**
     * checks gramset data and 
     * remove empty columns for the index page
     * 
     * @param Gramset $gramsets
     */
    public static function fieldsForIndex($gramsets) {
        $gram_fields = [];
        $all_gram_fields = GramCategory::getNames();    
        foreach ($all_gram_fields as $field) {
            foreach ($gramsets as $gramset) {
                if ($gramset->{'gram'.ucfirst($field)} != NULL) {
                    $gram_fields[] = $field;
                    continue 2;
                }
            }
        }        
        return $gram_fields;
    }

    public function toUniMorph($pos_code) {
        $feats = [$pos_code];
        $fields = ['Infinitive', 'Participle','Voice', 'Tense', 'Mood', 'Negation', 'Person', 'Case','Number'];
        foreach ($fields as $field) {
            $name = 'gram'.$field;
//print "<P>$name";   
//if ($this->$name) {            dd($this->$name); }
            if ($this->$name) {
                // исключаем коннегатив, его нет в unimorph
                if ($this->$name->unimorph && $this->$name->unimorph!='CON') {
                    if (preg_match("/^V\./",$this->$name->unimorph) && $feats[0] == 'V') {
                        unset($feats[0]);
                    }
                    $feats[] = $this->$name->unimorph;
                } else { return false; } // если нет кода для какой-то грамемы, то эту словоформу не выгружаем (например, инфинитив 2)
            }
        }  
//dd($feats);        
//        return $feats;
        return join(';',$feats);
    }
    
    public function toLGR($div='-') {
        $feats = [];
        $fields = ['Infinitive', 'Participle','Voice', 'Tense', 'Mood', 'Negation', 'Person', 'Number', 'Case'];
        foreach ($fields as $field) {
            $name = 'gram'.$field;
            if ($this->$name) {
                $feats[] = $this->$name->lgr;
            }
        }  
        return join($div,$feats);
    }
}

/*
    Addition new grammatical category:

    in mysql console:
        insert into `gram_categories` values (4, 'person','лицо');

        insert into `grams` (id, `gram_category_id`, `name_short_en`, `name_en`, `name_short_ru`, `name_ru`, `sequence_number`) values (21, 4, '1st', 'first person', '1 л.', '1 лицо', 1);
        insert into `grams` (id, `gram_category_id`, `name_short_en`, `name_en`, `name_short_ru`, `name_ru`, `sequence_number`) values (22, 4, '2nd', 'second person', '2 л.', '2 лицо', 2);
        insert into `grams` (id, `gram_category_id`, `name_short_en`, `name_en`, `name_short_ru`, `name_ru`, `sequence_number`) values (23, 4, '3rd', 'third person', '3 л.', '3 лицо', 3);

        alter table `gramsets` add `gram_id_person` smallint(5) unsigned default null after `gram_id_tense`;
        ALTER TABLE `gramsets` ADD CONSTRAINT `gramsets_gram_id_person_foreign` FOREIGN KEY (`gram_id_person`) REFERENCES `grams` (`id`);

    add strings into migration create_gramsets_table:
            // id of grammatical person attribute
            $table->smallInteger('gram_id_person')->unsigned()->nullable();
            $table->     foreign('gram_id_person')->references('id')->on('grams');

    add relation for model Gramset:
        public function gramPerson()
        {
            return $this->belongsTo(Gram::class, 'gram_id_person');
        }

    change method $this->gramsetString():
        if ($this->gram_id_person){
            $list[] = $this->gramPerson->name_short;
        }
    add new field in $fillable of this Model:
        $fillable = ['gram_id_number', 'gram_id_case', 'gram_id_tense', 'gram_id_person', ...];
 
    
 
*/
