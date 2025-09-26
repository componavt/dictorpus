<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use DB;
use Storage;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

use App\Library\Grammatic;
//use App\Library\Str;

use App\Models\Corpus\Cyrtext;
use App\Models\Corpus\Sentence;
//use App\Models\Corpus\Source;
use App\Models\Corpus\Transtext;
use App\Models\Corpus\Word;

use App\Models\Dict\Meaning;
use App\Models\Dict\Wordform;

class Text extends Model implements HasMediaConversions
{
    const PhotoDisk = 'photos';
    const PhotoDir = 'photo';
    protected $fillable = ['lang_id', 'source_id', 'event_id', 'title', 'text',  //'corpus_id', 
                           'text_xml', 'text_structure', 'comment'];

    use \App\Traits\Export\TextExportExcel;
    use \App\Traits\Export\TextExport;
    use \App\Traits\Modify\TextModify;
    use \App\Traits\Search\TextSearch;
    use \App\Traits\Select\TextHistory;
    use \App\Traits\Select\TextSelect;
    use \App\Traits\Select\TextWordBlock;
    use \App\Traits\TextMarkup;
    use HasMediaTrait;
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = false; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 999999; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.


    public static function boot()
    {
        parent::boot();
    }
    
    public function registerMediaConversions()
    {
        $this->addMediaConversion('thumb')
             ->setHeight(200);
    }
    
    //Scopes
    use \App\Traits\Scopes\HasOneDialect;
    use \App\Traits\Scopes\DialectTexts;
    use \App\Traits\Scopes\InformantBirthPlace;
    use \App\Traits\Scopes\WithAudio;
    
    // Belongs To Relations
//    use \App\Traits\Relations\BelongsTo\Corpus;
    use \App\Traits\Relations\BelongsTo\Event;
    use \App\Traits\Relations\BelongsTo\Lang;
    use \App\Traits\Relations\BelongsTo\Source;
    use \App\Traits\Relations\BelongsTo\Transtext;

    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Authors;
    use \App\Traits\Relations\BelongsToMany\Corpuses;
    use \App\Traits\Relations\BelongsToMany\Cycles;
    use \App\Traits\Relations\BelongsToMany\Dialects;
    use \App\Traits\Relations\BelongsToMany\Genres;
    use \App\Traits\Relations\BelongsToMany\Motives;
    use \App\Traits\Relations\BelongsToMany\Plots;
    use \App\Traits\Relations\BelongsToMany\Topics;
    use \App\Traits\Relations\BelongsToMany\Toponyms;
//    use \App\Traits\Relations\BelongsToMany\Meanings;
    
    use \App\Traits\Relations\HasMany\Audiotexts;
    use \App\Traits\Relations\HasMany\Sentences;
    use \App\Traits\Relations\HasMany\Words;

    // Text __belongsToMany__ Wordforms
    public function wordforms(){
//        return $this->hasMany(Wordform::class);
        $builder = $this->belongsToMany(Wordform::class,'text_wordform')
                 ->withPivot('w_id', 'gramset_id', 'relevance');
        return $builder;
    }

    function getYearAttribute() {
        if (!$this->event) {
            return null;
        }
        return $this->event->date;
    }
    
    public function meanings(){
        $builder = $this->belongsToMany(Meaning::class)//;
                 -> withPivot('w_id')
                 -> withPivot('relevance'); 
        return $builder;
    }
    
    // Text __has_one__ Video
    public function video()
    {
        return $this->hasOne(Video::class);
    }
   
    // Text __has_one__ Cyrtext
    public function cyrtext()
    {
        return $this->hasOne(Cyrtext::class, 'id');
    }
   
    public function topics(){
        return $this->belongsToMany(Topic::class)->withPivot('sequence_number')
                    ->orderBy('text_topic.sequence_number');
    }
    
    public function topicValueWithNumber():Array{
        $value = [];
        if ($this->topics) {
            foreach ($this->topics as $topic) {
                $value[$topic->id] = $topic->pivot->sequence_number;
            }
        }
        return $value;
    }
    
    public function photoDir() {
        return Storage::url(self::PhotoDir).'/';
    }

    public function getSpeechAttribute()
    {
        if (!$this->event) { return null; }
        
        $informant = $this->event->informants()->first();

        if (!$informant) { return null; }

        return $informant->birth_place;
    }
    
