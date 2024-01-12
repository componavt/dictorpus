<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use DB;
use Storage;
use LaravelLocalization;
use \Venturecraft\Revisionable\Revision;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

use App\Library\Grammatic;
//use App\Library\Str;

use App\Models\User;

//use App\Models\Corpus\Event;
use App\Models\Corpus\Sentence;
//use App\Models\Corpus\Source;
use App\Models\Corpus\Transtext;
use App\Models\Corpus\Word;

use App\Models\Dict\Gramset;
use App\Models\Dict\Meaning;
use App\Models\Dict\Wordform;

class Text extends Model implements HasMediaConversions
{
    const PhotoDisk = 'photos';
    const PhotoDir = 'photo';
    protected $fillable = ['lang_id', 'source_id', 'event_id', 'title', 'text',  //'corpus_id', 
                           'text_xml', 'text_structure', 'comment'];

    use \App\Traits\TextMarkup;
    use \App\Traits\TextModify;
    use \App\Traits\TextSearch;
    use HasMediaTrait;
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
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
        return $this->words()->whereIn('id', function ($q) use ($status) {
            $q->select('word_id')->from('meaning_text');
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
        return Collection::getCollectionId($this->lang_id, $genre_ids);
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
     * Load xml from string, create SimpleXMLelement
     *
     * @param $text_xml - markup text
     * @param id - identifier of object Text or Transtext
     *
     * return Array [SimpleXMLElement object, error text if exists]
     */
    public static function toXML($text_xml, $id=NULL){
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
            $error_text = "XML loading error". ' ('.$id.": $text_xml)\n";
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
        $sentence = Word::select('s_id')
                        ->where('text_id',$this->id)
                        ->where('w_id',$w_id)->first();
        return $sentence->s_id;
    }

    /**
     * Gets markup text with links from words to related lemmas

     * @param string $markup_text     - text with markup tags
     * @param string $search_word     - string of searching word
     * @param string $search_sentence - ID of searching sentence object
     * @param boolean $with_edit      - 1 if it is the edit mode
     * @param array $search_w         - array ID of searching word object
     * 
     * @return string                 - transformed text with markup tags
     **/
    public function setLemmaLink($markup_text=null, $search_word=null, $search_sentence=null, $with_edit=true, $search_w=[]){
        if (!$markup_text) {
            $markup_text = $this->text_xml;
        }
        list($sxe,$error_message) = self::toXML($markup_text,'');
//dd($error_message, $markup_text);        
        if ($error_message) {
            return $markup_text;
        }
        $sentences = $sxe->xpath('//s');
        foreach ($sentences as $sentence) {
                $s_id = (int)$sentence->attributes()->id;
//dd($sentence);     
            foreach ($sentence->children() as $word) {
                $word = $this->editWordBlock($word, $s_id, $search_word, $search_sentence, $with_edit, $search_w);
            }
            $sentence_class = "sentence";
            if ($search_sentence && $search_sentence==$s_id) {
                $sentence_class .= " word-marked";
            }
            $sentence->addAttribute('class',$sentence_class);
            $sentence->attributes()->id = 'text_s'.$s_id;
        }
        
        return $sxe->asXML();
    }

    public function editWordBlock($word, $s_id, $search_word=null, $search_sentence=null, $with_edit=null, $search_w=[]) {
        $word_id = (int)$word->attributes()->id;
        if (!$word_id) { return $word; }
        
        $meanings_checked = $this->meanings()->wherePivot('w_id',$word_id)
                          ->wherePivot('relevance', '>', 1)->count();
        $meanings_unchecked = $this->meanings()->wherePivot('w_id',$word_id)
                          ->wherePivot('relevance', 1)->count();
        $word_class = '';
        if ($meanings_checked || $meanings_unchecked) {
            list ($word, $word_class) = $this->addMeaningsBlock($word, $s_id, 
                    $word_id, $meanings_checked, $meanings_unchecked, $with_edit);
            
        } elseif (User::checkAccess('corpus.edit')) {
            $word_class = 'lemma-linked call-add-wordform';
        }

//        if ($search_word && Grammatic::toSearchForm((string)$word) == $search_word 
        if ($search_word && Grammatic::changeLetters((string)$word, $this->lang_id) == $search_word 
                || sizeof($search_w) && in_array($word_id,$search_w)) {
            $word_class .= ' word-marked';
        }

        if ($word_class) {
            $word->addAttribute('class',$word_class);
        }
        return $word;
    }

    public function addMeaningsBlock($word, $s_id, $word_id, $meanings_checked, $meanings_unchecked, $with_edit=null) {
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
    public static function addEditExampleButton($link_block, $text_id, $s_id, $word_id) {
        if (!User::checkAccess('corpus.edit')) {
            return;
        }
        $button_edit_p = $link_block->addChild('p');
        $button_edit_p->addAttribute('class','text-example-edit'); 
        $button_edit = $button_edit_p->addChild('a',' ');//,'&#9999;'
        $button_edit->addAttribute('href',
                LaravelLocalization::localizeURL('/corpus/text/'.$text_id.
                        '/edit/example/'.$s_id.'_'.$word_id)); 
        $button_edit->addAttribute('class','glyphicon glyphicon-pencil');  
        return $link_block;
    }
    
    public static function createWordCheckedBlock($meaning_id, $text_id, $s_id, $w_id) {
        $meaning = Meaning::find($meaning_id);
        $text = Text::find($text_id);
        if (!$meaning || !$text) { return; }
        $locale = LaravelLocalization::getCurrentLocale();
        $url = '/corpus/text/'.$text_id.'/edit/example/'.$s_id.'_'.$w_id;
        
        return  '<div><a href="'.LaravelLocalization::localizeURL('dict/lemma/'.$meaning->lemma_id)
             .'">'.$meaning->lemma->lemma.'<span> ('
             .$meaning->getMultilangMeaningTextsString($locale)
             .')</span></a></div>'

             .$text->createGramsetBlock($w_id)

             .'<p class="text-example-edit"><a href="'
             .LaravelLocalization::localizeURL($url)
             .'" class="glyphicon glyphicon-pencil"></a>';
    }
    
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
    
    /**
     * Преобразует текст перед выводом на отдельной странице (Text show).
     * Собирает предложения и расставляет блоки со ссылками на леммы 
     * и вызов функций редактирования.
     * 
     * @param array $url_args
     * @return string
     */
    public function textForPage($url_args) { 
//mb_internal_encoding("UTF-8");
//mb_regex_encoding("UTF-8");        
        if ($this->text_structure) :
            $this->text_xml = $this->text_structure;
            $sentences = Sentence::whereTextId($this->id)->orderBy('s_id')->get();
            foreach ($sentences as $s) {
                $s->text_xml = mb_ereg_replace('[¦^]', '', $s->text_xml);
//dd($s->text_xml);                
                $this->text_xml = mb_ereg_replace("\<s id=\"".$s->s_id."\"\/\>", 
//                        '<sup>'.$s->id.'</sup>'.
                        $s->text_xml, $this->text_xml);                
            }
        endif; 
//dd($this->text_xml);        
        if ($this->text_xml) :
            return $this->setLemmaLink($this->text_xml, 
                    $url_args['search_word'] ?? null, $url_args['search_sentence'] ?? null,
                    true, $url_args['search_wid'] ?? []);
        endif; 
        return nl2br($this->text);
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
    
    public function createLemmaBlock($w_id) {
        if (!$w_id) { return null; }
        
        $meaning_checked = $this->meanings()->wherePivot('w_id',$w_id)->wherePivot('relevance','>',1)->first();
        $meaning_unchecked = $this->meanings()->wherePivot('w_id',$w_id)->wherePivot('relevance',1)->get();
        if (!$meaning_checked && !sizeof($meaning_unchecked)) { return null; }
        
        $word_obj = Word::whereTextId($this->id)->whereWId($w_id)->first();
        if (!$word_obj) {return null;} 
        return $word_obj->createLemmaBlock($this->id, $w_id);
    }
    
    public function createGramsetBlock($w_id) {
        $wordform = $this->wordforms()->wherePivot('w_id',$w_id)->wherePivot('relevance',2)->first();
        if ($wordform) {
            return '<p class="word-gramset">'.Gramset::getStringByID($wordform->pivot->gramset_id).'</p>';
        } elseif (User::checkAccess('corpus.edit')) { 
            $wordforms = $this->wordforms()->wherePivot('w_id',$w_id)->wherePivot('relevance',1)->get();
            if (!sizeof($wordforms)) { return null; }

            $str = '<div id="gramsets_'.$w_id.'" class="word-gramset-not-checked">';
            foreach ($wordforms as $wordform) {
                $gramset_id = $wordform->pivot->gramset_id;
                $str .= '<p>'.Gramset::getStringByID($gramset_id)
                     . '<span data-add="'.$this->id."_".$w_id."_".$wordform->id."_".$gramset_id
                     . '" class="fa fa-plus choose-gramset" title="'.\Lang::trans('corpus.mark_right_gramset').' ('
                     . $wordform->wordform.')" onClick="addWordGramset(this)"></span>'
                     . '</p>';
            }
            $str .= '</div>';
            return $str;
        }
    }
    
    public static function spellchecking($text, $lang_id) {
        list($markup_text) = Sentence::markup($text,1);
        $markup_text = self::addBlocksToWords($markup_text, $lang_id);
        return $markup_text;
    }

    public static function addBlocksToWords($text, $lang_id) {
        list($sxe,$error_message) = self::toXML($text);
        if ($error_message) { return $error_message; }

        foreach ($sxe->xpath('//w') as $word) {
            $word = Word::addBlockToWord($word, $lang_id);
        }
        return $sxe->asXML();
    }
    
}
