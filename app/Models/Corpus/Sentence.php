<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use DB;

use App\Library\Grammatic;
use App\Library\Str;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gram;
use App\Models\Dict\Lang;
use App\Models\Dict\PartOfSpeech;

class Sentence extends Model
{
    protected $fillable = ['text_id', 's_id','text_xml'];

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
    use \App\Traits\Relations\BelongsTo\Text;
    
    public static function getBySid($text_id, $s_id) {
        return self::whereTextId($text_id)->whereSId($s_id)->first();
    }

    // different types of dashes and hyphens: '-', '‒', '–', '—', '―' 
    // if dash '-' inside words, then they are part of words,
    // if dash surrounded by spaces, then dashes are not parts of words.
    //
    // - and ' are a part of word
    public static function word_delimeters() {
        return ',;.!?"[](){}«»=„”“”:%‒–—―¦/';
    }
    
    /**
     * The char is a dash AND the next char is a special symbol (a word delimeter)
     * 
     * @param string $char one symbol
     * @param string $token
     * @param int $i
     * @return boolean
     */
    public static function dashIsOutOfWord($char, $token, $i) {
        $dashes = '-'; //digit dash

        $next_char = ($i+1 < mb_strlen($token)) ? mb_substr($token,$i+1,1) : '';

        // if the next_char is and of the sentence OR a delimeter OR a white space OR a dash THEN the next char is special
        $next_char_is_special = (!$next_char // empty string
                   // a word delimeter
                || mb_strpos(self::word_delimeters(), $next_char)!==false 
                   // a white space
                || preg_match("/\s/",$next_char) 
                   // a dash
                || mb_strpos($dashes,$next_char)!==false 
                   // a begin of a tag
                || $next_char == '<');
        
        return mb_strpos($dashes,$char)!==false && $next_char_is_special;
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
     * ./vendor/bin/phpunit tests/Models/Corpus/TextTest
     * 
     * @param string $token  text without mark up
     * @param integer $word_count  initial word count
     *
     * @return array text with markup (split to words) and next word count
     */
    public static function markup($token, $word_count): array
    {        
        $str = '';
        $i = 0;
        $is_word = false; // word tag <w> is not opened
        $word='';
        while ($i<mb_strlen($token)) {
            $char = mb_substr($token,$i,1);
            if ($char == '<') { // begin of a tag 
                list ($is_word, $str, $word_count) = self::wordAddToSentence($is_word, $word, $str, $word_count);
                list ($i, $str) = Word::tagOutWord($token, $i, $str);
                
                // the char is a delimeter or white space
            } elseif (mb_strpos(self::word_delimeters(), $char)!==false || preg_match("/\s/",$char)
                       // if word is ending with a dash, the dash is putting out of the word
                      || $is_word && self::dashIsOutOfWord($char, $token, $i) ) { 
                list ($is_word, $str, $word_count) = self::wordAddToSentence($is_word, $word, $str, $word_count);
                $str .= $char;
            } else {                
                // if word is not started AND (the char is not dash or the next char is not special) THEN the new word started
                if (!$is_word && !self::dashIsOutOfWord($char, $token, $i)) { 
                    $is_word = true;
                    $word='';
                }
                if ($is_word) {
                    $word .= $char;
                } else {
                    $str .= $char;            
                }
            }
//print "$i: $char| word: $word| is_word: $is_word| str: $str\n";            
            $i++;
        }
        list ($is_word, $str, $word_count) = self::wordAddToSentence($is_word, $word, $str, $word_count);
//print "$i: $char| word: $word| str: $str\n";            
        return [$str,$word_count]; 
    }
    
    public static function store($text_id, $s_id, $text_xml) {
        $sentence = self::firstOrCreate(['text_id' => $text_id, 's_id' => $s_id]);
        $sentence->text_xml = $text_xml;
        $sentence->save();
        return $sentence;
    }

    public function numerateWords() {
        $count=1;
//dd($this->text_xml);        
        list($sxe,$error_message) = Text::toXML($this->text_xml, 'sentence.id='.$this->id);
        if ($error_message) { dd($error_message); }
//dd($sxe->children()->s->w);        
        foreach ($sxe->children()->s->w as $w) {
            $w_id = (int)$w->attributes()->id;
            $word = Word::getByTextWid($this->text_id, $w_id); 
//dd($word);            
            $word->word_number = $count++;
            $word->save();
        }       
    }
    
    public function moveBrFromSentences() {
        $text=$this->text;
        $new_sentence = mb_ereg_replace("<s id=\"".$this->s_id."\"><br/>\r?\n?", "<s id=\"".$this->s_id."\">", $this->text_xml);
        $new_structure = mb_ereg_replace("<s id=\"".$this->s_id."\"", "<br/>\n<s id=\"".$this->s_id."\"", $text->text_structure);
        if ($this->text_xml != $new_sentence && $text->text_structure != $new_structure) {
            $this->text_xml = $new_sentence;
            $text->text_structure = $new_structure;
        } else {
dd($this->id, $text->id, $this->text_xml != $new_sentence, $text->text_structure != $new_structure);        
            
        }
//exit();        
//dd($this->text_xml, $text->text_structure);        
        $text->save();
        $this->save();
    }
    
/*    public static function search(Array $url_args) {
        $sentences = self::orderBy('id')
            ->whereIn('text_id', function ($q) use ($url_args) {
                $q ->select('id')->from('texts');
                if ($url_args['search_corpus']) {
                    $q = $q->whereIn('corpus_id',$url_args['search_corpus']);
                } 
                $q = Text::searchByDialects($q, $url_args['search_dialect']);
                $q = Text::searchByGenres($q, $url_args['search_genre']);
                $q = Text::searchByLang($q, $url_args['search_lang']);
                $q = Text::searchByYear($q, $url_args['search_year_from'], $url_args['search_year_to']);
            });
        
        $sentences = self::searchByWords($sentences, 'text_id', $url_args['search_word1'], $url_args['search_word2'], $url_args['search_distance_from'], $url_args['search_distance_to']);
        return $sentences;
    }
*/    
    
    /**
     * 
     * @param array $words
     * @return builder
     */
    public static function searchWords($words) {
//dd($words[1]['p']);
/*        
$words = [
    1 =>  [
        "w" => "vuozi",
        "p" => [ 0 => 1, 1 => 5 ], 
        "g" => [
            "gram_id_case" => [0 => 12], 
            "gram_id_number" => [0 => 1] 
        ] 
    ] 
    2 => [
        "w" => "^sin", 
        "p" => [0 => 10 ],
        "g" => [],
        "d_f" => "1",
        "d_t" => "1" 
    ] 
]
select t1.sentence_id from words as t1, words as t2
where t1.sentence_id=t2.sentence_id
AND t1.id in (
    SELECT word_id FROM text_wordform
    WHERE relevance > 0
    AND wordform_id in (
        select `wordform_id` from `lemma_wordform` 
        where `lemma_id` in (
            select `id` from `lemmas` 
            where `lemma_for_search` rlike 'vuozi' 
            and `pos_id` in ('1', '5')
        ) 
    )
    AND `gramset_id` in (
        select `id` from `gramsets` 
        where `gram_id_case` in ('12') 
        and `gram_id_number` in ('1') 
    )
) 
AND t2.word_number>t1.word_number 
AND t2.word_number-t1.word_number>=1 
AND t2.word_number-t1.word_number<=1
AND t2.id in (
    SELECT word_id FROM text_wordform
    WHERE relevance > 0
    AND wordform_id in (
        select `wordform_id` from `lemma_wordform` 
        where `lemma_id` in (
            select `id` from `lemmas` 
            where `lemma_for_search` rlike '^sin'
            and `pos_id` in ('10')
        )
    )
);
*/      $from = [];
        foreach (array_keys($words) as $i) {
            $from[] = 'words as t'.$i;
        }
        $builder = DB::table(DB::raw(join(', ', $from)));
        
        if (sizeof($words)>1) {
            $where = [];
            for ($i=1; $i<sizeof($words); $i++) {
                $where[] = "t".$i.".sentence_id=t".(int)($i+1).".sentence_id";
            }
            $builder -> whereRaw(join(' AND ', $where));
        }
        foreach ($words as $i => $word) {
            if ($i>1) {
                $builder->where('t1.word_number', '>', 0)
                        ->where('t'.$i.'.word_number', '>', 't'.($i-1).'.word_number')
                        ->where(DB::raw('t'.$i.'.word_number-t'.($i-1).'.word_number'), '>=', $word['d_f'])
                        ->where(DB::raw('t'.$i.'.word_number-t'.($i-1).'.word_number'), '<=', $word['d_t']);
            }
            $builder->whereIn('t'.$i.'.id', function ($q) use ($word) {
                $q->select('word_id')->from('text_wordform')
                  ->where('relevance', '>', 0);
                $search_by_lemma = isset($word['w']) && $word['w'];
                $search_by_pos = isset($word['p']) && $word['p'] && sizeof($word['p']);
                if ($search_by_lemma || $search_by_pos) {
                    $q->whereIn('wordform_id',function($query1) use ($word, $search_by_lemma, $search_by_pos){
                        $query1->select('wordform_id')
                        ->from('lemma_wordform')
                        ->whereIn('lemma_id',function($query2) use ($word, $search_by_lemma, $search_by_pos){
                            $query2->select('id')->from('lemmas');
                            if ($search_by_lemma) {
                                $query2->where('lemma_for_search', 'rlike', $word['w']);
                            }
                            if ($search_by_pos) {
                                $query2->whereIn('pos_id', $word['p']);
                            }
                        });
                    });
                }
                if (isset($word['g']) && sizeof($word['g'])) {
                    $q->whereIn('gramset_id',function($query2) use ($word){
                        $query2->select('id')->from('gramsets');
                        foreach ($word['g'] as $field => $group) {
                            $query2->whereIn($field, $group);
                        }
                    });
                }                    
            });
        }
//dd(vsprintf(str_replace(array('?'), array('\'%s\''), $builder->toSql()), $builder->getBindings()));                    
        return $builder;
    }
    /*public static function searchWords($builder, $words) {
//dd($words[1]['p']);        
            $builder=$builder->where('relevance', '>', 0);
//            foreach ($words as $count => $word)
            $builder=$builder->whereIn('wordform_id',function($query1) use ($words){
                    $query1->select('wordform_id')
                    ->from('lemma_wordform')
                    ->whereIn('lemma_id',function($query2) use ($words){
                        $query2->select('id')
                        ->from('lemmas');
                        if (isset($words[1]['w']) && $words[1]['w']) {
                            $query2->where('lemma_for_search', 'rlike', $words[1]['w']);
                        }
                        if (isset($words[1]['p']) && $words[1]['p'] && sizeof($words[1]['p'])) {
                            $query2->whereIn('pos_id', $words[1]['p']);
                        }
                    });
                    if (isset($words[1]['g']) && sizeof($words[1]['g'])) {
                        $query1->whereIn('gramset_id',function($query2) use ($words){
                            $query2->select('id')->from('gramsets');
                            foreach ($words[1]['g'] as $field => $group) {
                                $query2->whereIn($field, $group);
                            }
                        });
                    }
            });
        return $builder;
    }
*/    
    public static function searchTexts(Array $url_args) {
        $texts = Text::select('id');        
        if ($url_args['search_corpus']) {
            $texts = $texts->whereIn('corpus_id',$url_args['search_corpus']);
        } 
        $texts = Text::searchByDialects($texts, $url_args['search_dialect']);
        $texts = Text::searchByGenres($texts, $url_args['search_genre']);
        $texts = Text::searchByLang($texts, $url_args['search_lang']);
        $texts = Text::searchByYear($texts, $url_args['search_year_from'], $url_args['search_year_to']);
        return $texts->get();
    }
    /*
     * select * from gramsets where gram_id_mood in (27) and gram_id_tense in (24) and gram_id_number in (1,2) and gram_id_person in (21, 22);
     */
    public static function preparedWordsForSearch($words) {
        $out = [];
//dd($words);        
        foreach ($words as $i=>$word) {
            if ((!isset($word['w']) || !$word['w']) && (!isset($word['p']) || !$word['p']) && (!isset($word['g']) || !$word['g'])) {
                break;
            }
//            $out[$i]['w'] = Grammatic::toSearchForm($word['w']);
            $out[$i]['w'] = Grammatic::toSearchByPattern($word['w']);
            $out[$i]['p'] = [];
            foreach (preg_split('/\|/', $word['p']) as $p_code) {
                $p_id = PartOfSpeech::getIDByCode(trim($p_code));
                if ($p_id) {
                    $out[$i]['p'][] = $p_id;
                }
            }
            $out[$i]['g'] = [];
            if ($word['g']) {
                foreach (preg_split('/,/', $word['g']) as $orGroup) {
                    foreach (preg_split('/\|/', $orGroup) as $g_code) {
                        $gram = Gram::getByCode(trim($g_code));
                        $out[$i]['g']['gram_id_'.$gram->gramCategory->name_en][] = $gram->id; 
                    }
                }
            }
            if ($i>1) {
                $out[$i]['d_f'] = $word['d_f'] ?? 1; 
                $out[$i]['d_t'] = $word['d_t'] ?? 1; 
            }
        }
//dd($out);        
        return $out;
    }

    /**
     * select count(*) from `text_wordform` where `relevance` > 0 and `wordform_id` in (select `wordform_id` from `lemma_wordform` where `lemma_id` in (select `id` from `lemmas` where `lemma_for_search` like 'kačahtuakseh'))
     * 
     * @param type $word1
     * @param type $word2
     * @param type $distance_from
     * @param type $distance_to
     * @return collection
     */
    public static function entryNumber($args) {
        
//        $builder = DB::table('text_wordform')->selectRaw('DISTINCT text_id, w_id');
        $builder = self::searchWords($args['words'])
                ->whereIn('t1.text_id', Sentence::searchTexts($args));
//dd(vsprintf(str_replace(array('?'), array('\'%s\''), $builder->toSql()), $builder->getBindings()));            
        return sizeof($builder->get());
    }
    
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'search_corpus'   => (array)$request->input('search_corpus'),
                    'search_dialect'  => (array)$request->input('search_dialect'),
                    'search_genre'    => (array)$request->input('search_genre'),
                    'search_lang'     => (array)$request->input('search_lang'),
                    'search_year_from'=> (int)$request->input('search_year_from'),
                    'search_year_to'  => (int)$request->input('search_year_to'),
            
                    'search_words' => (array)$request->input('search_words'),
                ];
        
