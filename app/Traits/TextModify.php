<?php namespace App\Traits;

use DB;

use App\Library\Grammatic;


use App\Models\Corpus\Audiotext;
use App\Models\Corpus\Event;
use App\Models\Corpus\Source;
use App\Models\Corpus\Transtext;
use App\Models\Corpus\Word;

use App\Models\Dict\Meaning;

trait TextModify
{
    public function addMeaning($meaning_id, $s_id, $word_id, $w_id, $relevance) {
                        $this->meanings()->attach($meaning_id,
                                ['s_id'=>$s_id,
                                 'word_id'=>$word_id,
                                 'w_id'=>$w_id,
                                 'relevance'=>$relevance]);        
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
    
    public static function updateByID($request, $id) {
        $request['text'] = self::process($request['text']);
        $to_makeup = (int)$request['to_makeup'];
        
        $text = self::with('transtext','event','source')->get()->find($id);
        $old_text = $text->text;

        $text->fill($request->only('lang_id','title','text','text_structure', 'comment'));//,'text_xml''corpus_id',

        $text->updated_at = date('Y-m-d H:i:s');
        $text->save();
        
        return $text -> storeAdditionInfo($request, $old_text, $to_makeup);
    }
    
    public function storeAdditionInfo($request, $old_text=NULL, $to_makeup=true){
        $error_message = '';
        $request['transtext_text'] = self::process($request['transtext_text']);
        $request['event_date'] = (int)$request['event_date'];
        
        $this->storeVideo($request->youtube_id);
        $this->storeTranstext($request->only('transtext_lang_id','transtext_title','transtext_text','transtext_text_xml', 'trans_authors'));
        $this->storeEvent($request->only('event_place_id','event_date','event_informants','event_recorders'));
        $this->storeSource($request->only('source_title', 'source_author', 'source_year', 'source_ieeh_archive_number1', 'source_ieeh_archive_number2', 'source_pages', 'source_comment'));
//dd($request->corpuses);
        $this->corpuses()->sync($request->corpuses);
        
        $this->authors()->detach();
        $this->authors()->attach($request->authors);

        $this->dialects()->detach();
        $this->dialects()->attach($request->dialects);

        $this->genres()->detach();
        $this->genres()->attach($request->genres);
        
        $this->cycles()->detach();
        $this->cycles()->attach($request->cycles);
        
        $this->plots()->detach();
        $this->plots()->attach($request->plots);
        
        $this->topics()->detach();
  //      $this->topics()->attach($request->topics);
        foreach ($request->topics as $topic) {
            if ($topic['topic_id']) {
                $this->topics()->attach([$topic['topic_id'] => ['sequence_number'=>(int)$topic['sequence_number']]]);
            }
        }
        
        $this->motives()->sync((array)$request->motives);
//dd($old_text, $request->text, $old_text != $request->text, !$this->text_structure, $to_makeup && $request->text && !$this->hasImportantExamples() && ($old_text != $request->text || !$this->text_structure));
        if ($to_makeup && $request->text && !$this->hasImportantExamples() && ($old_text != $request->text || !$this->text_structure)) {
            
            $error_message = $this->markup();
        }

        $this->uploadAudioFile($request);
        
        $this->push();        
        
        return $error_message;
    }
    
    public function uploadAudioFile($request)
    {
        // загрузка файла
        if ($request->file('new_file')) { // $request->isMethod('post') && 
            $file = $request->file('new_file');
            $upload_folder = 'storage/'.Audiotext::DIR;
            $filename = $request->new_file_name 
                    ? $request->new_file_name : $file->getClientOriginalName(); 
            if ($this->audiotexts()->whereFilename($filename)->count()) {
                $newfilename = $this->newAudiotextName();
            }

            $file->move($upload_folder, $filename);    
            
            Audiotext::create(['filename'=>$filename, 'text_id'=>$this->id]);            
        }
    }
    
    public function uploadPhotoFile($request)
    {
        // загрузка файла
        if ($request->file('new_file')) {
            $file = $request->file('new_file');
            $upload_folder = 'storage/'.Audiotext::DIR;
            $filename = $request->new_file_name 
                    ? $request->new_file_name : $file->getClientOriginalName(); 
            if ($this->audiotexts()->whereFilename($filename)->count()) {
                $newfilename = $this->newAudiotextName();
            }

            $file->move($upload_folder, $filename);    
            
            Audiotext::create(['filename'=>$filename, 'text_id'=>$this->id]);            
        }
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
//        if ($request_data['transtext_title'] && $request_data['transtext_text']) {
        if ($request_data['transtext_title']) {
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
            
            $transtext->authors()->detach();
            $transtext->authors()->attach($request_data['trans_authors']);
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
        $this->corpuses()->detach();
        $this->dialects()->detach();
        $this->genres()->detach();
        $this->plots()->detach();
        $this->cycles()->detach();
        $this->motives()->detach();
        $this->meanings()->detach();
        $this->wordforms()->detach();
        $this->authors()->detach();

        $this->sentences()->delete();
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
     * Sets links meaning - text - sentence AND text-wordform
     */
    public function updateMeaningAndWordformText($sentence, $text_xml, $without_check=false){
        $s_id = $sentence->s_id;
        list($sxe,$error_message) = self::toXML($text_xml, $s_id);
        if ($error_message) { return $error_message; }
//dd($text_xml);
        $checked_words = $without_check ? [] : $this->checkedWords($text_xml);
//dd($checked_words);
        $where_text = "text_id=".(int)$this->id;
        DB::statement("DELETE FROM words WHERE s_id=$s_id and $where_text");
        DB::statement("DELETE FROM meaning_text WHERE s_id=$s_id and $where_text");
        DB::statement("DELETE FROM text_wordform WHERE w_id in (select w_id from words where s_id=$s_id and $where_text) and $where_text");            

        $this->updateMeaningAndWordformSentence($sentence, $sxe->xpath('//w'), 
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
    
    public function updateMeaningAndWordformSentence($sentence, $sent_words, $checked_sent_words/*, $set_meanings=true, $set_wordforms=true*/) {
        $s_id = $sentence->s_id;
        $word_count = 0;
        foreach ($sent_words as $word) {
//dd((string)$word);            
            $w_id = (int)$word->attributes()->id;
            $word_for_search = Grammatic::changeLetters((string)$word,$this->lang_id);
            
//            if ($set_meanings) {
                $word_obj = Word::create(['text_id' => $this->id, 
                                          'sentence_id' => $sentence->id, 
                                          's_id' => $s_id, 
                                          'w_id' => $w_id, 
                                          'word' => $word_for_search, 
                                          'word_number' => $word_count+1]);
//            } else {
//                $word_obj = Word::whereTextId($this->id)->whereWId($w_id)->first();                
//            }
            
            $cond = "w_id=$w_id and s_id<>$s_id and text_id=".(int)$this->id;
            DB::statement("DELETE FROM words WHERE $cond");
            DB::statement("DELETE FROM meaning_text WHERE $cond");
            
            $the_same_word = isset($checked_sent_words[$word_count]['w']) && $word_for_search==$checked_sent_words[$word_count]['w'];
//            if ($set_meanings) {
                $word_obj->setMeanings($the_same_word ? $checked_sent_words[$word_count]['meanings'] : [], $this->lang_id);
//            }
//            if ($set_wordforms) {
                $this->setWordforms($the_same_word ? $checked_sent_words[$word_count]['wordforms'] : [], $word_obj);
//            }
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
    
    public function uploadPhoto($filename, $name) {
            $p = $this->addMediaFromRequest($filename)->toMediaLibrary();
//            $p = $this->addMedia($file)->toMediaLibrary();
            $p->name = $name; 
            $p->save();        
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
        foreach (self::getWordformsByWord($word_obj->word, $this->lang_id) as $wordform) {
            $wg_id = $wordform->id. '_'. $wordform->gramset_id;
            $relevance = $checked_relevances[$wg_id] ?? ($has_checked ? 0 : 1);
            $this->addWordform($wordform->id, $wordform->gramset_id, $word_obj->id, $word_obj->w_id, $relevance);
        }
    }

    public function addWordform($wordform_id, $gramset_id, $word_id, $w_id, $relevance) {
        if ($this->wordforms()->wherePivot('wordform_id',$wordform_id)
                 ->wherePivot('w_id',$w_id)
                 ->wherePivot('gramset_id',$gramset_id)->count()) {
            return;
        }
        $this->wordforms()->attach($wordform_id,
                ['w_id'=>$w_id,
                 'word_id' => $word_id,
                 'gramset_id' => $gramset_id,
                 'relevance'=>$relevance]);        
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
                    ['s_id'=>$word->s_id,
                     'word_id'=>$word->id, 'w_id'=>$w_id,
                     'relevance'=>$relevance]);            
        }
        DB::statement("UPDATE meaning_text SET relevance=0 WHERE text_id=".$this->id
                    . " and w_id=$w_id and meaning_id<>".$meaning_id);
//dd($this->meanings()->wherePivot('w_id', $w_id)->get());        
    }
    
// select id from meanings where lemma_id in (SELECT id from lemmas where lemma like '$word_t' or id in (SELECT lemma_id FROM lemma_wordform WHERE wordform_id in (SELECT id from wordforms where wordform like '$word_t')))    
// select id from meanings where lemma_id in (SELECT id from lemmas where lemma like 'myö' or id in (SELECT lemma_id FROM lemma_wordform WHERE wordform_id in (SELECT id from wordforms where wordform like 'myö')));    


}