    public function authorsToString() {
        $authors = [];
        foreach ($this->authors as $author) {
            $name = $author->getNameByLang($this->lang_id);
            $authors[] = $name ? $name : $author->name;
        }
        return join(', ', $authors);
    }
    
    public function newAudiotextName() {
        $count = 1;
        while ($this->audiotexts()->whereFilename($this->id.'_'.$count.'.mp3')->count()) {
            $count++;
        }
        return $this->id.'_'.$count.'.mp3';
    }
    
    public function markedWords($status='all') {
        $text_id = $this->id;
        return $this->words()->whereIn('id', function ($q) use ($status, $text_id) {
            $q->select('word_id')->from('meaning_text')
              ->whereTextId($text_id);
            if ($status=='checked') {
                $q->where('relevance','>','1');
            }
        });
    }
   
    /**
     * select * from `words` where `text_id` = 1548 and `w_id` in (select `w_id` from `text_wordform` where `text_id` = 1548 and `relevance` > 0 and `wordform_id` in (select `wordform_id` from `lemma_wordform` where `lemma_id` in (select `id` from `lemmas` where `lemma_for_search` like 'paha'))) order by `s_id` asc, `w_id` asc
     * 
     * @param array $words
     * @return collection
     */
    public function getWords($sentence_builder, $word_nums) {
        $out = $sentence_builder->where('t2.text_id', $this->id)->pluck('w1_id');
        for ($i=2; $i<=$word_nums; $i++) {
            $out = array_merge($out, 
                    $sentence_builder->where('t2.text_id', $this->id)->pluck('w2_id')); 
        }
        return $out;
    }
    
    /**
     * select id from sentences where `text_id` = 1548 and id in (select sentence_id from `words` where `text_id` = 1548 and `w_id` in (select `w_id` from `text_wordform` where `text_id` = 1548 and `relevance` > 0 and `wordform_id` in (select `wordform_id` from `lemma_wordform` where `lemma_id` in (select `id` from `lemmas` where `lemma_for_search` like 'paha'))) order by `s_id` asc, `w_id` asc);
     * 
     * @param array $words
     * @return collection
     */
    
    public function getSentencesByGram($words) {
        $text_id = $this->id;
        $builder = Sentence::whereTextId($text_id)->orderBy('s_id')   
                    ->whereIn('id', Sentence::searchWords($words, [$text_id])->pluck('t1.sentence_id'));
//dd($builder->toSql());                    
        return $builder->get();
    }
    
    public function getSentencesByIds($sentences_id) {
        $text_id = $this->id;
        $builder = Sentence::whereTextId($text_id)->orderBy('s_id')   
                    ->whereIn('s_id', $sentences_id);
//dd($builder->toSql());                    
        return $builder->get();
    }
    
    public function getCollectionId() {
        $genre_ids = $this->genres()->pluck('genre_id')->toArray();
        $author_ids = $this->authors()->pluck('id')->toArray();
        return Collection::getCollectionId($this->lang_id, $genre_ids, $author_ids);
    }

    public function processTextBeforeSaving($text) {
        $text = str_replace("&sub;b&sup;", "<b>", $text);
        $text = str_replace("&sub;/b&sup;", "</b>", $text);
        $text = str_replace("&sub;sup&sup;", "<sup>", $text);
        $text = str_replace("&sub;/sup&sup;", "</sup>", $text);
        return $text;
    }

    
    public function getMeaningsByWid($w_id) {
        $meanings = $this->meanings();
//var_dump($meanings);        
        /*->wherePivot('text_id',$this->id)
                         ->wherePivot('w_id',$w_id)
                         ->wherePivot('relevance','>',0)->get();
        */
    }
    
    // get old checked links meaning-text
    public function checkedMeaningRelevances($w_id, $word) {
        $relevances = [];
        $meanings = $this->meanings()->wherePivot('w_id',$w_id)
                         ->wherePivot('relevance','<>',1)->get();
     
        foreach ($meanings as $meaning) {
            $relevances[$meaning->id] = $meaning->pivot->relevance;
        }
        return $relevances;
    }
    
    // get old checked links text-wordform
    public function checkedWordformRelevances($w_id, $word) {
        $relevances = [];
        $wordforms = $this->wordforms()->wherePivot('w_id',$w_id)
                          ->wherePivot('relevance','<>',1)->get();
        foreach ($wordforms as $wordform) {
            $relevances[$wordform->id.'_'.$wordform->pivot->gramset_id]
                       = $wordform->pivot->relevance;
        }
        return $relevances;
    }
    
