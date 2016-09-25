<?php
namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;

class Gramset extends Model
{
    public $timestamps = false;
    
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
    
    // Gramset __has_many__ PartOfSpeech
    public function parts_of_speech()
    {
        return $this->belongsToMany(PartOfSpeech::class,'gramset_pos','gramset_id','pos_id');
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
            
        if ($this->gram_id_number){
            $list[] = $this->gramNumber->name_short;
        }
            
        if ($this->gram_id_case){
            $list[] = $this->gramCase->name_short;
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

    in mysql console:
        insert into `grams` values (24, 3, 'PRS', 'present', 'наст. вp.', 'презенс', 1);
        insert into `grams` values (25, 3, 'PST', 'past', 'прош. вр.', 'претерит', 2);
        insert into `grams` values (26, 3, 'FUT', 'future', 'буд. вр.', 'футурум', 3);

        insert into `gram_categories` values (5, 'mood','наклонение');

        insert into `grams` values (27, 5, 'ind', 'indicative', 'из.н.', 'индикатив', 1);
        insert into `grams` values (28, 5, 'cond', 'conditional', 'усл.н.', 'субъюнктив', 2);
        insert into `grams` values (29, 5, 'imp', 'imperative', 'пов.н', 'императив', 3);

        alter table `gramsets` add `gram_id_mood` smallint(5) unsigned default null after `gram_id_person`;
        ALTER TABLE `gramsets` ADD CONSTRAINT `gramsets_gram_id_mood_foreign` FOREIGN KEY (`gram_id_mood`) REFERENCES `grams` (`id`);

INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (26,11,1,NULL,24,21,27,2);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (27,11,1,NULL,24,22,27,3);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (28,11,1,NULL,24,23,27,4);

INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (29,11,2,NULL,24,21,27,5);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (30,11,2,NULL,24,22,27,6);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (31,11,2,NULL,24,23,27,7);

INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (32,11,1,NULL,25,21,27,11);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (33,11,1,NULL,25,22,27,12);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (34,11,1,NULL,25,23,27,13);

INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (35,11,2,NULL,25,21,27,14);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (36,11,2,NULL,25,22,27,15);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (37,11,2,NULL,25,23,27,16);

INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (38,11,1,NULL,24,21,28,26);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (39,11,1,NULL,24,22,28,27);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (40,11,1,NULL,24,23,28,28);

INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (41,11,2,NULL,24,21,28,29);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (42,11,2,NULL,24,22,28,30);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (43,11,2,NULL,24,23,28,31);

INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (44,11,1,NULL,25,21,28,34);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (45,11,1,NULL,25,22,28,35);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (46,11,1,NULL,25,23,28,36);

INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (47,11,2,NULL,25,21,28,37);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (48,11,2,NULL,25,22,28,38);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (49,11,2,NULL,25,23,28,39);

INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (50,11,1,NULL,NULL,21,29,40);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (51,11,1,NULL,NULL,22,29,41);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (52,11,1,NULL,NULL,23,29,42);

INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (53,11,2,NULL,NULL,21,29,43);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (54,11,2,NULL,NULL,22,29,44);
INSERT INTO `gramsets` (`id`, `pos_id_debug`, `gram_id_number`, `gram_id_case`, `gram_id_tense`, `gram_id_person`, `gram_id_mood`, `sequence_number`) VALUES (55,11,2,NULL,NULL,23,29,45);

INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (26,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (27,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (28,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (29,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (30,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (31,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (32,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (33,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (34,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (35,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (36,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (37,11);

INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (38,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (39,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (40,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (41,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (42,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (43,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (44,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (45,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (46,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (47,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (48,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (49,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (50,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (51,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (52,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (53,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (54,11);
INSERT INTO `gramset_pos` (`gramset_id`, `pos_id`) VALUES (55,11);
*/
