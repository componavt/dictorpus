<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use DB;
use LaravelLocalization;
use \Venturecraft\Revisionable\Revision;

use App\Library\Grammatic;
use App\Models\User;

use App\Models\Corpus\Corpus;
use App\Models\Corpus\Event;
use App\Models\Corpus\Informant;
use App\Models\Corpus\Source;
use App\Models\Corpus\Transtext;
use App\Models\Corpus\Word;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\Meaning;
use App\Models\Dict\MeaningText;
use App\Models\Dict\Wordform;

class Text extends Model
{
    protected $fillable = ['corpus_id','lang_id','source_id','event_id','title','text','text_xml'];

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
    use \App\Traits\Relations\BelongsToMany\Dialects;
    use \App\Traits\Relations\BelongsToMany\Genres;
    use \App\Traits\Relations\BelongsToMany\Meanings;
    
    use \App\Traits\Relations\HasMany\Words;

    // Text __belongsToMany__ Wordforms
    public function wordforms(){
//        return $this->hasMany(Wordform::class);
        $builder = $this->belongsToMany(Wordform::class,'text_wordform')
                 ->withPivot('w_id') -> withPivot('gramset_id');
        return $builder;
    }

    // Text __has_one__ Video
    public function video()
    {
        return $this->hasOne(Video::class);
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
        $texts = self::searchByBirthPlace($texts, $url_args['search_birth_place']);
        $texts = self::searchByDialects($texts, $url_args['search_dialect']);
        $texts = self::searchByInformant($texts, $url_args['search_informant']);
        $texts = self::searchByLang($texts, $url_args['search_lang']);
        $texts = self::searchByPlace($texts, $url_args['search_place']);
        $texts = self::searchByRecorder($texts, $url_args['search_recorder']);
        $texts = self::searchByTitle($texts, $url_args['search_title']);
        $texts = self::searchByWord($texts, $url_args['search_word']);
        $texts = self::searchByText($texts, $url_args['search_text']);
        
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
        
        $this->dialects()->detach();
        $this->dialects()->attach($request->dialects);

        $this->genres()->detach();
        $this->genres()->attach($request->genres);
        
        if ($request->text && ($old_text != $request->text || !$this->text_xml)) {
            $error_message = $this->markup($this->text_xml);
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

    /**
     * Gets a markup text with sentences
     *
     * @param $text String text without mark up
     * @return String text with markup (split to sentences and words)
     */
    public static function markupText($text): String
    {
        $out = '';
        $sen_count = 1;
        $word_count = 1;

        $end1 = ['.','?','!','…','|'];
        $end2 = ['.»','?»','!»','."','?"','!"','.”','?”','!”'];
        $text = trim($text);
        $pseudo_end = false;
        if (!in_array(mb_substr($text,-1,1),$end1) && !in_array(mb_substr($text,-1,2),$end2)) {
            $text .= '.';
            $pseudo_end = true;
        }

        $text = nl2br($text);
        if (preg_match_all("/(.+?)(\||\.|\?|!|\.»|\?»|!»|\.\"|\?\"|!\"|\.”|\?”|!”|…{1,})(\s|(<br(| \/)>\s*){1,}|$)/is", // :|
                           $text, $desc_out)) {
            for ($k=0; $k<sizeof($desc_out[1]); $k++) {
                $sentence = trim($desc_out[1][$k]);

                // <br> in in the beginning of the string is moved before the sentence
                if (preg_match("/^(<br(| \/)>)(.+)$/is",$sentence,$regs)) {
                    $out .= $regs[1]."\n";
                    $sentence = trim($regs[3]);
                }
                if ($k == sizeof($desc_out[1])-1 && $pseudo_end || $desc_out[2][$k] == '|') {
                    $desc_out[2][$k] = '';
                }

//                $sentence = str_replace('|','',$sentence);
                // division on words
                list($str,$word_count) = self::markupSentence($sentence,$word_count);

                $out .= "<s id=\"".$sen_count++.'">'.$str.$desc_out[2][$k]."</s>\n";
                $div = trim($desc_out[3][$k]);
                if ($div) {
                    $out .= trim($div)."\n";
                }
            }
        }

        return trim($out);
    }
    
    public static function wordAddToSentence($is_word, $word, $str, $word_count) {
        if ($is_word) { // the previous char is part of a word, the word ends
            if (!preg_match("/([a-zA-ZА-Яа-яЁё])/u",$word, $regs)) {
                $str .= $word;
            } else {
//dd($regs);
                $str .= '<w id="'.$word_count++.'">'.$word.'</w>';
            }
            $is_word = false;
        }
        return [$is_word, $str, $word_count]; 
    }

    /**
     * Divides sentence on words
     *
     * @param $sentence String text without mark up
     * @param $word_count Integer initial word count
     * ./vendor/bin/phpunit tests/Models/Corpus/TextTest
     *
     * @return Array text with markup (split to words) and next word count
     */
    public static function markupSentence($sentence,$word_count): Array
    {
        $delimeters = ',.!?"[](){}«»=„”:%‒–—―'; // - and ' - part of word
        // different types of dashes and hyphens: '-', '‒', '–', '—', '―' 
        // if dash '-' inside words, then they are part of words,
        // if dash surrounded by spaces, then dashes are not parts of words.
        $dashes = '-'; //digit dash
        
        $str = '';
        $i = 0;
        $is_word = false; // word tag <w> is not opened
        $token = $sentence;
        $word='';
        while ($i<mb_strlen($token)) {
            $char = mb_substr($token,$i,1);
            if ($char == '<') { // begin of a tag 
                list ($is_word, $str, $word_count) = Text::wordAddToSentence($is_word, $word, $str, $word_count);
                $j = mb_strpos($token,'>',$i+1);
                $str .= mb_substr($token,$i,$j-$i+1); // other chars of the tag are transferred to str
                $i = $j;
            } elseif (mb_strpos($delimeters, $char)!==false || preg_match("/\s/",$char)) { // the char is a delimeter or white space
                list ($is_word, $str, $word_count) = Text::wordAddToSentence($is_word, $word, $str, $word_count);
                $str .= $char;
//if ($i>15) {exit(0);}
//dd("$i: $char");                
            } else {
                $next_char = ($i+1 < mb_strlen($token)) ? mb_substr($token,$i+1,1) : '';
                
                // if the next_char is and of the sentence OR a delimeter OR a white space OR a dash THEN the next char is special
                $next_char_is_special = (!$next_char || mb_strpos($delimeters, $next_char)!==false || preg_match("/\s/",$next_char) || mb_strpos($dashes,$next_char)!==false || $next_char == '<');
                $char_is_dash_AND_next_char_is_special = mb_strpos($dashes,$char)!==false && $next_char_is_special;
                
                if ($is_word && $char_is_dash_AND_next_char_is_special) {
                    list ($is_word, $str, $word_count) = Text::wordAddToSentence($is_word, $word, $str, $word_count);
                    $str .= $char;
                } else {
                // if word is not started AND NOT (the char is dash AND the next char is special) THEN the new word started
                if (!$is_word && !$char_is_dash_AND_next_char_is_special) { 
//                if (!$is_word && mb_strpos($dashes,$char)===false) { 
                    $is_word = true;
                    $word='';
                }
                if ($is_word) {
                    $word .= $char;
                } else {
                    $str .= $char;            
                }
                }
            }
//print "$i: $char| word: $word| is_word: $is_word| str: $str\n";            
            $i++;
        }
        list ($is_word, $str, $word_count) = Text::wordAddToSentence($is_word, $word, $str, $word_count);
//print "$i: $char| word: $word| str: $str\n";            
        return [$str,$word_count]; 
    }

    /**
     * Sets text_xml as a markup text with sentences
     */
    public function markup($old_xml=null){
        $this->text_xml = self::markupText($this->text);
        $error_message = $this->updateMeaningText($old_xml);
        if ($error_message) {
            return $error_message;
        }
    }

    // saving old checked links
    public function checkedWords($old_xml) {
        $checked_words = [];
        if (!$old_xml) { return $checked_words; } 
        
        list($sxe_old,$error_message) = self::toXML($old_xml,$this->id);
        if (!$sxe_old || $error_message) { return $checked_words; } 

        foreach ($sxe_old->children()->s as $sentence) {
            $s_id = (int)$sentence->attributes()->id;
            $word_count = 0;
            foreach ($sentence->children()->w as $word) {
                $checked_words[$s_id][$word_count] = 
                    Word::checkedMeaningRelevances($this->id, 
                            (int)$word->attributes()->id, (string)$word);                
                $word_count++;
            }
        }
        
        return $checked_words;
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
     * Sets links meaning - text - sentence
     */
    public function updateMeaningText($old_xml=null){
        list($sxe,$error_message) = self::toXML($this->text_xml,$this->id);
        if ($error_message) { return $error_message; }

        $checked_words = $this->checkedWords($old_xml);
//dd($checked_words);        
        DB::statement("DELETE FROM words WHERE text_id=".(int)$this->id);
        DB::statement("DELETE FROM meaning_text WHERE text_id=".(int)$this->id);

        foreach ($sxe->children()->s as $sentence) {
            $s_id = (int)$sentence->attributes()->id;
/*print "<pre>";
        var_dump($s_id, $sentence->children()->w, isset($checked_words[$s_id]) ? $checked_words[$s_id] : NULL, '____________________<br>');
print "</pre>";*/
            $sxe = $this->updateMeaningSentence($sxe, $s_id, $sentence->children()->w, isset($checked_words[$s_id]) ? $checked_words[$s_id] : NULL);
        }
    }
    
    public function updateMeaningSentence($sxe, $s_id, $sent_words, $checked_sent_words) {
        $word_count = 0;
//dd($sent_words);        
/*print "<pre>";
        var_dump($sent_words);
print "</pre>";*/
        $left_words = [];
        foreach ($sent_words as $word) {
            $w_id = (int)$word->attributes()->id;
            $word_for_search = Grammatic::changeLetters((string)$word,$this->lang_id);

//            list($sxe, $word_for_search) = $this->searchToMerge($sxe, $w_id, $word_for_search, $left_words);
            
            $word_obj = Word::create(['text_id' => $this->id, 'sentence_id' => $s_id, 'w_id' => $w_id, 'word' => $word_for_search]);
//            if (isset ($checked_sent_words[$word_count])) {
                $word_obj->setMeanings(isset ($checked_sent_words[$word_count])? $checked_sent_words[$word_count] : 1, $this->lang_id);
//            }
/*            foreach (Word::getMeaningsByWord($word_for_search, $this->lang_id) as $meaning) {
                $meaning_id = $meaning->id;
                $relevance = isset($checked_sent_words[$word_count][$meaning_id][0]) && $checked_sent_words[$word_count][$meaning_id][0] == $word 
                           ? $relevance = $checked_sent_words[$word_count][$meaning_id][1] : 1;
                $this->addMeaning($meaning_id, $s_id, $word_obj->id, $w_id, $relevance);
            }*/
            $left_words[$w_id] = $word_for_search;
            $word_count++;
        }
//dd($sent_words);     
        return $sxe;
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
                $error_text .= "\t". $error->message. '('.$id.')';
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

    // icon 'pensil'
    static public function addEditExampleButton($link_block, $text_id, $sentence_id, $word_id) {
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
    /**
     * Gets markup text with links from words to related lemmas

     * @param String $markup_text 
     * @param String $search_word 
     * @return String markup text
     **/
    public function setLemmaLink($markup_text, $search_word=null, $search_sentence=null){
        $text_id = (int)$this->id;
        list($sxe,$error_message) = self::toXML($markup_text,'');
        if ($error_message) {
            return $markup_text;
        }
        $sentences = $sxe->xpath('//s');
        foreach ($sentences as $sentence) {
            $sentence_id = (int)$sentence->attributes()->id;
//dd($sentence);     
            foreach ($sentence->children() as $word) {
                $word_id = (int)$word->attributes()->id;
                if (!$word_id) {
                    continue;
                }
                $meanings = $this->meanings()->wherePivot('text_id',$text_id)
                                 ->wherePivot('w_id',$word_id)
                                 ->wherePivot('relevance','>',0);
                $word_class = '';
                if ($meanings->count()) {
                    $word_class = 'lemma-linked';
                    $link_block = $word->addChild('div');
                    $link_block->addAttribute('class','links-to-lemmas');
                    $link_block->addAttribute('id','links_'.$word_id);
                    $has_checked_meaning = false;
                    // output meanings
                    foreach ($meanings->get() as $meaning) {
                        $lemma = $meaning->lemma;

                        if ($meaning->pivot->relevance >1) {
                            $has_checked_meaning = true;
                        }
                        // link to lemma
                        $link_div = $link_block->addChild('div');
                        $link = $link_div->addChild('a',$lemma->lemma);
                        $link->addAttribute('href',LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id));

                        // icon 'plus' - for choosing meaning
                        $locale = LaravelLocalization::getCurrentLocale();
                        $link->addChild('span',' ('.$meaning->getMultilangMeaningTextsString($locale).')');
                        if (!$has_checked_meaning && User::checkAccess('corpus.edit')) {
                            $add_link = $link_div->addChild('span');
                            $add_link->addAttribute('data-add',$meaning->id.'_'.$this->id.'_'.$sentence_id.'_'.$word_id);
                            $add_link->addAttribute('class','fa fa-plus choose-meaning'); //  fa-lg 
                            $add_link->addAttribute('title',trans('corpus.mark_right_meaning'));
                        }
                    }
                    if ($has_checked_meaning) {
                        $word_class .= ' has-checked';
                        // gramset
                        $wordform = $this->wordforms()->wherePivot('w_id',$word_id) -> first();
                        if ($wordform) {
                            $gramset_p = $link_block->addChild('p', Gramset::getStringByID($wordform->pivot->gramset_id));
                            $gramset_p -> addAttribute('class','word_gramset');
                        }
                    } elseif ($meanings->count() > 1) {
                        $word_class .= ' polysemy';                
                    } else {
                        $word_class .= ' not-checked';
                    }
                    // icon 'pensil'
                    $link_block = self::addEditExampleButton($link_block,$text_id, $sentence_id, $word_id);
                } elseif (User::checkAccess('corpus.edit')) {
                    $word_class = 'lemma-linked call-add-wordform';
                }
                
                if ($search_word && Grammatic::toSearchForm((string)$word) == $search_word) {
                    $word_class .= ' word-marked';
                }
                
                if ($word_class) {
                    $word->addAttribute('class',$word_class);
                }
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
            
            $text = Text::find($text_id);
            if ($text) {
                $wordform = $text->wordforms()->wherePivot('w_id',$w_id) -> first();
                if ($wordform) {
                    $str.='<p class="word_gramset">'.Gramset::getStringByID($wordform->pivot->gramset_id).'</p>';
                }
            }
            $str.='<p class="text-example-edit"><a href="'
                 .LaravelLocalization::localizeURL($url)
                 .'" class="glyphicon glyphicon-pencil"></a>';
            return $str;
    }
    
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
            $sentence_builder = $sentence_builder ->where('word','like',$word);
        }                                
//dd($sentence_builder->toSql());        
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

    /**
     * 

     * @param $markup_text String
     * @return String markup text
     **/
    public static function preparationForExampleEdit($example_id){
        if (preg_match("/^(\d+)_(\d+)_(\d+)$/",$example_id,$regs)) {
            $text_id = (int)$regs[1];
            $sentence_id = (int)$regs[2];
            $w_id = (int)$regs[3];
        
            $sentence = self::extractSentence($text_id, $sentence_id, $w_id);            

            $meanings = Meaning::join('meaning_text','meanings.id','=','meaning_text.meaning_id')
                               -> where('text_id',$text_id)
                               -> where('sentence_id',$sentence_id)
                               -> where('w_id',$w_id)
                               -> get();
            $meaning_texts = [];

            foreach ($meanings as $meaning) {
                $langs_for_meaning = Lang::getListWithPriority($meaning->lemma->lang_id);
                foreach ($langs_for_meaning as $lang_id => $lang_text) {
                    $meaning_text_obj = MeaningText::where('lang_id',$lang_id)->where('meaning_id',$meaning->id)->first();
                    if ($meaning_text_obj) {
                        $meaning_texts[$meaning->id][$lang_text] = $meaning_text_obj->meaning_text;
                    }
                }
            }   
            
            return [$sentence, $meanings, $meaning_texts];
        } else {
            return [NULL, NULL, NULL];
        }
    }

    public static function updateExamples($relevances) {
        foreach ($relevances as $key => $value) {
            $relevance = (int)$value;
            if (preg_match("/^(\d+)\_(\d+)_(\d+)_(\d+)$/",$key,$regs)) {
                $meaning_id = (int)$regs[1];
                $text_id = (int)$regs[2];
                $sentence_id = (int)$regs[3];
                $w_id = (int)$regs[4];
                if ($relevance == 1) { // не выставлена оценка
                    $exists_positive_rel = DB::table('meaning_text') // ищем другие значения лемм с положительной оценкой
                            -> where('text_id',$text_id)
                            -> where('sentence_id',$sentence_id)
                            -> where('w_id',$w_id)
                            -> where('meaning_id', '<>', $meaning_id)
                            -> where ('relevance','>',1);
                    if ($exists_positive_rel->count() > 0) { // этот пример привязан к другому значению
                        $relevance = 0;
                    }
                } elseif ($relevance != 0) { // положительная оценка
                    DB::statement('UPDATE meaning_text SET relevance=0'. // всем значениям с неопределенными оценками проставим отрицательные
                                  ' WHERE meaning_id <> '.$meaning_id.
                                  ' AND relevance=1'.
                                  ' AND text_id='.$text_id.
                                  ' AND sentence_id='.$sentence_id.
                                  ' AND w_id='.$w_id);
                }
                DB::statement('UPDATE meaning_text SET relevance='.$relevance // запишем оценку этому значению
                             .' WHERE meaning_id='.$meaning_id
                             .' AND text_id='.$text_id
                             .' AND sentence_id='.$sentence_id
                             .' AND w_id='.$w_id);
            }
        }
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
}