    public function hasImportantExamples() {
        if ($this->meanings()->whereRelevance(10)->count()>0) {
            return true;
        }
        $text_id = $this->id;
        
        $fragments_count = SentenceFragment::whereIn('sentence_id', function ($q) use ($text_id) {
                $q ->select('id')->from('sentences')
                   ->whereTextId($text_id);
            })->count();
        if ($fragments_count) {
            return true;
        }
        
        $translations_count = SentenceTranslation::whereIn('sentence_id', function ($q) use ($text_id) {
                $q ->select('id')->from('sentences')
                   ->whereTextId($text_id);
            })->count();
        if ($translations_count) {
            return true;
        }
    }

    /**
     * Search wordforms with gramsets matched with $word
     * @param String $word  in lower case
     * @param Int $lang_id
     * @return Collection
     */
    public static function getWordformsByWord($word, $lang_id) {
// TODO BEFORE COMLETION        
        $wordforms = Wordform::where('lemma_wordform.wordform_for_search', 'like', $word)
                   ->join('lemma_wordform','lemma_wordform.wordform_id', '=', 'wordforms.id')
                   ->whereNotNull('gramset_id')
                   ->whereIn('lemma_id', function ($query) use ($lang_id) {
                       $query->select('id')->from('lemmas')->whereLangId($lang_id);
                   })->get();    
        return $wordforms;
    }
    
    public function searchToMerge($sxe, $last_w_id, $last_word, $left_words) {
/*        $words[$last_w_id] = $last_word;
        $wordform_is_exist = true;
        $ids = array_keys($left_words);
        $i=sizeof($left_words)-1;
        while ($wordform_is_exist && $i >=0) {
            $w_id = $ids[$i];
            $word_with_left = $left_words[$w_id]. ' '.  join(' ',$words);
            $lemma_count = Lemma::where('lemma','like',$word_with_left)->count();
            $wordforms_count = Wordform::where('wordform','like',$word_with_left)->count();
            if (!$lemma_count && !$wordforms_count) {
                $wordform_is_exist = false;
            } else {
                $words = [$w_id =>$left_words[$w_id]] + $words;
            }
            $i--;
        }
        
        if ($wordform_is_exist && sizeof($words)>1) {
            list($sxe,$last_word)=$this->mergeNodes($sxe, $words);
        } */
        return [$sxe, $last_word];
    }
    
    /**
     * Gets identifier of the sentence by ID of word in the text

     * @param $w_id INT identifier of the word in the text
     * @return Int identifier of the sentence
     **/
    public function getSentenceID($w_id){
        $sentence = Word::select('s_id')
                        ->where('text_id',$this->id)
                        ->where('w_id',$w_id)->first();
        return $sentence->s_id;
    }

/*    public function addMeaningsBlock($word, $s_id, $word_id, $meanings_checked, $meanings_unchecked, $with_edit=null) {
        $word_class = 'lemma-linked';
        $link_block = $word->addChild('div');
        $link_block->addAttribute('id','links_'.$word_id);
        $link_block->addAttribute('class','links-to-lemmas');
        $link_block->addAttribute('data-downloaded',0);
        
        $load_img = $link_block->addChild('img');
        $load_img->addAttribute('class','img-loading');
        $load_img->addAttribute('src','/images/waiting_small.gif');
                
        $has_checked_meaning = false;
        if ($meanings_checked) {
            $word_class .= ' meaning-checked';
        } elseif ($meanings_unchecked>1) {
            $word_class .= ' polysemy';                
        } else {
            $word_class .= ' meaning-not-checked';
        }
        
        $wordforms = $this->wordforms()->wherePivot('w_id',$word_id);
        if (!$wordforms->wherePivot('relevance', '>', 0)->count()) {
            $word_class .= ' no-gramsets';
        } elseif ($wordforms->wherePivot('relevance',2)->count()) {
            $word_class .= ' gramset-checked';
        } else { 
            $word_class .= ' gramset-not-checked';
        }
        if (User::checkAccess('corpus.edit') && $with_edit) { // icon 'pensil'
            $link_block = self::addEditExampleButton($link_block, $this->id, $s_id, $word_id);
        }
        
        return [$word, $word_class];        
    }
*/

