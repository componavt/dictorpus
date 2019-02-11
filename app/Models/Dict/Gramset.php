<?php
namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;

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

    public function gramsetCategory()
    {
        return $this->belongsTo(GramsetCategory::class);
    }
    
    // Gramset __belongs_to__ Dialect
    public function dialect()
    {
        return $this->belongsTo(Dialect::class);
    }
    
    // Gramset __belongs_to__ Gram
    public function gramNumber()
    {
        return $this->belongsTo(Gram::class, 'gram_id_number');
    }
    
    public function gramCase()
    {
        return $this->belongsTo(Gram::class, 'gram_id_case');
    }
    
    public function gramTense()
    {
        return $this->belongsTo(Gram::class, 'gram_id_tense');
    }
    
    public function gramPerson()
    {
        return $this->belongsTo(Gram::class, 'gram_id_person');
    }
    
    public function gramMood()
    {
        return $this->belongsTo(Gram::class, 'gram_id_mood');
    }
    
    public function gramNegation()
    {
        return $this->belongsTo(Gram::class, 'gram_id_negation');
    }
    
    public function gramInfinitive()
    {
        return $this->belongsTo(Gram::class, 'gram_id_infinitive');
    }
    
    public function gramVoice()
    {
        return $this->belongsTo(Gram::class, 'gram_id_voice');
    }
    
    public function gramParticiple()
    {
        return $this->belongsTo(Gram::class, 'gram_id_participle');
    }
    
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
    
    //public static function gramsetCategories($categories)


    public static function getGroupedList($lang_id, $pos_id) {
        $groups = [];
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) {
            foreach (Gram::getList(GramCategory::getIDByName('number')) as $category_id => $category_name) {
                $gramsets = self::gramsetsLangPOS($lang_id, $pos_id)
                        ->where('gram_id_number', $category_id)->get();
                foreach ($gramsets as $gramset) {
                    if ($gramset->gram_id_case){
                        $groups[$category_name][$gramset->id] = $gramset->gramCase->name_short;
                    }
                }
            }
//        } elseif ($pos_id == PartOfSpeech::getVerbID()) {
        } else {
            $gramsets = self::gramsetsLangPOS($lang_id, $pos_id)->get();
            foreach ($gramsets as $gramset) {
                $groups[NULL][$gramset->id] = $gramset->gramsetString();
            }
        }
//dd($groups);        
        return $groups;
    }

    public function inCategoryString(String $glue=', ', $with_number=false) : String
    {
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

    // Gramset __has_many__ PartOfSpeech
    public function parts_of_speech()
    {
        return $this->belongsToMany(PartOfSpeech::class,'gramset_pos','gramset_id','pos_id');
    }
     
    // Gramset __has_many__ Lang
    public function langs()
    {
        return $this->belongsToMany(Lang::class,'gramset_pos','gramset_id','lang_id');
    }
     
    // Gramset __has_many__ Lemmas
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

    // Gramset __has_many__ Wordforms
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
        return $out;
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

        $gramsets = DB::table('gramsets')
                      ->join('gramset_pos', 'gramsets.id', '=', 'gramset_pos.gramset_id')
                      ->select('gramsets.id');
        if ($pos_id) {
            $gramsets = $gramsets->where('gramset_pos.pos_id',$pos_id);
        }
        if ($lang_id) {
            $gramsets = $gramsets->where('lang_id',$lang_id);
        }
         
        $gramsets = $gramsets->groupBy('gramsets.id')
                             ->orderBy('sequence_number')
                             ->get();
        
        $list = array();
        foreach ($gramsets as $row) {
            $gramset = self::find($row->id);
            $list[$row->id] = $gramset->gramsetString();
            if ($with_number) {
                $list[$row->id] = $gramset->sequence_number .'. '.$list[$row->id];
            }
        }
        
        return $list;         
    }
    
    /**
     * 1 - nominative, singular
     * 2 - nominative, plural
     * 3 - genetive, singular
     * 4 - partitive, singular
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
    public static function dictionaryGramsets($pos_id, $is_plural=NULL, $lang_id=5) {
        if (in_array($pos_id, PartOfSpeech::getNameIDs())) {
            if ($is_plural) {
                if ($lang_id == 1) { // vepsian
                    return [1=>22, 3=>2];
                } else {
                    return [0=>24, 1=>22, 3=>2];
                }
            } else {
                return [0=>3, 1=>4, 2=>22, 3=>1];
            }
        } elseif ($pos_id == PartOfSpeech::getVerbID()) { 
            return [0=>26, 1=>28, 2=>31, 3=>170];
        }
        return NULL;
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