        if (!isset($url_args['search_words'][1])) {
            $url_args['search_words'][1]['w'] = '';
        }
        return $url_args;
    }    
    
    public static function searchQueryToString($args) {
        $out = [];
        if (sizeof($args['search_lang'])) {
            $out[] = '(<b>'.trans('dict.lang'). '</b>: '. join(' <span class="warning">'.trans('search.or').'</span> ', 
                    array_map(function ($id) {return Lang::getNameByID($id); }, 
                            $args['search_lang'])).')';
        }
        if (sizeof($args['search_dialect'])) {
            $out[] = '(<b>'.trans('dict.dialect'). '</b>: '. join(' <span class="warning">'.trans('search.or').'</span> ',
                    array_map(function ($id) {return Dialect::getNameByID($id); }, 
                            $args['search_dialect'])).')';
        }
        if (sizeof($args['search_corpus'])) {
            $out[] = '(<b>'.trans('corpus.corpus'). '</b>: '. join(' <span class="warning">'.trans('search.or').'</span> ',
                    array_map(function ($id) {return Corpus::getNameByID($id); }, 
                            $args['search_corpus'])).')';
        }
        if (sizeof($args['search_genre'])) {
            $out[] = '(<b>'.trans('corpus.genre'). '</b>: '. join(' <span class="warning">'.trans('search.or').'</span> ',
                    array_map(function ($id) {return Genre::getNameByID($id); }, 
                            $args['search_genre'])).')';
        }
        if ($args['search_year_from']) {
            $out[] = '<b>'.trans('search.year_from'). '</b>: '. 
                            $args['search_year_from'].'';
        }
        if ($args['search_year_to']) {
            $out[] = '<b>'.trans('search.year_to'). '</b>: '. 
                            $args['search_year_to'].'';
        }
        
        foreach ($args['search_words'] as $i => $word) {
            if (!isset($word['w']) && !isset($word['p']) && !isset($word['w'])) {
                continue;
            }
            $tmp=[];
            if ($word['w']) {
                $tmp[] = '<i>'.$word['w'].'</i>';
            }
            if ($word['p']) {
                $tmp[] = '('.join(' <span class="warning">'.trans('search.or').'</span> ',
                            array_map(function ($code) {return PartOfSpeech::getNameByCode(trim($code)); },
                                    preg_split('/\|/',$word['p']))).')';
            }
            if ($word['g']) {
                $groups = [];
                foreach (preg_split('/\,/',$word['g']) as $gr) {
                    $groups[] = '('.join(' <span class="warning">'.trans('search.or').'</span> ',
                            array_map(function ($code) {return Gram::getNameByCode(trim($code)); },
                                    preg_split('/\|/',$gr))).')';
                    
                }
                $tmp[] = '('.join(' <span class="warning">'.trans('search.and').'</span> ', $groups).')';
            }
            $out[] = '<br>(<b>'.trans('corpus.word'). " $i</b>: ". 
                            join(' <span class="warning">'.trans('search.and').'</span> ',$tmp).')';
        } 
        return join(' <span class="warning">'.trans('search.and').'</span> ', $out);
    }
    
    /**
     * Устанавить разметку с блоками слов

     * @param array $search_w         - array ID of searching word object
     * 
     * @return string                 - transformed text with markup tags
     **/
    public function addWordBlocks($search_w=[]){
        $markup_text = $this->text_xml;
        list($sxe,$error_message) = Text::toXML($markup_text,'');
//dd($error_message, $markup_text);        
        if ($error_message) {
            return $markup_text;
        }
//        $s_id = (int)$sentence->attributes()->id;
//dd($sentence);         
        $words = $sxe->xpath('//w');
        foreach ($words as $word) {
            $word = $this->addWordBlock($word, $search_w);
        }
        return $sxe->asXML();
    }
    
    public function addWordBlock($word, $search_w=[]) {
        $w_id = (int)$word->attributes()->id;
        if (!$w_id) { return $word; }
        $word['id'] = $this->text_id.'_'.$w_id;
        
        $meanings_checked = $this->text->meanings()->wherePivot('w_id',$w_id)
                          ->wherePivot('relevance', '>', 1)->count();
        $meanings_unchecked = $this->text->meanings()->wherePivot('w_id',$w_id)
                          ->wherePivot('relevance', 1)->count();
        $word_class = '';
        if ($meanings_checked || $meanings_unchecked) {
            $wordform_checked = $this->text->wordforms()->wherePivot('w_id',$w_id)
                              ->wherePivot('relevance', '>', 1)->count();
            $wordform_unchecked = $this->text->wordforms()->wherePivot('w_id',$w_id)
                              ->wherePivot('relevance', 1)->count();
            $word_class = 'word-linked';
            $word = $this->addLemmasBlock($word, $w_id, 
                    $meanings_checked && $wordform_checked ? 'word-checked' : 'word-unchecked');            
        }

        if (sizeof($search_w) && in_array($w_id,$search_w)) {
            $word_class .= ' word-marked';
        }

        if ($word_class) {
            $word->addAttribute('class',$word_class);
        }
        return $word;
    }

    public function addLemmasBlock($word, $w_id, $block_class) {
//        $word_obj = Word::getByTextWid($this->text_id, $w_id);
        $link_block = $word->addChild('div');
        $link_block->addAttribute('id','links_'.$this->text_id.'_'.$w_id);
        $link_block->addAttribute('class','links-to-lemmas '.$block_class);
        $link_block->addAttribute('data-downloaded',0);
        
        $load_img = $link_block->addChild('img');
        $load_img->addAttribute('class','img-loading');
        $load_img->addAttribute('src','/images/waiting_small.gif');
                
        return $word;        
    }
}
