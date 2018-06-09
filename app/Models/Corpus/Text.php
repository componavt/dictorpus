<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use DB;
use LaravelLocalization;
use \Venturecraft\Revisionable\Revision;

use App\Models\User;

use App\Models\Corpus\Corpus;
use App\Models\Corpus\Event;
use App\Models\Corpus\Informant;
use App\Models\Corpus\Source;
use App\Models\Corpus\Transtext;
use App\Models\Corpus\Word;

use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\Meaning;
use App\Models\Dict\MeaningText;
use App\Models\Dict\Wordform;

class Text extends Model
{
    protected $fillable = ['corpus_id','lang_id','source_id','event_id','title','text','text_xml'];

//    protected $delimeters = [',', '.', '!', '?', ':', '\'', '"', '[', ']', '(', ')', '{', '}', '«', '»', '=']; // '-',

    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.


    public static function boot()
    {
        parent::boot();
    }
/*
    public function getDelimeters()
    {
        return $this->delimeters;
    }
*/
    // Text __belongs_to__ Lang
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }

    // Text __belongs_to__ Corpus
    public function corpus()
    {
        return $this->belongsTo(Corpus::class);
    }

    // Text __belongs_to__ Event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Text __belongs_to__ Source
    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    // Text __belongs_to__ Transtext
    public function transtext()
    {
        return $this->belongsTo(Transtext::class);
    }

    // Text __has_many__ Dialects
    public function dialects(){
        $builder = $this->belongsToMany(Dialect::class);
        return $builder;
    }

    // Text __has_many__ Genres
    public function genres(){
        $builder = $this->belongsToMany(Genre::class);
        return $builder;
    }

    // Text __has_many__ Meanings
    public function meanings(){
        $builder = $this->belongsToMany(Meaning::class)
                 -> withPivot('relevance');
        return $builder;
    }

    // Text __has_many__ Words
    public function words(){
        return $this->hasMany(Word::class);
//        return = $this->belongsToMany(Word::class,'meaning_text');
    }

    /**
     * Gets IDs of informants for informant's form field
     *
     * @return Array
     */
    public function informantValue():Array{
        $informant_value = [];
        if ($this->event && $this->event->informants) {
            foreach ($this->event->informants as $informant) {
                $informant_value[] = $informant->id;
            }
        }
        return $informant_value;
    }

    /**
     * Gets IDs of recorders for record's form field
     *
     * @return Array
     */
    public function recorderValue():Array{
        $recorder_value = [];
        if ($this->event && $this->event->recorders) {
            foreach ($this->event->recorders as $recorder) {
                $recorder_value[] = $recorder->id;
            }
        }
        return $recorder_value;
    }

    /**
     * Gets IDs of dialects for dialect's form field
     *
     * @return Array
     */
    public function dialectValue():Array{
        $value = [];
        if ($this->dialects) {
            foreach ($this->dialects as $dialect) {
                $value[] = $dialect->id;
            }
        }
        return $value;
    }

    /**
     * Gets IDs of genres for genre's form field
     *
     * @return Array
     */
    public function genreValue():Array{
        $value = [];
        if ($this->genres) {
            foreach ($this->genres as $genre) {
                $value[] = $genre->id;
            }
        }
        return $value;
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
//        $str = str_replace(">","&gt;",$str);
//        $str = str_replace("<","&lt;",$str);
        return $str;
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

        $end1 = ['.','?','!','…'];
        $end2 = ['.»','?»','!»','."','?"','!"','.”','?”','!”'];
        $text = trim($text);
        $pseudo_end = false;
        if (!in_array(mb_substr($text,-1,1),$end1) && !in_array(mb_substr($text,-1,2),$end2)) {
            $text .= '.';
            $pseudo_end = true;
        }

        $text = nl2br($text);
        if (preg_match_all("/(.+?)(\.|\?|!|\.»|\?»|!»|\.\"|\?\"|!\"|\.”|\?”|!”|…{1,})(\s|(<br(| \/)>\s*){1,}|$)/is", // :|
                           $text, $desc_out)) {
            for ($k=0; $k<sizeof($desc_out[1]); $k++) {
                $sentence = trim($desc_out[1][$k]);

                // <br> in in the beginning of the string is moved before the sentence
                if (preg_match("/^(<br(| \/)>)(.+)$/is",$sentence,$regs)) {
                    $out .= $regs[1]."\n";
                    $sentence = trim($regs[3]);
                }
                if ($k == sizeof($desc_out[1])-1 && $pseudo_end) {
                    $desc_out[2][$k] = '';
                }

                // division on words
                list($str,$word_count) = Text::markupSentence($sentence,$word_count);

                $out .= "<s id=\"".$sen_count++.'">'.$str.$desc_out[2][$k]."</s>\n";
                $div = trim($desc_out[3][$k]);
                if ($div) {
                    $out .= trim($div)."\n";
                }
            }
        }

        return trim($out);
    }

    /**
     * Divides sentence on words
     *
     * @param $sentence String text without mark up
     * @param $word_count Integer initial word count
     *
     * @return Array text with markup (split to words) and next word count
     */
    public static function markupSentence($sentence,$word_count): Array
    {
        $delimeters = ',.!?"[](){}«»=”:'; // - and ' - part of word
        // different types of dashes and hyphens: '-', '‒', '–', '—', '―' 
        // if dashes inside words, then they are part of words,
        // if dashes surrounded by spaces, then dashes are not parts of words.
        $dashes = '-‒–—―';
        
        $str = '';
        $i = 0;
        $is_word = false; // word tag <w> is not opened
        $token = $sentence;
        while ($i<mb_strlen($token)) {
            $char = mb_substr($token,$i,1);
            if (mb_strpos($delimeters, $char)!==false || preg_match("/\s/",$char)) {
                if ($is_word) {
                    $str .= '</w>';
                    $is_word = false;
                }
                $str .= $char;
            } elseif ($char == '<') { // && strpos($token,'>',$i+1)
                if ($is_word) {
                    $str .= '</w>';
                    $is_word = false;
                }
                $j = mb_strpos($token,'>',$i+1);
                $str .= mb_substr($token,$i,$j-$i+1);
                $i = $j;
            } else {
                $next_char = ($i+1 < mb_strlen($token)) ? mb_substr($token,$i+1,1) : '';
                $next_char_is_special = (!$next_char || mb_strpos($delimeters, $next_char) || preg_match("/\s/",$next_char) || mb_strpos($dashes,$next_char));
//                if (!$is_word && !preg_match("/^-\s/",mb_substr($token,$i,2))) {
                if (!$is_word && !(mb_strpos($dashes,$char)!==false && $next_char_is_special)) { // && $next_char_is_special
                    $str .= '<w id="'.$word_count++.'">';
                    $is_word = true;
                }
                $str .= $char;
            }
            $i++;
        }
        if ($is_word) {
            $str .= '</w>';
        }
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

    /**
     * Sets links meaning - text - sentence
     */
    public function updateMeaningText($old_xml=null){
        if (Lang::isLetterChangeable($this->lang_id)) {
            $is_changeLetters = true;
        } else {
            $is_changeLetters = false;
        }
        list($sxe,$error_message) = self::toXML($this->text_xml,$this->id);

        if ($error_message) {
            return $error_message;
        }

        // saving old checked links
        $checked_words = [];
        if ($old_xml) {
            list($sxe_old,$error_message) = self::toXML($old_xml,$this->id);
            foreach ($sxe_old->children()->s as $sentence) {
                $s_id = (int)$sentence->attributes()->id;
                $word_count = 0;
                foreach ($sentence->children()->w as $word) {
                    //$checked_words[$s_id][$word_count] = [];                
                    $w_id = (int)$word->attributes()->id;
                    $meanings = DB::table("meaning_text")
                              ->where('relevance','<>',1)
                              ->where('text_id',$this->id)
                              ->where('w_id',$w_id)
                              ->get();
    //dd($meanings);        
                    foreach ($meanings as $meaning) {
                        $checked_words[$s_id][$word_count][$meaning->meaning_id] =
                                [(string)$word, $meaning->relevance];
                    }
                    $word_count++;
                }
            }
        }
        DB::statement("DELETE FROM words WHERE text_id=".(int)$this->id);
        DB::statement("DELETE FROM meaning_text WHERE text_id=".(int)$this->id);

        foreach ($sxe->children()->s as $sentence) {
                $s_id = (int)$sentence->attributes()->id;
                $word_count = 0;
                foreach ($sentence->children()->w as $word) {
                    $w_id = $word->attributes()->id;
                    $w_id = (int)$w_id;
                    $word_for_DB = (string)$word;
                    if ($is_changeLetters) {
                        $word_for_DB = Word::changeLetters($word_for_DB);
                    }
                    $word_obj = Word::create(['text_id' => $this->id,
                                              'sentence_id' => $s_id,
                                              'w_id' => $w_id,
                                              'word' => (string)$word_for_DB
                                            ]);
                    $word_t = addcslashes($word_for_DB,"'%");
                    $word_t_l = strtolower($word_t);
                    $wordform_q = "(SELECT id from wordforms where wordform like '$word_t' or wordform like '$word_t_l')";
                    $lemma_q = "(SELECT lemma_id FROM lemma_wordform WHERE wordform_id in $wordform_q)";
                    $meanings = Meaning::whereRaw("lemma_id in (SELECT id from lemmas where lang_id=".$this->lang_id
                                       ." and (lemma like '$word_t' or lemma like '$word_t_l' or id in $lemma_q))")
                                       ->get();    
                    foreach ($meanings as $meaning) {
                        $meaning_id = $meaning->id;
                        $relevance = 1;
                        if (isset($checked_words[$s_id][$word_count][$meaning_id][0])) {
                            if ($checked_words[$s_id][$word_count][$meaning_id][0] == $word 
                                    || $checked_words[$s_id][$word_count][$meaning_id][0] == $word.',') {
                                $relevance = $checked_words[$s_id][$word_count][$meaning_id][1];
                            }
                        }
                        $this->meanings()->attach($meaning_id,
                                ['sentence_id'=>$s_id,
                                 'word_id'=>$word_obj->id,
                                 'w_id'=>$w_id,
                                 'relevance'=>$relevance]);
                                            
                    }
                    $word_count++;
                }
        }
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
        $sxe = simplexml_load_string('<?xml version="1.0" encoding="utf-8" standalone="yes" ?>'.
                                     '<text>'.$text_xml.'</text>');
        $error_text = '';
        if (!$sxe) {
            $error_text = "XML loading error\n". '('.$id.')';
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

    /**
     * Gets markup text with links from words to related lemmas

     * @param $markup_text String
     * @return String markup text
     **/
    public function setLemmaLink($markup_text){
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

                $meanings = $this->meanings()->wherePivot('text_id',$text_id)
                                 ->wherePivot('w_id',$word_id)
                                 ->wherePivot('relevance','>',0);
                if ($meanings->count()) {
                    $link_block = $word->addChild('div');
                    $link_block->addAttribute('class','links-to-lemmas');
                    $link_block->addAttribute('id','links_'.$word_id);

                    $has_checked_meaning = false;
                    foreach ($meanings->get() as $meaning) {
                        $lemma = $meaning->lemma;

                        if ($meaning->pivot->relevance >1) {
                            $has_checked_meaning = true;
                        }

                        $link_div = $link_block->addChild('div');
                        $link = $link_div->addChild('a',$lemma->lemma);
                        $link->addAttribute('href',LaravelLocalization::localizeURL('/dict/lemma/'.$lemma->id));

                        $locale = LaravelLocalization::getCurrentLocale();
                        $meaning_text = $link->addChild('span',' ('.$meaning->getMultilangMeaningTextsString($locale).')');
                        if (!$has_checked_meaning && User::checkAccess('corpus.edit')) {
                            $add_link = $link_div->addChild('span');
                            $add_link->addAttribute('data-add',$meaning->id.'_'.$this->id.'_'.$sentence_id.'_'.$word_id);
                            $add_link->addAttribute('class','fa fa-plus choose-meaning'); //  fa-lg 
                            $add_link->addAttribute('title',trans('corpus.mark_right_meaning'));
                        }
                    }
                    if (User::checkAccess('corpus.edit')) {
                        $button_edit_p = $link_block->addChild('p');
                        $button_edit_p->addAttribute('class','text-example-edit'); 
                        $button_edit = $button_edit_p->addChild('a',' ');//,'&#9999;'
                        $button_edit->addAttribute('href',LaravelLocalization::localizeURL('/corpus/text/'.$text_id.'/edit/example/'.
                                                                                            $sentence_id.'_'.$word_id)); 
                        $button_edit->addAttribute('class','glyphicon glyphicon-pencil');
        //                $button = $button_edit->addChild('i');
        //                $button->addAttribute('class','fa-pencil'); 
        //                $button->addAttribute('class','fa fa-pencil fa-lg'); 
                    }
                    $class = 'lemma-linked';
                    if ($has_checked_meaning) {
                        $class .= ' has-checked';
                    } elseif ($meanings->count() > 1) {
                        $class .= ' polysemy';                
                    } 
                    $word->addAttribute('class',$class);

                }
            }
        }
        
        return $sxe->asXML();
    }

    public function sentences($word=''){
        $sentences = [];
        
        list($sxe,$error_message) = Text::toXML($this->text_xml,$this->id);
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
            $text = Text::find($text_id);
            if (!$text) {
//print "<p>text error</p>";
                return NULL;
            }
            list($sxe,$error_message) = Text::toXML($text->text_xml,$text->id);
            if ($error_message) {
//print "<p>$error_message</p>";                
                return NULL;
            }
            $s = $sxe->xpath('//s[@id="'.$sentence_id.'"]');
            if (isset($s[0])) {
                $transtext = Transtext::find($text->transtext_id);
                $trans_s = '';
                if ($transtext) {
                    list($trans_sxe,$trans_error) = Text::toXML($transtext->text_xml,'trans: '.$transtext->id);
                    if (!$trans_error) {
                        $trans_sent = $trans_sxe->xpath('//s[@id="'.$sentence_id.'"]');
                        if (isset($trans_sent[0])) {
                            $trans_s = $trans_sent[0]->asXML();
                        }
                    }                    
                }
                $sentence = ['s' => $s[0]->asXML(), 
                                's_id' => $sentence_id,
                                'text' => $text, 
                                'trans_s' => $trans_s,
                                'w_id' => $w_id, 
                                'relevance' => $relevance]; 
                return $sentence;
            } else {
                dd("!s: meaning_id=".$this->id.' and text_id='.$text_id.' and sentence_id='.$sentence_id.' and w_id='.$w_id);                    
            }
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
        
            $sentence = Text::extractSentence($text_id, $sentence_id, $w_id);            

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

    /**
     * 
     *
     * 
     */
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
            $text = Text::find($revision->revisionable_id);
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
        
        $event_history = $this->event->revisionHistory->filter(function ($item) {
                            return $item['key'] != 'text_xml';
                        });
        foreach ($event_history as $history) {
                $fieldName = $history->fieldName();
                $history->field_name = trans('history.'.$fieldName.'_accusative')
                        . ' '. trans('history.event_genetiv');
            }
            $all_history = $all_history -> merge($event_history);
        
        $source_history = $this->source->revisionHistory->filter(function ($item) {
                            return $item['key'] != 'text_xml';
                        });
        foreach ($source_history as $history) {
                $fieldName = $history->fieldName();
                $history->field_name = trans('history.'.$fieldName.'_accusative')
                        . ' '. trans('history.source_genetiv');
            }
            $all_history = $all_history -> merge($source_history);
        
/*        foreach ($this->transtext as $meaning) {
            foreach($meaning->meaningTexts as $meaning_text) {
               foreach ($meaning_text->revisionHistory as $history) {
                   $lang = $meaning_text->lang->name;
                   $fieldName = $history->fieldName();
                   $history->field_name = trans('history.'.$fieldName.'_accusative'). ' '
                           . trans('history.meaning_genetiv',['num'=>$meaning->meaning_n])
                           . " ($lang)";
               }
               $all_history = $all_history -> merge($meaning_text->revisionHistory);
            }
        }
 * 
 */
         
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
        $examples = DB::table('meaning_text')->select('text_id', 'w_id')->groupBy('text_id', 'w_id')->get();
//        $examples = DB::table('meaning_text')->select('text_id', 'sentence_id')->groupBy('text_id', 'sentence_id')->get();
        return sizeof($examples);
    }
          
    public static function countCheckedExamples(){
        return DB::table('meaning_text')->where('relevance','<>',1)->count();
    }
          
    public static function countCheckedWords(){
        return DB::table('meaning_text')->select('text_id', 'w_id')
                 ->where('relevance','>',1)->distinct()->count();
    }
}
