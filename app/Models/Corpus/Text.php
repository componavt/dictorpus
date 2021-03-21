<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use DB;
use LaravelLocalization;
use \Venturecraft\Revisionable\Revision;
//use DOMDocument;

use App\Library\Grammatic;
use App\Library\Str;

use App\Models\User;

use App\Models\Corpus\Event;
use App\Models\Corpus\Sentence;
use App\Models\Corpus\Source;
use App\Models\Corpus\Transtext;
use App\Models\Corpus\Word;

//use App\Models\Dict\Lang;
use App\Models\Dict\Meaning;
//use App\Models\Dict\MeaningText;
use App\Models\Dict\Wordform;

class Text extends Model
{
    protected $fillable = ['corpus_id', 'lang_id', 'source_id', 'event_id', 
                        'title', 'text', 'text_xml', 'text_structure'];

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
    use \App\Traits\Relations\BelongsTo\Corpus;
    use \App\Traits\Relations\BelongsTo\Event;
    use \App\Traits\Relations\BelongsTo\Lang;
    use \App\Traits\Relations\BelongsTo\Source;
    use \App\Traits\Relations\BelongsTo\Transtext;

    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Authors;
    use \App\Traits\Relations\BelongsToMany\Dialects;
    use \App\Traits\Relations\BelongsToMany\Genres;
//    use \App\Traits\Relations\BelongsToMany\Meanings;
    
    use \App\Traits\Relations\HasMany\Words;

