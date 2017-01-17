<?php
namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;

class Gramset extends Model
{
    public $timestamps = false;
    protected $fillable = ['gram_id_number', 'gram_id_case', 'gram_id_tense', 'gram_id_person', 'gram_id_mood', 
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
    
    public function gramReflexive()
    {
        return $this->belongsTo(Gram::class, 'gram_id_reflexive');
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
    public function gramsetString(String $glue=', ') : String
    {
        $list = array();
        if ($this->gram_id_person){
            $list[] = $this->gramPerson->name_short;
        }
            
        if ($this->gram_id_case){
            $list[] = $this->gramCase->name_short;
        }
            
        if ($this->gram_id_number){
            $list[] = $this->gramNumber->name_short;
        }
            
        if ($this->gram_id_tense){
            $list[] = $this->gramTense->name_short;
        }
            
        if ($this->gram_id_mood){
            $list[] = $this->gramMood->name_short;
        }
            
        return join($glue, $list);
    }
    
    /** Gets ordered list of gramsets for the part of speech
     * 
     * @param int $pos_id
     * @return Array [1=>'ед. ч., номинатив',..]
     */
    public static function getList(int $pos_id)
    {
        // select id from gramsets,gramset_pos where gramset_pos.gramset_id=gramsets.id and gramset_pos.pos_id=5 group by id order by sequence_number;

        $gramsets = DB::table('gramsets')
                      ->join('gramset_pos', 'gramsets.id', '=', 'gramset_pos.gramset_id')
                      ->select('gramsets.id')
                      ->where('gramset_pos.pos_id',$pos_id)
                      ->groupBy('gramsets.id')
                      ->orderBy('sequence_number')
                      ->get();
        
        $list = array();
        foreach ($gramsets as $row) {
            $gramset = self::find($row->id);
            $list[$row->id] = $gramset->gramsetString();
        }
        
        return $list;         
    }


    /** Takes data from search form (part of speech, language) and 
     * returns string for url such_as 
     * pos_id=$pos_id&lang_id=$lang_id
     * IF value is empty, the pair 'argument-value' is ignored
     * 
     * @param Array $url_args - array of pairs 'argument-value', f.e. ['pos_id'=>11, lang_id=>1]
     * @return String f.e. 'pos_id=11&lang_id=1'
     */
    public static function searchValuesByURL(Array $url_args=NULL) : String
    {
        $url = '';
        if (isset($url_args) && sizeof($url_args)) {
            $tmp=[];
            foreach ($url_args as $a=>$v) {
                if ($v!='') {
                    $tmp[] = "$a=$v";
                }
            }
            if (sizeof ($tmp)) {
                $url .= "?".implode('&',$tmp);
            }
        }
        
        return $url;
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