    /**
     * choose all sentences and all words
     * if a word is given then choose only sentences, containing the word, and only given words
     * 
     * @param string $word
     * @return array
     * 
     * [<s_id> => ['w_id'=>[<w_id1>, <w_id2>], 's' => <sentence_xml>] ]
     */
    public function sentencesFromText($word=''){
        $sentences = [];
        
        $word_builder = Word::where('text_id',$this->id)
                              ->orderBy('s_id');
        if ($word) {
//            $sentence_builder = $sentence_builder->where('word','like',Grammatic::toSearchForm($word));
            $word_builder = $word_builder->where('word','like',Grammatic::changeLetters($word, $this->lang_id));
        }                                
//dd($word_builder->first()->sentence);        
        foreach ($word_builder->get() as $word) {
            $sentences[$word->s_id]['s']=$word->sentence->text_xml ?? '';
            $sentences[$word->s_id]['w_id'][]=$word->w_id;
        }
        
        return $sentences;
    }
    
    /**
     * find sentence in text, parse xml
     * 
     * @param int $text_id
     * @param int $s_id - number of sentence in the text
     * @param int $w_id - number of word in the text
     * @param INT OR Array $relevance - if function is called for one meaning, type is INT, else ARRAY
     * @return array f.e. ['s' => <sentence in xml format>, 
                           'sent_obj' => <sentence object>,
                           's_id' => <number of sentence in the text>,
                           'text' => <text object>, 
                           'trans_s' => <transtext sentence in xml format>,
                           'w_id' => <number of word in the text>, 
                            'relevance' => <relevance>]
     */
    public static function extractSentence($text_id, $s_id, $w_id, $relevance='') {
        $text = self::find($text_id);
        $sent_obj = Sentence::getBySid($text_id, $s_id);
        if (!$text || !$sent_obj) { return NULL; }
        
        return ['s' => preg_replace('/[¦^]/', '', $sent_obj->text_xml), 
                'sent_obj' => $sent_obj,
                's_id' => $s_id,
                'text' => $text, 
                'trans_s' => $text->getTransSentence($s_id),
                'cyr_s' => $text->getCyrSentence($s_id),
                'w_id' => $w_id, 
                'relevance' => $relevance]; 
    }
    
    public function getTransSentence($s_id) {
        $transtext = Transtext::find($this->transtext_id);
        $trans_s = '';
        if ($transtext) {
            list($trans_sxe,$trans_error) = self::toXML($transtext->text_xml,'trans: '.$transtext->id);
            if (!$trans_error) {
                $trans_sent = $trans_sxe->xpath('//s[@id="'.$s_id.'"]');
                if (isset($trans_sent[0])) {
                    $trans_s = $trans_sent[0]->asXML();
                }
            }                    
        }
        return mb_ereg_replace('[¦^]', '', $trans_s);
    }

    public function getCyrSentence($s_id) {
        $cyr_s = '';
        if (!empty($this->cyrtext)) {
            $cyrtext = $this->cyrtext;
            list($cyr_sxe,$cyr_error) = self::toXML($cyrtext->text_xml,'trans: '.$this->id);
            if (!$cyr_error) {
                $cyr_sent = $cyr_sxe->xpath('//s[@id="'.$s_id.'"]');
                if (isset($cyr_sent[0])) {
                    $cyr_s = $cyr_sent[0]->asXML();
                }
            }                    
        }
        return mb_ereg_replace('[¦^]', '', $cyr_s);
    }

    public static function countExamples(){
//        $examples = DB::table('meaning_text')->groupBy('text_id', 'w_id')->get(['text_id', 'w_id']);
//        return sizeof($examples);
        return DB::table('meaning_text')->count();
    }
          
    public static function countCheckedExamples(){
        return DB::table('meaning_text')->where('relevance','<>',1)->count();
    }
          
    public static function countCheckedWords($lang_id=null){
        $texts = DB::table('meaning_text')->select('text_id', 'w_id')
                 ->where('relevance','>',1);
        if ($lang_id) {
            $texts=$texts -> whereIn('text_id', function ($q) use ($lang_id) {
                $q->select('id')->from('texts')->where('lang_id',$lang_id);
            });
        }
        
        return $texts->distinct()->count();
    }
    
    public static function videoForStart() {
        $texts = [1859, 2070, 1616, 1601];
//date_default_timezone_set('europe/lisbon');
        $date1 = new \DateTime('2018-10-07');
        $date2 = new \DateTime('now');

        $n = $date2->diff($date1)->format("%a") % sizeof($texts);
        
        $text = self::find($texts[$n]);
        if (!$text) { return; }
        
        return $text->video;        
    }
    