    // Text __belongsToMany__ Wordforms
    public function wordforms(){
//        return $this->hasMany(Wordform::class);
        $builder = $this->belongsToMany(Wordform::class,'text_wordform')
                 ->withPivot('w_id', 'gramset_id', 'relevance');
        return $builder;
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
   
    public function authorsToString() {
        $authors = [];
        foreach ($this->authors as $author) {
            $name = $author->getNameByLang($this->lang_id);
            $authors[] = $name ? $name : $author->name;
        }
        return join(', ', $authors);
    }

    public function addMeaning($meaning_id, $sentence_id, $word_id, $w_id, $relevance) {
                        $this->meanings()->attach($meaning_id,
                                ['sentence_id'=>$sentence_id,
                                 'word_id'=>$word_id,
                                 'w_id'=>$w_id,
                                 'relevance'=>$relevance]);        
    }
    
    public static function search(Array $url_args) {
        // select * from `texts` where (`transtext_id` in (select `id` from `transtexts` where `title` = '%nitid_') or `title` like '%nitid_') and `lang_id` = '1' order by `title` asc limit 10 offset 0
        // select texts by title from texts and translation texts
        $texts = self::orderBy('title');        
        $texts = self::searchByAuthor($texts, $url_args['search_author']);
//        $texts = self::searchByAuthors($texts, $url_args['search_author']);
        $texts = self::searchByBirthPlace($texts, $url_args['search_birth_place']);
        $texts = self::searchByDialects($texts, $url_args['search_dialect']);
        $texts = self::searchByInformant($texts, $url_args['search_informant']);
        $texts = self::searchByLang($texts, $url_args['search_lang']);
        $texts = self::searchByPlace($texts, $url_args['search_place']);
        $texts = self::searchByRecorder($texts, $url_args['search_recorder']);
        $texts = self::searchByTitle($texts, $url_args['search_title']);
        $texts = self::searchByWid($texts, $url_args['search_wid']);
        $texts = self::searchByWord($texts, $url_args['search_word']);
        $texts = self::searchByText($texts, $url_args['search_text']);
        $texts = self::searchByGenres($texts, $url_args['search_genre']);
        $texts = self::searchByYear($texts, $url_args['search_year_from'], $url_args['search_year_to']);
        
        if ($url_args['search_corpus']) {
            $texts = $texts->whereIn('corpus_id',$url_args['search_corpus']);
        } 
/*
        if ($url_args['search_text']) {
            $texts = $texts->where('text','like','%'.$url_args['search_text'].'%');
        } */
//dd($texts->toSql());                                

        return $texts;
    }

    public static function searchByBirthPlace($texts, $place) {
        if (!$place) {
            return $texts;
        }
        return $texts->whereIn('event_id',function($query) use ($place){
                    $query->select('event_id')
                    ->from('event_informant')
                    ->whereIn('informant_id',function($query) use ($place){
                        $query->select('id')
                        ->from('informants')
                        ->where('birth_place_id',$place);
                    });
                });
    }
    
    public static function searchByDialects($texts, $dialects) {
        if (!sizeof($dialects)) {
            return $texts;
        }
        return $texts->whereIn('id',function($query) use ($dialects){
                    $query->select('text_id')
                    ->from("dialect_text")
                    ->whereIn('dialect_id',$dialects);
                });
    }
    
    public static function searchByGenres($texts, $genres) {
        if (!sizeof($genres)) {
            return $texts;
        }
        return $texts->whereIn('id',function($query) use ($genres){
                    $query->select('text_id')
                    ->from("genre_text")
                    ->whereIn('genre_id',$genres);
                });
    }
    
    public static function searchByAuthor($texts, $author) {
        if (!$author) {
            return $texts;
        }
        return $texts->whereIn('id',function($query) use ($author){
                    $query->select('text_id')
                    ->from("author_text")
                    ->where('author_id',$author);
                });
    }
/*    
    public static function searchByAuthors($texts, $authors) {
        if (!sizeof($authors)) {
            return $texts;
        }
        return $texts->whereIn('id',function($query) use ($authors){
                    $query->select('text_id')
                    ->from("author_text")
                    ->whereIn('author_id',$authors);
                });
    }
*/    
    public static function searchByInformant($texts, $informant) {
        if (!$informant) {
            return $texts;
        }
        return $texts->whereIn('event_id',function($query) use ($informant){
                    $query->select('event_id')
                    ->from('event_informant')
                    ->where('informant_id',$informant);
                });
    }
    
    public static function searchByLang($texts, $langs) {
        if (!sizeof($langs)) {
            return $texts;
        }
        return $texts->whereIn('lang_id',$langs);
    }
    
    public static function searchByPlace($texts, $place) {
        if (!$place) {
            return $texts;
        }
        return $texts->whereIn('event_id',function($query) use ($place){
                    $query->select('id')
                    ->from('events')
                    ->where('place_id',$place);
                });
    }
    
    public static function searchByRecorder($texts, $recorder) {
        if (!$recorder) {
            return $texts;
        }
        return $texts->whereIn('event_id',function($query) use ($recorder){
                    $query->select('event_id')
                    ->from('event_recorder')
                    ->where('recorder_id',$recorder);
                });
    }
    
    public static function searchByTitle($texts, $title) {
        if (!$title) {
            return $texts;
        }
        return $texts->where(function($q) use ($title){
                        $q->whereIn('transtext_id',function($query) use ($title){
                            $query->select('id')
                            ->from(with(new Transtext)->getTable())
                            ->where('title','like', $title);
                        })->orWhere('title','like', $title);
                });
                       //->whereOr('transtexts.title','like', $text_title);
    }

    public static function searchByWord($texts, $word) {
        if (!$word) {
            return $texts;
        }
        return $texts->whereIn('id',function($query) use ($word){
                                $query->select('text_id')
                                ->from('words')
//                                ->where('word','like', $word);
                                ->where('word', 'like', Grammatic::toSearchForm($word));
                            });
    }

    public static function searchByWid($texts, $w_id) {
        if (!$w_id) {
            return $texts;
        }
        return $texts->whereIn('id',function($query) use ($w_id){
                                $query->select('text_id')
                                ->from('words')
                                ->where('w_id', $w_id);
                            });
    }

    public static function searchByText($texts, $str) {
        if (!$str) {
            return $texts;
        }
        return $texts->where(function($q) use ($str){
                        $q->whereIn('transtext_id',function($query) use ($str){
                            $query->select('id')
                            ->from(with(new Transtext)->getTable())
                            ->where('text','like', '%'.$str.'%');
                        })->orWhere('text','like', '%'.$str.'%');
                });
                       //->whereOr('transtexts.title','like', $text_title);
    }

    public static function searchByYear($texts, $year_from, $year_to) {
        if (!$year_from && !$year_to) {
            return $texts;
        }
        $year_from = $year_from ? $year_from : 1;
        $year_to = $year_to ? $year_to : 1000;
        
        return $texts->where(function ($q) use ($year_from, $year_to) {
                $q->whereIn('event_id',function($query) use ($year_from, $year_to){
                    $query->select('id')->from('events')
                    ->where('date', '>=', $year_from)
                    ->where('date', '<=', $year_to);
                   })->orWhereIn('source_id',function($query) use ($year_from, $year_to){
                    $query->select('id')->from('sources')
                    ->where('year', '>=', $year_from)
                    ->where('year', '<=', $year_to);
                   });
        });
    }
    
    public static function updateByID($request, $id) {
        $request['text'] = self::process($request['text']);
        
        $text = self::with('transtext','event','source')->get()->find($id);
        $old_text = $text->text;
//dd($request->text);

        $text->fill($request->only('corpus_id','lang_id','title','text','text_xml'));
//        $text->fill($request->only('corpus_id','lang_id','title','text_xml'));
//        $text->text = $text->processTextBeforeSaving($request->text);
//dd($text->text);
        $text->updated_at = date('Y-m-d H:i:s');
        $text->save();
        
        return $text -> storeAdditionInfo($request, $old_text);
    }
    
    public function storeAdditionInfo($request, $old_text=NULL){
        $error_message = '';
        $request['transtext_text'] = Text::process($request['transtext_text']);
        $request['event_date'] = (int)$request['event_date'];
        
        $this->storeVideo($request->youtube_id);
        $this->storeTranstext($request->only('transtext_lang_id','transtext_title','transtext_text','transtext_text_xml'));
        $this->storeEvent($request->only('event_place_id','event_date','event_informants','event_recorders'));
        $this->storeSource($request->only('source_title', 'source_author', 'source_year', 'source_ieeh_archive_number1', 'source_ieeh_archive_number2', 'source_pages', 'source_comment'));
        
        $this->authors()->detach();
        $this->authors()->attach($request->authors);

        $this->dialects()->detach();
        $this->dialects()->attach($request->dialects);

        $this->genres()->detach();
        $this->genres()->attach($request->genres);
        
        if ($request->text && ($old_text != $request->text || !$this->text_xml)) {
            $error_message = $this->markup();
        }

        $this->push();        
        
        return $error_message;
    }
    
    public function storeVideo($youtube_id) {
//dd($youtube_id);        
        if (!$youtube_id) {
            return;
        } else {
            $youtube_id = trim($youtube_id);
        }
        
        if ($this->video) {
            $this->video->youtube_id = $youtube_id;
            $this->video->save();
        } else {
            $video = Video::firstOrCreate(['text_id'=>$this->id]);
            $video->youtube_id = $youtube_id;
            $video->save();
            $this->video()->save($video);
        }
    }
    
    /**
     * Checks request data. If the request data is not null, 
     * updates Transtext if it exists or creates new and returns id of Transtext
     * 
     * If the request data is null and Transtext exists, 
     * destroy it and sets transtext_id in Text as NULL.
     * 
     * @return INT or NULL
     */
    public function storeTranstext($request_data){
        $is_empty_data = true;
        if ($request_data['transtext_title'] && $request_data['transtext_text']) {
            $is_empty_data = false;
        }
//dd($is_empty_data);
        if ($this) {
            $transtext_id = $this->transtext_id;
        } else {
            $transtext_id = NULL;
        }

        if (!$is_empty_data) {
            foreach (['lang_id','title','text'] as $column) {
                $data_to_fill[$column] = ($request_data['transtext_'.$column]) ? $request_data['transtext_'.$column] : NULL;
            }
            if ($transtext_id) {
               
                $transtext = Transtext::find($transtext_id);
                $old_text = $transtext->text;
                $transtext->fill($data_to_fill);
                if ($data_to_fill['text'] && ($old_text != $data_to_fill['text'] || !$transtext->text_xml)) {
                    $transtext->markup();
                }
                $transtext->save();
            } else {
                $transtext = Transtext::firstOrCreate($data_to_fill);

                if ($data_to_fill['text']) {
                    $transtext->markup();
                }
                $transtext->save();

                $this->transtext_id = $transtext->id;
                $this->save();
            }
            return $transtext->id;
            
        } elseif ($transtext_id) {
            $this->transtext_id = NULL;
            $this->save();
            if (!self::where('id','<>',$this->id)
                     ->where('transtext_id',$transtext_id)
                     ->count()) {
                Transtext::destroy($transtext_id);
            }
        }
    }    
    
    /**
     * Checks request data. If the request data is not null, 
     * updates Event if it exists or creates new and returns id of Event
     * 
     * If the request data is null and Event exists, 
     * destroy it and sets event_id in Text as NULL.
     * 
     * @return INT or NULL
     */
    public function storeEvent($request_data){
//dd($request_data);        
        if (!$this) { return; }
        $is_empty_data = true;
        if(array_filter($request_data)) {
            $is_empty_data = false;
        }

        $event_id = $this->event_id;
        if (!$is_empty_data) {
            $this->updateEvent($event_id, $request_data);
            return $this->event_id;
            
        } elseif ($event_id) {
            $this->removeEvent();
        }
    }    
    
    public function updateEvent($event_id, $request_data) {
        $data_to_fill = [];
        foreach (['place_id','date'] as $column) {//'informant_id',
            $data_to_fill[$column] = ($request_data['event_'.$column]) ? $request_data['event_'.$column] : NULL;
        }
//dd($data_to_fill);
        if ($event_id) {
            $event = Event::find($event_id);
            $is_possible_changed = $event->isPossibleChanged($this, $request_data);
//print "<pre>";
//var_dump($is_possible_changed);            
            if ($is_possible_changed==1) {
                $event->fill($data_to_fill);
                $event->save();
            } elseif ($is_possible_changed==0) {
                $event = $this->createEvent($data_to_fill);
//var_dump($this->event_id);
//var_dump($this->event);
            }
        } else {
            $event = $this->createEvent($data_to_fill);
        }
        //if (!$this->event) { return; }
        $event -> updateInformantsAndRecorders($request_data);
    }
    
    public function createEvent($data_to_fill) {
        $event = Event::create($data_to_fill);
        $this->event_id = $event->id;
        $this->save();  
//var_dump($event);
//var_dump($this->event);
        return $event;
    }
    
    public function removeEvent() {
        $event_id = $this->event_id;
        
        $this->event_id = NULL;
        $this->save();
        
        Event::removeUnused($event_id, $this->id);        
    }

    /**
     * Checks request data. 
     * If the request data is not null, 
     *  update source data
     * 
     * If the request data is null and Source exists, 
     *      destroys it and sets source_id in Text as NULL.
     * 
     * @return INT or NULL
     */
    public function storeSource($request_data){
        $is_empty_data = true;
        if(array_filter($request_data)) { // returns unempty items of array
            $is_empty_data = false;
        }
        if ($this) {
            $source_id = $this->source_id;
        } else {
            $source_id = NULL;
        }

        if (!$is_empty_data) {
            $this->source_id = Source::fillByData($source_id, $request_data);
            $this->save();
        } 
        elseif ($source_id) {
            $this->source_id = NULL;
            $this->save();
            
            if (!self::where('id','<>',$this->id)
                     ->where('source_id',$source_id)
                     ->count()) {
                Source::destroy($source_id);
            }
        }
    }    

    public function remove() {
        $this->dialects()->detach();
        $this->genres()->detach();
        $this->meanings()->detach();
        $this->wordforms()->detach();

        $this->words()->delete();
        $this->video()->delete();

        $this->delete();
    }    

    public static function removeAll($text) {
        $id = $text->id;
        $text_title = $text->title;

        $transtext_id = $text->transtext_id;
        $event_id = $text->event_id;
        $source_id = $text->source_id;

        $text->remove();

        Transtext::removeByID($transtext_id);
        Event::removeByID($event_id);
        Source::removeByID($source_id);

        return $text_title;
    }

    public function updateXML($text_xml) {
        $this->text_xml = $text_xml;
        $this->save();
    }
    
    /**
     * process string, replace simbols >, < on html-entities
     *
     * @param $str String 
     * @return String 
     */
    public static function process($str):String{
        $str = str_replace(">","&sup;",$str);
        $str = str_replace("<","&sub;",$str);
        $str = str_replace("&sub;b&sup;", "<b>", $str);
        $str = str_replace("&sub;/b&sup;", "</b>", $str);
        $str = str_replace("&sub;sup&sup;", "<sup>", $str);
        $str = str_replace("&sub;/sup&sup;", "</sup>", $str);
//        $str = str_replace(">","&gt;",$str);
//        $str = str_replace("<","&lt;",$str);
        return $str;
    }
    
    public function processTextBeforeSaving($text) {
        $text = str_replace("&sub;b&sup;", "<b>", $text);
        $text = str_replace("&sub;/b&sup;", "</b>", $text);
        $text = str_replace("&sub;sup&sup;", "<sup>", $text);
        $text = str_replace("&sub;/sup&sup;", "</sup>", $text);
        return $text;
    }

    
    public static function preProcessText($text) {
        $end1 = ['.','?','!','…','|'];
        $end2 = ['.»','?»','!»','."','?"','!"','.”','?”','!”','.“'];
        $pseudo_end = false;
        if (!in_array(mb_substr($text,-1,1),$end1) && !in_array(mb_substr($text,-1,2),$end2)) {
            $text .= '.';
            $pseudo_end = true;
        }
        return [nl2br($text), $pseudo_end];
    }

    /**
     * Gets a markup text with sentences
     *
     * @param string $text  text without mark up
     * @return string text with markup (split to sentences and words)
     */
    public static function markupText($text, $by_sentences=false)
    {
        list($text, $pseudo_end) = self::preProcessText(trim($text));
        
        $text_xml = '';
        $sen_count = $word_count = 1;
        $sentences = [];

        if (preg_match_all("/(.+?)(\||\.|\?|!|\.»|\?»|!»|\.\"|\?\"|!\"|\.”|\?”|!”|…{1,})(\s|(<br(| \/)>\s*){1,}|$)/is", // :|
                           $text, $desc_out)) {
            for ($k=0; $k<sizeof($desc_out[1]); $k++) {
                $sentence = trim($desc_out[1][$k]);

                // <br> in in the beginning of the string is moved before the sentence
                if (preg_match("/^(<br(| \/)>)(.+)$/is",$sentence,$regs)) {
                    $text_xml .= $regs[1]."\n";
                    $sentence = trim($regs[3]);
                }
                if ($k == sizeof($desc_out[1])-1 && $pseudo_end || $desc_out[2][$k] == '|') {
                    $desc_out[2][$k] = '';
                }

                // division on words
                list($str,$word_count) = Sentence::markup($sentence,$word_count);
//                $str = str_replace('¦', '', $str);
                $sentences[$sen_count] = "<s id=\"".$sen_count.'">'.$str.$desc_out[2][$k]."</s>\n";
                $text_xml .= $by_sentences ? "<s id=\"".$sen_count++.'"/>'
                                                 : $sentences[$sen_count];
                $div = trim($desc_out[3][$k]);
                $text_xml .= $div ? $div."\n" : '';
            }
        }

        return $by_sentences ? [trim($text_xml), $sentences] : trim($text_xml);
    }
    
    /**
     * Sets text_xml as a markup text with sentences
     */
    public function markup(){
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        list($this->text_structure, $sentences) = self::markupText($this->text, true);
        foreach ($sentences as $s_id => $text_xml) {
            Sentence::store($this->id, $s_id, $text_xml);
            $error_message = $this->updateMeaningAndWordformText($s_id, $text_xml);
            if ($error_message) {
                print $error_message;
            }
        }
        DB::statement("DELETE FROM sentences WHERE s_id>$s_id and text_id=".(int)$this->id);
    }

    public function getMeaningsByWid($w_id) {
        $meanings = $this->meanings();
//var_dump($meanings);        
        /*->wherePivot('text_id',$this->id)
                         ->wherePivot('w_id',$w_id)
                         ->wherePivot('relevance','>',0)->get();
        */
    }
    
    /**
     * Sets links meaning - text - sentence AND text-wordform
     */
    public function updateMeaningAndWordformText($s_id, $text_xml){
        list($sxe,$error_message) = self::toXML($text_xml, $s_id);
        if ($error_message) { return $error_message; }
//dd($text_xml);
        $checked_words = $this->checkedWords($text_xml);
//dd($checked_words);
        $where_text = "text_id=".(int)$this->id;
        DB::statement("DELETE FROM words WHERE sentence_id=$s_id and $where_text");
        DB::statement("DELETE FROM meaning_text WHERE sentence_id=$s_id and $where_text");
        DB::statement("DELETE FROM text_wordform WHERE w_id in (select w_id from words where sentence_id=$s_id and $where_text) and $where_text");            

//        $this->updateMeaningAndWordformSentence($s_id, $sxe->children()->w, 
        $this->updateMeaningAndWordformSentence($s_id, $sxe->xpath('//w'), 
                $checked_words ?? NULL);
    }
    
    /**
     * Sets ONLY links text-wordform
     */
    public function updateWordformLinks() {
        list($sxe,$error_message) = self::toXML($this->text_xml,$this->id);
        if ($error_message) { return $error_message; }

//        $checked_words = $this->checkedWords($this->text_xml, false);
//dd($checked_words);
//        DB::statement("DELETE FROM text_wordform WHERE text_id=".(int)$this->id);

        foreach ($sxe->children()->s as $sentence) {
            $s_id = (int)$sentence->attributes()->id;
            $this->updateWordformSentence($s_id, $sentence->children()->w);
        }
    }
    
    public function updateMeaningAndWordformSentence($s_id, $sent_words, $checked_sent_words, $set_meanings=true, $set_wordforms=true) {
        $word_count = 0;
        foreach ($sent_words as $word) {
//dd((string)$word);            
            $w_id = (int)$word->attributes()->id;
            $word_for_search = Grammatic::changeLetters((string)$word,$this->lang_id);
            
            if ($set_meanings) {
                $word_obj = Word::create(['text_id' => $this->id, 'sentence_id' => $s_id, 'w_id' => $w_id, 'word' => $word_for_search]);
            } else {
                $word_obj = Word::whereTextId($this->id)->whereWId($w_id)->first();
            }
            $the_same_word = isset($checked_sent_words[$word_count]['w']) && $word_for_search==$checked_sent_words[$word_count]['w'];
            if ($set_meanings) {
                $word_obj->setMeanings($the_same_word ? $checked_sent_words[$word_count]['meanings'] : [], $this->lang_id);
            }
            if ($set_wordforms) {
                $this->setWordforms($the_same_word ? $checked_sent_words[$word_count]['wordforms'] : [], $word_obj);
            }
            $word_count++;
        }
    }
    
    public function updateWordformSentence($s_id, $sent_words) {
        $word_count = 0;
        foreach ($sent_words as $word) {
            $w_id = (int)$word->attributes()->id;
            $word_obj = Word::whereTextId($this->id)->whereWId($w_id)->first();
            $word_obj->updateWordformText();
            $word_count++;
        }
    }
    
    // saving old checked links
    public function checkedWords($old_xml, $for_meanings=true, $for_wordforms=true) {
        $checked_words = [];
        if (!$old_xml) { return $checked_words; } 
        
        list($sxe_old,$error_message) = self::toXML($old_xml,$this->id);
        if (!$sxe_old || $error_message) { return $checked_words; } 

//        foreach ($sxe_old->children()->s as $sentence) {
//            $s_id = (int)$sentence->attributes()->id;
            $word_count = 0;
//dd($sxe_old->children()->w);            
            foreach ($sxe_old->xpath("//w") as $word) {
                $w_id = (int)$word->attributes()->id;
                $word_for_search = Grammatic::changeLetters((string)$word,$this->lang_id);
//                $checked_words[$s_id][$word_count]['w'] = $word_for_search;
                $checked_words[$word_count]['w'] = $word_for_search;
                if ($for_meanings) {
//                    $checked_words[$s_id][$word_count]['meanings']
                    $checked_words[$word_count]['meanings']
                            =$this->checkedMeaningRelevances($w_id, $word_for_search);
                }
                if ($for_wordforms) {
//                    $checked_words[$s_id][$word_count]['wordforms']
                    $checked_words[$word_count]['wordforms']
                            =$this->checkedWordformRelevances($w_id, $word_for_search);                
                }
                $word_count++;
//            }
        }        
        return $checked_words;
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
    
    /**
     * set links between a word (of some text) and a wordform-gramset in the dictionary
     * 
     * @param Array $checked_relevances [<wordform1_id>_<gramset1_id> => [word, relevance1], <wordform2_id>_<gramset2_id> => [word, relevance2], ... ]
     * @param INT $lang_id
     * $retutn INT - the number of links with meanings
     */
    public function setWordforms($checked_relevances, $word_obj) {
        if (in_array(2, array_values($checked_relevances))) {
            $has_checked = true;
        } else {
            $has_checked = false;
        }
        foreach ($this->getWordformsByWord($word_obj->word) as $wordform) {
            $wg_id = $wordform->id. '_'. $wordform->gramset_id;
            $relevance = $checked_relevances[$wg_id] ?? ($has_checked ? 0 : 1);
            $this->addWordform($wordform->id, $wordform->gramset_id, $word_obj->sentence_id, $word_obj->w_id, $relevance);
        }
    }

    /**
     * Search wordforms with gramsets matched with $word
     * @param String $word  in lower case
     * @param Int $lang_id
     * @return Collection
     */
    public function getWordformsByWord($word) {
        $lang_id = $this->lang_id;
// TODO BEFORE COMLETION        
        $wordforms = Wordform::where('lemma_wordform.wordform_for_search', 'like', $word)
                   ->join('lemma_wordform','lemma_wordform.wordform_id', '=', 'wordforms.id')
                   ->whereNotNull('gramset_id')
                   ->whereIn('lemma_id', function ($query) use ($lang_id) {
                       $query->select('id')->from('lemmas')->whereLangId($lang_id);
                   })->get();    
        return $wordforms;
    }
    
    public function addWordform($wordform_id, $gramset_id, $sentence_id, $w_id, $relevance) {
        if ($this->wordforms()->wherePivot('wordform_id',$wordform_id)
                 ->wherePivot('w_id',$w_id)
                 ->wherePivot('gramset_id',$gramset_id)->count()) {
            return;
        }
        $this->wordforms()->attach($wordform_id,
                ['w_id'=>$w_id,
                 'gramset_id' => $gramset_id,
                 'relevance'=>$relevance]);        
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
    
    public function mergeNodes($sxe, $words) {
        $word_ids = array_keys($words);
        $last_id = array_pop($word_ids);
        
        $last_node = $sxe->xpath("//w[@id='".$last_id."']");
        foreach ($word_ids as $word_id) {
            $node = $sxe->xpath("//w[@id='".$word_id."']");
            if ($node) {
                $last_node[0][0] = (string)$node[0].' '.(string)$last_node[0];
                unset($node[0][0]);
            }
            Word::removeByTextWid($this->id,$word_id);
        }
        return [$sxe, (string)$last_node[0]];
    }
    
    /**
     * Add link w_id (word from text) - meaning of lemma
     * 
     * @param Int $lemma - Lemma ID
     * @param Int $meaning_id - Meaning ID
     * @param Int $w_id - ID of word in the text
     * @param Word $word - Word Object
     */
    public function addLinkWithMeaning($lemma, $meaning_id, $w_id, $word){
        if (!$meaning_id) { return; }
        
        $meaning = Meaning::find($meaning_id);
        if (!$meaning) { return; }

        foreach ($lemma->meanings as $meaning) {
            DB::statement("DELETE FROM meaning_text WHERE text_id=".$this->id
                    . " and w_id=$w_id and meaning_id=".$meaning->id);
            if ($meaning->id == $meaning_id) {
                $relevance = 5;
            } else {
                $relevance = 0;
            }
            $this->meanings()->attach($meaning->id,
                    ['sentence_id'=>$word->sentence_id,
                     'word_id'=>$word->id, 'w_id'=>$w_id,
                     'relevance'=>$relevance]);            
        }
//dd($this->meanings()->wherePivot('w_id', $w_id)->get());        
    }
    
// select id from meanings where lemma_id in (SELECT id from lemmas where lemma like '$word_t' or id in (SELECT lemma_id FROM lemma_wordform WHERE wordform_id in (SELECT id from wordforms where wordform like '$word_t')))    
// select id from meanings where lemma_id in (SELECT id from lemmas where lemma like 'myö' or id in (SELECT lemma_id FROM lemma_wordform WHERE wordform_id in (SELECT id from wordforms where wordform like 'myö')));    

    /**
     * Load xml from string, create SimpleXMLelement
     *
     * @param $text_xml - markup text
     * @param id - identifier of object Text or Transtext
     *
     * return Array [SimpleXMLElement object, error text if exists]
     */
    public static function toXML($text_xml, $id){
        libxml_use_internal_errors(true);
        if (!preg_match("/^\<\?xml/", $text_xml)) {
            $text_xml = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>'.
                                     '<text>'.$text_xml.'</text>';
        }
//dd($text_xml);        
        $sxe = simplexml_load_string($text_xml);
//dd($text_xml);       
        $error_text = '';
        if (!$sxe) {
            $error_text = "XML loading error". '('.$id.")\n";
            foreach(libxml_get_errors() as $error) {
                $error_text .= "\t". $error->message;
            }
        }
        return [$sxe,$error_text];
    }

    /**
     * Gets identifier of the sentence by ID of word in the text

     * @param $w_id INT identifier of the word in the text
     * @return Int identifier of the sentence
     **/
    public function getSentenceID($w_id){
        $sentence = Word::select('sentence_id')
                        ->where('text_id',$this->id)
                        ->where('w_id',$w_id)->first();
        return $sentence->sentence_id;
    }

    /**
     * Gets markup text with links from words to related lemmas

     * @param string $markup_text 
     * @param string $search_word 
     * @return string markup text
     **/
    public function setLemmaLink($markup_text=null, $search_word=null, $search_sentence=null, $with_edit=true, $search_w=null){
        if (!$markup_text) {
            $markup_text = $this->text_xml;
        }
        list($sxe,$error_message) = self::toXML($markup_text,'');
        if ($error_message) {
            return $markup_text;
        }
        $sentences = $sxe->xpath('//s');
        foreach ($sentences as $sentence) {
                $sentence_id = (int)$sentence->attributes()->id;
//dd($sentence);     
            foreach ($sentence->children() as $word) {
                $word = $this->editWordBlock($word, $sentence_id, $search_word, $search_sentence, $with_edit, $search_w);
            }
            $sentence_class = "sentence";
            if ($search_sentence && $search_sentence==$sentence_id) {
                $sentence_class .= " word-marked";
            }
            $sentence->addAttribute('class',$sentence_class);
            $sentence->attributes()->id = 'text_s'.$sentence_id;
        }
        
        return $sxe->asXML();
    }

    public function editWordBlock($word, $sentence_id, $search_word=null, $search_sentence=null, $with_edit=null, $search_w=null) {
        $word_id = (int)$word->attributes()->id;
        if (!$word_id) { return $word; }
        
        $meanings_checked = $this->meanings()->wherePivot('w_id',$word_id)
                          ->wherePivot('relevance', '>', 1)->count();
        $meanings_unchecked = $this->meanings()->wherePivot('w_id',$word_id)
                          ->wherePivot('relevance', 1)->count();
        $word_class = '';
        if ($meanings_checked || $meanings_unchecked) {
            list ($word, $word_class) = $this->addMeaningsBlock($word, $sentence_id, 
                    $word_id, $meanings_checked, $meanings_unchecked, $with_edit);
            
        } elseif (User::checkAccess('corpus.edit')) {
            $word_class = 'lemma-linked call-add-wordform';
        }

//        if ($search_word && Grammatic::toSearchForm((string)$word) == $search_word 
        if ($search_word && Grammatic::changeLetters((string)$word, $this->lang_id) == $search_word 
                || $search_w && $word_id==$search_w) {
            $word_class .= ' word-marked';
        }

        if ($word_class) {
            $word->addAttribute('class',$word_class);
        }
        return $word;
    }

    public function addMeaningsBlock($word, $sentence_id, $word_id, $meanings_checked, $meanings_unchecked, $with_edit=null) {
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
            $link_block = self::addEditExampleButton($link_block, $this->id, $sentence_id, $word_id);
        }
        
        return [$word, $word_class];        
    }

    public static function addLinkToLemma($link_block, $lemma, $meaning, $id, $has_checked_meaning, $with_edit) {
        $link_div = $link_block->addChild('div');
        $link = $link_div->addChild('a',$lemma->lemma);
        $link->addAttribute('href',LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id));

        $locale = LaravelLocalization::getCurrentLocale();
        $link->addChild('span',' ('.$meaning->getMultilangMeaningTextsString($locale).')');
        // icon 'plus' - for choosing meaning
        if ($with_edit && !$has_checked_meaning && User::checkAccess('corpus.edit')) {
            $link_div= self::addEditMeaningButton($link_div, $id);
        }
        return $link_block;
    }

    // icon 'plus' - for choosing gramset
    public static function addEditGramsetButton($link_div, $id) {
        $add_link = $link_div->addChild('span');
        $add_link->addAttribute('data-add',$id);
        $add_link->addAttribute('class','fa fa-plus choose-gramset'); //  fa-lg 
        $add_link->addAttribute('title',trans('corpus.mark_right_meaning'));
        $add_link->addAttribute('onClick','addWordGramset(this)');
        return $link_div;
    }

    // icon 'plus' - for choosing meaning
    public static function addEditMeaningButton($link_div, $id) {
        $add_link = $link_div->addChild('span');
        $add_link->addAttribute('data-add',$id);
        $add_link->addAttribute('class','fa fa-plus choose-meaning'); //  fa-lg 
        $add_link->addAttribute('title',trans('corpus.mark_right_meaning'));
        return $link_div;
    }

    // icon 'pensil'
    public static function addEditExampleButton($link_block, $text_id, $sentence_id, $word_id) {
        if (!User::checkAccess('corpus.edit')) {
            return;
        }
        $button_edit_p = $link_block->addChild('p');
        $button_edit_p->addAttribute('class','text-example-edit'); 
        $button_edit = $button_edit_p->addChild('a',' ');//,'&#9999;'
        $button_edit->addAttribute('href',LaravelLocalization::localizeURL('/corpus/text/'.$text_id.'/edit/example/'.
                                                                            $sentence_id.'_'.$word_id)); 
        $button_edit->addAttribute('class','glyphicon glyphicon-pencil');  
        return $link_block;
    }
    
    public static function createWordCheckedBlock($meaning_id, $text_id, $sentence_id, $w_id) {
            $meaning = Meaning::find($meaning_id);
            if (!$meaning) {
                return;
            }
            $locale = LaravelLocalization::getCurrentLocale();
            $url = '/corpus/text/'.$text_id.'/edit/example/'.$sentence_id.'_'.$w_id;
            $str = '<div><a href="'.LaravelLocalization::localizeURL('dict/lemma/'.$meaning->lemma_id)
                 .'">'.$meaning->lemma->lemma.'<span> ('
                 .$meaning->getMultilangMeaningTextsString($locale)
                 .')</span></a></div>';
            
            $str .= Word::createGramsetBlock($text_id, $w_id);
            
            $str.='<p class="text-example-edit"><a href="'
                 .LaravelLocalization::localizeURL($url)
                 .'" class="glyphicon glyphicon-pencil"></a>';
            return $str;
    }
    
    /**
     * choose all sentences and all words
     * if a word is given then choose only sentences, containing the word, and only given words
     * 
     * @param string $word
     * @return array
     * 
     * [<sentence_id> => ['w_id'=>[<w_id1>, <w_id2>], 's' => <sentence_xml>] ]
     */
    public function sentences($word=''){
        $sentences = [];
        
        list($sxe,$error_message) = self::toXML($this->text_xml,$this->id);
        if ($error_message) {
            return $sentences;
        }

        $sentence_builder = Word::select('sentence_id','w_id')
                              ->where('text_id',$this->id)
                              ->orderBy('sentence_id');
        if ($word) {
//            $sentence_builder = $sentence_builder->where('word','like',Grammatic::toSearchForm($word));
            $sentence_builder = $sentence_builder->where('word','like',Grammatic::changeLetters($word, $this->lang_id));
        }                                
//dd($sentence_builder->get());        
        foreach ($sentence_builder->get() as $sentence) {
            $sentences[$sentence->sentence_id]['w_id'][]=$sentence->w_id;
        }
        
        foreach ($sentences as $sentence_id => $sentence) {
            $s = $sxe->xpath('//s[@id="'.$sentence_id.'"]');
            if (isset($s[0])) {
                $sentences[$sentence_id]['s']= $s[0]->asXML();
            }
        }
        
        return $sentences;
    }
    
    /**
     * find sentence in text, parse xml
     * 
     * @param int $text_id
     * @param int $sentence_id - number of sentence in the text
     * @param int $w_id - number of word in the text
     * @param INT OR Array $relevance - if function is called for one meaning, type is INT, else ARRAY
     * @return array f.e. ['s' => <sentence in xml format>, 
                           's_id' => <number of sentence in the text>,
                           'text' => <text object>, 
                           'trans_s' => <transtext sentence in xml format>,
                           'w_id' => <number of word in the text>, 
                            'relevance' => <relevance>]
     */
    public static function extractSentence($text_id, $sentence_id, $w_id, $relevance='') {
            $text = self::find($text_id);
            if (!$text) {
//print "<p>text error</p>";
                return NULL;
            }
            list($sxe,$error_message) = self::toXML($text->text_xml,$text->id);
            if ($error_message) {
//print "<p>$error_message</p>";                
                return NULL;
            }
            $s = $sxe->xpath('//s[@id="'.$sentence_id.'"]');
            if (isset($s[0])) {
                $sentence = ['s' => $s[0]->asXML(), 
                                's_id' => $sentence_id,
                                'text' => $text, 
                                'trans_s' => $text->getTransSentence($sentence_id),
                                'w_id' => $w_id, 
                                'relevance' => $relevance]; 
                return $sentence;
            } else {
                dd('!text_id='.$text_id.' and sentence_id='.$sentence_id.' and w_id='.$w_id);                    
            }
    }
    
    public function getTransSentence($sentence_id) {
        $transtext = Transtext::find($this->transtext_id);
        $trans_s = '';
        if ($transtext) {
            list($trans_sxe,$trans_error) = self::toXML($transtext->text_xml,'trans: '.$transtext->id);
            if (!$trans_error) {
                $trans_sent = $trans_sxe->xpath('//s[@id="'.$sentence_id.'"]');
                if (isset($trans_sent[0])) {
                    $trans_s = $trans_sent[0]->asXML();
                }
            }                    
        }
        return $trans_s;
    }

    public static function lastCreated($limit='') {
        $texts = self::latest();
        if ($limit) {
            $texts = $texts->take($limit);
        }
        $texts = $texts->get();
        foreach ($texts as $text) {
            $revision = Revision::where('revisionable_type','like','%Text')
                                ->where('key','created_at')
                                ->where('revisionable_id',$text->id)
                                ->latest()->first();
            if ($revision) {
                $text->user = User::getNameByID($revision->user_id);
            }
        }
        return $texts;
    }
    
    public static function lastUpdated($limit='',$is_grouped=0) {
        $revisions = Revision::where('revisionable_type','like','%Text')
                            ->where('key','updated_at')
                            ->groupBy('revisionable_id')
                            ->latest()->take($limit)->get();
        $texts = [];
        foreach ($revisions as $revision) {
            $text = self::find($revision->revisionable_id);
            if (!$text || !$revision->user_id) {
                continue;
            }
            $text->user = User::getNameByID($revision->user_id);
            if ($is_grouped) {
                $updated_date = $text->updated_at->formatLocalized(trans('main.date_format'));            
                $texts[$updated_date][] = $text;
            } else {
                $texts[] = $text;
            }
        }
        return $texts;
    }
    
    public static function processSentenceForExport($sentence) {
        $sentence = trim(str_replace("\n"," ",strip_tags($sentence)));
        return str_replace("\'","'",$sentence);
    }
    
    public function toCONLL() {
        $out = "";
        list($sxe,$error_message) = self::toXML($this->text_xml,$this->id);
        if ($error_message) {
            return NULL;
        }
        $sentences = $sxe->xpath('//s');
        $is_last_item = sizeof($sentences);
        foreach ($sentences as $sentence) {
            $out .= "# text_id = ".$this->id."\n".
                    "# sent_id = ".$this->id."-".$sentence['id']."\n".
                    //$sentence->asXML()."\n".
                    "# text = ".Text::processSentenceForExport($sentence->asXML())."\n";
            $trans_text = Text::processSentenceForExport($this->getTransSentence($sentence['id']));
            if ($trans_text) {
                $out .= "# text_ru = ".$trans_text."\n";
            }
            $count = 1;
            foreach ($sentence->w as $w) {
                $words = Word::toCONLL($this->id, (int)$w['id'], (string)$w);
                if (!$words) {
                    $out .= "$count\tERROR\n";
                    continue;
                }
                foreach ($words as $line) {
                    $out .= "$count\t".
                            //$w->asXML().
                            $line."\n";
                }
                $count++;
            }
            if ($is_last_item-- > 1) {
                $out .= "\n";
            }
        }
        return $out;
    }
    
    public function breakIntoVerses() {
        $verses = [];
        $v_text = trim(preg_replace("/\r/",'',preg_replace("/\n/",'',preg_replace("/\|/",'',$this->text))));
        $prev_verse=0;
        while (preg_match("/^(.*?)\<sup\>(\d+)\<\/sup\>(.*)$/", $v_text, $regs)) {
            if ($prev_verse) {
                $verses[$prev_verse] = trim($regs[1]);
            }
            $prev_verse = $regs[2];
            $v_text = $regs[3];
        }
        $verses[$prev_verse]= trim($v_text);
//dd($this->id, $verses);        
        return $verses;
    }
    
    public function sentencesToLines() {
        $out = "";
        list($sxe,$error_message) = self::toXML($this->text_xml,$this->id);
        if ($error_message) {
            return NULL;
        }
        $sentences = $sxe->xpath('//s');
        $is_last_item = sizeof($sentences);
        foreach ($sentences as $sentence) {
            $words = [];
            foreach ($sentence->w as $w) {
                $words[] = Word::uniqueLemmaWords($this->id, (int)$w['id'], (string)$w);
            }
            $out .= join('|',$words)."\n";
        }
        return $out;
    }
    
    public function allHistory() {
        $all_history = $this->revisionHistory->filter(function ($item) {
                            return $item['key'] != 'updated_at' 
                                   && $item['key'] != 'text_xml'
                                   && $item['key'] != 'transtext_id'
                                   && $item['key'] != 'event_id'
                                   && $item['key'] != 'checked'
                                   && $item['key'] != 'text_structure'
                                   && $item['key'] != 'source_id';
                                 //&& !($item['key'] == 'reflexive' && $item['old_value'] == null && $item['new_value'] == 0);
                        });
        foreach ($all_history as $history) {
            $history->what_created = trans('history.text_accusative');
        }
 
        if ($this->transtext) {
            $transtext_history = $this->transtext->revisionHistory->filter(function ($item) {
                                return $item['key'] != 'text_xml';
                            });
            foreach ($transtext_history as $history) {
                    $history->what_created = trans('history.transtext_accusative');
                    $fieldName = $history->fieldName();
                    $history->field_name = trans('history.'.$fieldName.'_accusative')
                            . ' '. trans('history.transtext_genetiv');
                }
                $all_history = $all_history -> merge($transtext_history);
        }
        
        if ($this->event) {
            $event_history = $this->event->revisionHistory->filter(function ($item) {
                                return $item['key'] != 'text_xml';
                            });
            foreach ($event_history as $history) {
                    $fieldName = $history->fieldName();
                    $history->field_name = trans('history.'.$fieldName.'_accusative')
                            . ' '. trans('history.event_genetiv');
                }
                $all_history = $all_history -> merge($event_history);
        }
        
        if ($this->source) {
            $source_history = $this->source->revisionHistory->filter(function ($item) {
                                return $item['key'] != 'text_xml';
                            });
            foreach ($source_history as $history) {
                    $fieldName = $history->fieldName();
                    $history->field_name = trans('history.'.$fieldName.'_accusative')
                            . ' '. trans('history.source_genetiv');
                }
                $all_history = $all_history -> merge($source_history);
        }
         
        $all_history = $all_history->sortByDesc('id')
                      ->groupBy(function ($item, $key) {
                            return (string)$item['updated_at'];
                        });
//dd($all_history);                        
        return $all_history;
    }
    
/*    public static function totalCount(){
        return self::count();
    }*/
          
    public static function countExamples(){
//        $examples = DB::table('meaning_text')->select('text_id', 'w_id')->groupBy('text_id', 'w_id')->get();
//        return sizeof($examples);
        return DB::table('meaning_text')->count();
    }
          
    public static function countCheckedExamples(){
        return DB::table('meaning_text')->where('relevance','<>',1)->count();
    }
          
    public static function countCheckedWords(){
        return DB::table('meaning_text')->select('text_id', 'w_id')
                 ->where('relevance','>',1)->distinct()->count();
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
        $texts = Text::select('text')->where('lang_id', $lang_id)->get();
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
    
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'search_birth_place' => $request->input('search_birth_place'),
                    'search_author'   => $request->input('search_author'),
                    'search_corpus'   => (array)$request->input('search_corpus'),
                    'search_dialect'  => (array)$request->input('search_dialect'),
                    'search_genre'    => (array)$request->input('search_genre'),
                    'search_informant'=> $request->input('search_informant'),
                    'search_lang'     => (array)$request->input('search_lang'),
                    'search_place'    => $request->input('search_place'),
                    'search_recorder' => $request->input('search_recorder'),
                    'search_sentence' => (int)$request->input('search_sentence'),
                    'search_title'    => $request->input('search_title'),
                    'search_wid'     => $request->input('search_wid'),
                    'search_word'     => $request->input('search_word'),
                    'search_text'     => $request->input('search_text'),
//                    'search_year'     => (int)$request->input('search_year'),
                    'search_year_from'=> (int)$request->input('search_year_from'),
                    'search_year_to'  => (int)$request->input('search_year_to'),
                ];
        
        return $url_args;
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
        
        $by_publ = self::selectRaw("year, count(*) as count")
                         ->join('sources', 'texts.source_id', '=', 'sources.id')
                         ->groupBy('year')
                         ->orderBy('year')
                         ->get();
        foreach ($by_publ as $rec) {
            $out['source_year'][$rec->year] = number_format($rec->count, 0, ',', ' ');
        }        

        $by_creation = self::selectRaw("year(created_at) as year, count(*) as count")
                         ->groupBy('year')
                         ->orderBy('year')
                         ->get();
        foreach ($by_creation as $rec) {
            $out['creation_date'][$rec->year] = number_format($rec->count, 0, ',', ' ');
        }    
        
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
    
    public function splitXMLToSentencesAndWrite() {       
        list($sxe,$error_message) = self::toXML($this->text_xml,$this->id);
        if ($error_message) {
            dd($error_message);
        }
        
        foreach ($sxe->xpath('//s') as $s) {
            $s_obj = Sentence::firstOrCreate([
                'text_id'=> $this->id,
                's_id' => $s->attributes()->id]);
            $s_obj->text_xml = $s->asXML();
            $s_obj->save();
            $s[0]='';
        }
        $this->text_structure = $sxe->asXML();
//dd($this->text_structure);        
        $this->save();
        /*
print "<pre>";        
        $sxe = new DOMDocument('1.0', 'utf-8');
        libxml_use_internal_errors(true);
        $sxe->LoadHTML($this->text_xml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
//        $sxe->LoadXML($this->text_xml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        foreach ($sxe->getElementsByTagName('s') as $s) {
dd($s->saveXML());            
        }
        */
    }
    
    public function textForPage($url_args) {       
        if ($this->text_structure) :
            $this->text_xml = $this->text_structure;
            $sentences = Sentence::whereTextId($this->id)->orderBy('s_id')->get();
            foreach ($sentences as $s) {
                $s->text_xml = preg_replace('/¦/', '', $s->text_xml);
                $this->text_xml = preg_replace("/\<s id=\"".$s->s_id."\"\/\>/", 
//                        '<sup>'.$s->id.'</sup>'.
                        $s->text_xml, $this->text_xml);                
            }
        endif; 
        if ($this->text_xml) :
            return $this->setLemmaLink($this->text_xml, 
                    $url_args['search_word'] ?? null, $url_args['search_sentence'] ?? null,
                    true, $url_args['search_wid'] ?? null);
        else :
            return nl2br($this->text);
        endif; 
    }
}