    public static function countFrequencySymbols($lang_id) {
        $symbols = [];
        $texts = Text::where('lang_id', $lang_id)->get(['text']);
        foreach ($texts as $text) {
            $text_symbols = preg_split("//u", $text->text);
//dd($text_symbols);    
            foreach ($text_symbols as $symbol) {
                if (!in_array($symbol, ['', ' ', "\r", "\n"])) {
                    $symbols[$symbol]=isset($symbols[$symbol]) ? $symbols[$symbol]+1 : 1;
                }
            }
        }
//        ksort($symbols);
        arsort($symbols);
//dd($symbols);
        return $symbols;
    }
    
    public function getPhotoFiles() {
        $all_files = Storage::disk(self::PhotoDisk)->files();
        $files = [];
        foreach ($all_files as $filename) {
            if (preg_match('/^'.$this->id.'_/', $filename)) {
                $files[$filename] = Storage::disk(self::PhotoDisk)->exists('big/'.$filename) ? 'big/'.$filename : '';
            }
        }
        return $files;
    }
    
    /**
     * count the number of texts by year of recording, publication and creation to VepKar
     * 
     * select date, count(*) from texts, events where texts.event_id=events.id group by date order by date;
     * select year, count(*) from texts, sources where texts.source_id=sources.id group by year order by year;
     * select year(created_at) as year, count(*) from texts group by year order by year; 
     * 
     * @return array ['year of recording' => [<year> => <number_of_texts>, ... ], ... ]
     */
    public static function countTextsByYears() {
        $out = [];

        $by_record = self::selectRaw("date, count(*) as count")
                         ->join('events', 'texts.event_id', '=', 'events.id')
                         ->groupBy('date')
                         ->orderBy('date')
                         ->get();
        foreach ($by_record as $rec) {
            $out['recording_year'][$rec->date] = number_format($rec->count, 0, ',', ' ');
        }        
        
        $by_publ = DB::table('texts')->selectRaw("year, count(*) as count")
                         ->join('sources', 'texts.source_id', '=', 'sources.id')
                         ->groupBy('year')
                         ->orderBy('year')
                         ->get();
        foreach ($by_publ as $rec) {
            $out['source_year'][$rec->year] = number_format($rec->count, 0, ',', ' ');
        }        
//dd($out['source_year']);
        $by_creation = DB::table('texts')->selectRaw("year(created_at) as year, count(*) as count")
                         ->groupBy('year')
                         ->orderBy('year')
                         ->get();
//dd($by_creation);        
        foreach ($by_creation as $rec) {
//dd($rec, $rec->year);            
            $out['creation_date'][$rec->year] = number_format($rec->count, 0, ',', ' ');
        }    
//dd($out['creation_date']);        
        $years = array_unique(array_merge(array_keys($out['recording_year']), array_keys($out['source_year']),array_keys($out['creation_date'])));
        sort($years);
        $text_years=[];
        foreach ($years as $year) {
            foreach (array_keys($out) as $label) {
//                foreach ($label_year as $old_year => $num) {
                    $text_years[\Lang::trans('corpus.'.$label)][$year ? $year : 'unknown'] = $out[$label][$year] ?? 0;
//                }
            }
        }
//dd($text_years);        
        return $text_years;
    }
    
    public function plotsToArray($link=null) {
        return $this->relationsToArr('plots', $link);
    }
    
    public function plotsToString($link=null) {
        return $this->relationsToString('plots', $link);
    }
    
    public function motivesToString($link=null, $div='<br>', $with_code=false) {
        return $this->relationsToString('motives', $link, $with_code ? 'full_name_with_code' : 'full_name', $div);
    }
    
    public function relationsToArr($relation_name, $link=null, $name_field='name') {
        $out = [];
        foreach ($this->{$relation_name} as $relation) {
            $name = $relation->{$name_field};
            if ($link) {
                $name = to_link($name, $link.$relation->id);
            }
            $out[] = $name;
        }
        return $out;
    }
    
    public function relationsToString($relation_name, $link=null, $name_field='name', $div=', ') {
        return join($div, $this->relationsToArr($relation_name, $link, $name_field));
    }
    
    public function cyclesToString($link=null) {
        return $this->relationsToString('cycles', $link);
    }
    
    public function topicsToArray($link=null) {
        return $this->relationsToArr('topics', $link);
    }           
}
