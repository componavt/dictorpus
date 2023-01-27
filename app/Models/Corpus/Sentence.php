<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Library\Grammatic;
use App\Library\Str;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gram;
use App\Models\Dict\Gramset;
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
    
    // Has Many Relations
    use \App\Traits\Relations\HasMany\SentenceFragments;    // fragments()
    use \App\Traits\Relations\HasMany\SentenceTranslations; // translations()
    
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
     * @param string $text  text without mark up
     * @param integer $word_count  initial word count
     *
     * @return array text with markup (split to words) and next word count
     */
    public static function markup($text, $word_count): array
    {        
        $str = '';
        $i = 0;
        $is_word = false; // word tag <w> is not opened
        $word='';
        while ($i<mb_strlen($text)) {
            $char = mb_substr($text,$i,1);
            if ($char == '<') { // begin of a tag 
                list ($is_word, $str, $word_count) = self::wordAddToSentence($is_word, $word, $str, $word_count);
                list ($i, $str) = Word::tagOutWord($text, $i, $str);
                
                // the char is a delimeter or white space
            } elseif (mb_strpos(self::word_delimeters(), $char)!==false || preg_match("/\s/",$char)
                       // if word is ending with a dash, the dash is putting out of the word
                      || $is_word && self::dashIsOutOfWord($char, $text, $i) ) { 
                list ($is_word, $str, $word_count) = self::wordAddToSentence($is_word, $word, $str, $word_count);
                $str .= $char;
            } else {                
                // if word is not started AND (the char is not dash or the next char is not special) THEN the new word started
                if (!$is_word && !self::dashIsOutOfWord($char, $text, $i)) { 
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
        return [$str, $word_count]; 
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
    public static function searchWords($words, $text_ids=[], $lang_ids=[]) {
/*        
$words = [
    1 =>  [
        "w" => "",
        "l" => "vuozi",
        "p" => [ 0 => 1, 1 => 5 ], 
        "g" => [
            "gram_id_case" => [0 => 12], 
            "gram_id_number" => [0 => 1] 
        ] 
    ] 
    2 => [
        "w" => "", 
        "l" => "^sin", 
        "p" => [0 => 10 ],
        "g" => [],
        "d_f" => "1",
        "d_t" => "1" 
    ] 
$words = [
    1 =>  [
        "w" => "",
        "l" => "",
        "p" => [ 0 => 5 ], 
        "g" => [
            "gram_id_case" => [0 => 5] 
        ] 
    ] 
    2 => [
        "w" => "", 
        "l" => "", 
        "p" => [0 => 11 ],
        "g" => [],
        "d_f" => "-1",
        "d_t" => "-1" 
    ] 
]*/
        $table_names = [];
        $word_total = sizeof($words);
        for ($i=1; $i<$word_total; $i++) {
            $table_names[$i] = 'tmp_words_'.$i;
            Schema::create('tmp_words_'.$i, function (Blueprint $table) {
//                $table->integer('text_id')->unsigned();
                $table->integer('sentence_id')->unsigned();
                $table->integer('word_number')->unsigned();
                $table->smallInteger('w_id')->unsigned();
                $table->index(['sentence_id','word_number']);
                $table->temporary();
            });   
            
            $query = 'INSERT INTO tmp_words_'.$i.' SELECT sentence_id, word_number, w_id from words where 1=1 ';
            if (sizeof($text_ids) && $i==1) {
                $query .= 'and text_id in ('.join(',',$text_ids).')';
            }
            $query .= self::searchWordsByWordRaw($words[$i], $lang_ids);
//print "<p>$query";            
            DB::statement($query);
//dd($affected);            
        }
//        $table_names[sizeof($words)] = 'words';
        $select = 't'.$word_total.'.text_id as text1_id, t'.$word_total.'.sentence_id as sentence1_id';
        $from = [];
        if ($word_total>1) {
            for ($i=1; $i<$word_total; $i++) {
                $from[] = $table_names[$i].' as t'.$i; 
                $select .= ', t'.$i.'.w_id as w'.$i.'_id';
//                $w = 
            }
        }
        $from[] = 'words as t'.$word_total; 
        $select .= ', t'.$word_total.'.w_id as w'.$word_total.'_id';
//dd(DB::table('tmp_words_1')->count());        
        $builder = DB::table(DB::raw(join(', ', $from)))->select(DB::raw($select));
//dd(to_sql($builder));            
        if ($word_total>1) {
            $builder = $builder->whereRaw('t1.sentence_id=t2.sentence_id');
//dd($builder->get());            
            $builder = self::searchWordsByNumbers($builder, [2=>$words[sizeof($words)]]);
//dd(to_sql($builder));            
//dd($builder->get());            
        } elseif ($word_total==1 && sizeof($text_ids)) {
            $builder = $builder->whereIn('t1.text_id', $text_ids);
        }
        $builder = self::searchWordsByWord($builder, 't'.$word_total.'.', $words[$word_total], $lang_ids);
//dd(to_sql($builder));            
//dd($builder->get());            
        
/*        $from = [];
        foreach (array_keys($words) as $i) {
            $from[] = 'words as t'.$i;
        }
        $builder = DB::table(DB::raw(join(', ', $from)));
        
        if (sizeof($text_ids)) {
            $builder -> whereIn('t1.text_id', $text_ids);
        }
        
        if (sizeof($words)>1) {
            $where = [];
            for ($i=1; $i<sizeof($words); $i++) {
                $where[] = "t".$i.".sentence_id=t".(int)($i+1).".sentence_id";
            }
            $builder -> whereRaw(join(' AND ', $where));
        }
        $builder = self::searchWordsByNumbers($builder, $words);
        foreach ($words as $i => $word) {
            $builder = self::searchWordsByWord($builder, 't'.$i, $word, $lang_ids);
        }*/
//print "<p>".to_sql($builder);                    
        return $builder;
    }
    
    public static function searchWordsByNumbers($builder, $words) {
//dd($words);            
        foreach ($words as $i => $word) {
            if ($i==1) {
                continue;
            }
            $A = $word['d_f'];
            $B = $word['d_t'];
/*
0<=A<=B: from 1 to 3
t2.word_number>t1.word_number 
AND t2.word_number-t1.word_number>=A 
AND t2.word_number-t1.word_number<=B;
*/            
            if ($A>=0 && $B>=0) {
                if ($A > $B) { // from 3 to 1
                    list($A,$B)=[$B,$A];
                }
                $builder=$builder->whereRaw('t'.$i.'.word_number > t'.($i-1).'.word_number')
                         ->whereRaw('t'.$i.'.word_number-t'.($i-1).'.word_number  >= '. $A)
                         ->whereRaw('t'.$i.'.word_number-t'.($i-1).'.word_number <= '. $B);
//print "<p>".to_sql($builder);            
//dd($builder->take(10)->get());            
/*                       
dd($builder->take(10)->get());            
/*
A<0<B: from -3 to 3
(t1.word_number>t2.word_number 
AND t1.word_number-t2.word_number<=|A|
OR t2.word_number>t1.word_number 
AND t2.word_number-t1.word_number<=B);
*/
            } elseif ($A<0 && $B>0 || $B<0 && $A>0) {
                if ($A > $B) { // from 3 to -3
                    list($A,$B)=[$B,$A];
                }
                $builder=$builder->where(function($q) use ($i, $A, $B) {
                        $q->where('t'.($i-1).'.word_number', '>', 't'.$i.'.word_number')
                          ->where(DB::raw('t'.($i-1).'.word_number-t'.$i.'.word_number'), '<=', abs($A))
                          ->orWhere('t'.$i.'.word_number', '>', 't'.($i-1).'.word_number')
                          ->where(DB::raw('t'.$i.'.word_number-t'.($i-1).'.word_number'), '<=', $B);
                    });
/*                    
B<=A<=0: from -3 to -5
t1.word_number>t2.word_number 
AND t1.word_number-t2.word_number>=|A|
AND t1.word_number-t2.word_number<=|B|;
*/                    
            } elseif ($A<=0 && $B<=0) {
                if ($A < $B) { // from -5 to -3
                    list($A,$B)=[$B,$A];
                }
                $builder=$builder->where('t'.($i-1).'.word_number', '>', 't'.$i.'.word_number')
                        ->where(DB::raw('t'.($i-1).'.word_number-t'.$i.'.word_number'), '>=', abs($A))
                        ->where(DB::raw('t'.($i-1).'.word_number-t'.$i.'.word_number'), '<=', abs($B));
            }
        }
        return $builder;
    }
    
    public static function searchWordsByWordRaw($word, $lang_ids=[]) {
        $search_by_pos = isset($word['p']) && $word['p'] && sizeof($word['p']);
        if ($search_by_pos && PartOfSpeech::isExistNonChangableIDs($word['p'])) {
            return self::searchWordsByWordRawForNotChangablePOS($word, $lang_ids);
        }
        
        $out = '';
        if (isset($word['w']) && $word['w']) {
            $out .= " and word rlike '".$word['w']."'";
        }
        $search_by_lemma = isset($word['l']) && $word['l'];
        $search_by_grams = isset($word['g']) && sizeof($word['g']);
        $search_by_gramset = isset($word['gs']) && $word['gs'];

        if (!$search_by_lemma && !$search_by_pos && !$search_by_grams && !$search_by_gramset) {
            return $out;
        }
        $out .= ' and id in (select word_id from text_wordform where relevance>0';
        
        $lemma_conds = [];
        if ($search_by_lemma) {
            $lemma_conds[] = "lemma_for_search rlike '". $word['l']."'";
        }
        if ($search_by_pos) {
            $lemma_conds[] = 'pos_id in ('. join(', ', $word['p']). ')';
        }
        if (sizeof($lang_ids)) {
            $lemma_conds[] = 'lang_id in ('. join(', ', $lang_ids). ')';                                
        }
        if (sizeof($lemma_conds)) {
            $out .= ' and wordform_id in (select wordform_id from lemma_wordform'
                 .  ' where lemma_id in (select id from lemmas where '
                 . join(' and ', $lemma_conds). '))';
        }
        if ($search_by_gramset) {
            $out .= ' and gramset_id = '.$word['gs'];
        }
        if ($search_by_grams) {
            $gram_conds = [];
            foreach ($word['g'] as $field => $group) {
                $gram_conds[] = $field.' in ('.join(', ', $group). ')';
            }
            $out .= ' and gramset_id in (select id from gramsets where '.join(' and ',$gram_conds).')';
        }                    
        return $out.')';
    }
    
    public static function searchWordsByWordRawForNotChangablePOS($word, $lang_ids=[]) {
        $out = '';
        if (isset($word['w']) && $word['w']) {
            $out .= " and word rlike '".$word['w']."'";
        }

        $lemma_conds = ['pos_id in ('. join(', ', $word['p']). ')'];
        
        if (isset($word['l']) && $word['l']) {
            $lemma_conds[] = "lemma_for_search rlike '". $word['l']."'";
        }
        if (sizeof($lang_ids)) {
            $lemma_conds[] = 'lang_id in ('. join(', ', $lang_ids). ')';                                
        }
        
        return ' and id in (select word_id from meaning_text where relevance>0'
                 . ' and meaning_id in (select id from meanings'
                 . ' where lemma_id in (select id from lemmas where '
                 . join(' and ', $lemma_conds). ')))';
    }
    
    public static function searchWordsByWord($builder, $table_name, $word, $lang_ids=[]) {
        $search_by_pos = isset($word['p']) && $word['p'] && sizeof($word['p']);
        if ($search_by_pos && PartOfSpeech::isExistNonChangableIDs($word['p'])) {
            return self::searchWordsByWordForNotChangablePOS($builder, $table_name, $word, $lang_ids);
        }
        
        if (isset($word['w']) && $word['w']) {
            $builder=$builder->where($table_name.'word', 'rlike', $word['w']);
        }
        $search_by_lemma = isset($word['l']) && $word['l'];
        $search_by_grams = isset($word['g']) && sizeof($word['g']);
        $search_by_gramset = isset($word['gs']) && $word['gs'];

        if ($search_by_lemma || $search_by_pos || $search_by_grams || $search_by_gramset) {
            $builder=$builder->whereIn($table_name.'id', function ($q) use ($word, $search_by_lemma, $search_by_pos, $search_by_grams, $search_by_gramset, $lang_ids) {
                $q->select('word_id')->from('text_wordform')
                  ->where('relevance', '>', 0);
                if ($search_by_lemma || $search_by_pos) {
                    $q->whereIn('wordform_id',function($query1) use ($word, $search_by_lemma, $search_by_pos, $lang_ids){
                        $query1->select('wordform_id')
                        ->from('lemma_wordform')
                        ->whereIn('lemma_id',function($query2) use ($word, $search_by_lemma, $search_by_pos, $lang_ids){
                            $query2->select('id')->from('lemmas');
                            if ($search_by_lemma) {
                                $query2->where('lemma_for_search', 'rlike', $word['l']);
                            }
                            if ($search_by_pos) {
                                $query2->whereIn('pos_id', $word['p']);
                            }
                            if (sizeof($lang_ids)) {
                                $query2->whereIn('lang_id', $lang_ids);                                
                            }
                        });
                    });
                }
                if ($search_by_gramset) {
                    $q->whereGramsetId($word['gs']);
                }
                if ($search_by_grams) {
                    $q->whereIn('gramset_id',function($query2) use ($word){
                        $query2->select('id')->from('gramsets');
                        foreach ($word['g'] as $field => $group) {
                            $query2->whereIn($field, $group);
                        }
                    });
                }                    
            });
        }
        return $builder;
    }
    
    public static function searchWordsByWordForNotChangablePOS($builder, $table_name, $word, $lang_ids=[]) {
        if (isset($word['w']) && $word['w']) {
            $builder=$builder->where($table_name.'word', 'rlike', $word['w']);
        }
        $search_by_lemma = isset($word['l']) && $word['l'];

        return $builder->whereIn($table_name.'id', function ($q) use ($word, $search_by_lemma, $lang_ids) {
            $q->select('word_id')->from('meaning_text')
              ->where('relevance', '>', 0)
              ->whereIn('meaning_id',function($query1) use ($word, $search_by_lemma, $lang_ids){
                $query1->select('id')->from('meanings')
                ->whereIn('lemma_id',function($query2) use ($word, $search_by_lemma, $lang_ids){
                    $query2->select('id')->from('lemmas')
                           ->whereIn('pos_id', $word['p']);
                    if ($search_by_lemma) {
                        $query2->where('lemma_for_search', 'rlike', $word['l']);
                    }
                    if (sizeof($lang_ids)) {
                        $query2->whereIn('lang_id', $lang_ids);                                
                    }
                });
            });
        });
    }
    
    public static function searchTexts(Array $url_args) {
        $texts = Text::select('id');        
        if ($url_args['search_corpus']) {
            $texts = $texts->whereIn('corpus_id',$url_args['search_corpus']);
        } 
        $texts = Text::searchByDialects($texts, $url_args['search_dialect']);
        $texts = Text::searchByGenres($texts, $url_args['search_genre']);
        $texts = Text::searchByLang($texts, $url_args['search_lang']);
        $texts = Text::searchByYear($texts, $url_args['search_year_from'], $url_args['search_year_to']);
//dd(to_sql($texts));
        return $texts->get()->pluck('id')->toArray();
    }
    /*
     * select * from gramsets where gram_id_mood in (27) and gram_id_tense in (24) and gram_id_number in (1,2) and gram_id_person in (21, 22);
     */
    public static function preparedWordsForSearch($words) {
        $out = [];
//dd($words);        
        foreach ($words as $i=>$word) {
            if ((!isset($word['w']) || !$word['w']) && (!isset($word['p']) || !$word['p']) && (!isset($word['g']) || !$word['g']) && (!isset($word['gs']) || !$word['gs'])) {
                break;
            }
//            $out[$i]['w'] = Grammatic::toSearchForm($word['w']);
            $out[$i]['w'] = $out[$i]['l'] = $out[$i]['p'] = $out[$i]['g']  = $out[$i]['gs'] = [];
            
            if (isset($word['w']) && $word['w']) {
                if (preg_match("/^\"(.+)\"$/", trim($word['w']), $regs)) {
                    $out[$i]['w'] = Grammatic::toSearchByPattern($regs[1]);
                } else {
                    $out[$i]['l'] = Grammatic::toSearchByPattern($word['w']);                
                }
            }
            
            foreach (preg_split('/\|/', $word['p']) as $p_code) {
                $p_id = PartOfSpeech::getIDByCode(trim($p_code));
//dd($p_id, $p_code);                
                if ($p_id) {
                    $out[$i]['p'][] = $p_id;
                }
            }

            if (isset($word['g']) && $word['g']) {
                foreach (preg_split('/,/', $word['g']) as $orGroup) {
                    foreach (preg_split('/\|/', $orGroup) as $g_code) {
                        $gram = Gram::getByCode(trim($g_code));
                        $out[$i]['g']['gram_id_'.$gram->gramCategory->name_en][] = $gram->id; 
                    }
                }
            }

            if (isset($word['gs']) && $word['gs']) {
                $out[$i]['gs'] = (int)$word['gs'];
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
//dd(Sentence::searchTexts($args));
        $builder = self::searchWords($args['words'], Sentence::searchTexts($args), $args['search_lang']);
//dd(to_sql($builder));        
//dd($builder->get());            
//        return sizeof($builder->get());
        return [sizeof($builder->get()), $builder];
//        return [$builder->count(), $builder];
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
        if (!isset($url_args['search_words'][1]['p'])) {
            $url_args['search_words'][1]['p'] = '';
        }
        $last_word = $url_args['search_words'][sizeof($url_args['search_words'])];
        if (sizeof($url_args['search_words'])>1 && (!isset($last_word['w']) || !$last_word['w'])
            && (!isset($last_word['p']) || !$last_word['p'])
            && (!isset($last_word['g']) || !$last_word['g'])) {
            unset($url_args['search_words'][sizeof($url_args['search_words'])]);
        }
        return $url_args;
    }    
    
    public static function searchQueryToString($args) {
        $out = self::searchQueryToStringMeta($args);
        
        foreach ($args['search_words'] as $i => $word) {
            if (!isset($word['w']) && !isset($word['p']) && !isset($word['g'])) {
                continue;
            }
            
            $tmp=[];
            if (isset($word['w']) && $word['w']) {
                $tmp[] = '<i>'.$word['w'].'</i>';
            }
            if ($word['p']) {
                $tmp[] = '('.join(' <span class="warning">'.trans('search.or').'</span> ',
                            array_map(function ($code) {return PartOfSpeech::getNameByCode(trim($code)); },
                                    preg_split('/\|/',$word['p']))).')';
            }
            if (isset($word['g']) && $word['g']) {
                $groups = [];
                foreach (preg_split('/\,/',$word['g']) as $gr) {
                    $groups[] = '('.join(' <span class="warning">'.trans('search.or').'</span> ',
                            array_map(function ($code) {return Gram::getNameByCode(trim($code)); },
                                    preg_split('/\|/',$gr))).')';
                    
                }
                $tmp[] = '('.join(' <span class="warning">'.trans('search.and').'</span> ', $groups).')';
            }
            if (isset($word['gs']) && $word['gs']) {
                $tmp[] = Gramset::getStringByID($word['gs']);
            }
            
            $out[] = //'<br>'.
                    (isset($word['d_f']) && isset($word['d_t']) 
                    ? trans('search.in_distance', ['from'=>$word['d_f'], 'to'=>$word['d_t']]) : '')
                    .' <b>'.trans('corpus.word'). " $i</b>: ".join(' <span class="warning">'.trans('search.and').'</span> ',$tmp);
        } 
        return join(' <span class="warning">'.trans('search.and').'</span><br>', $out);
    }
    
    public static function searchQueryToStringMeta($args) {
        $meta = [];
        if (sizeof($args['search_lang'])) {
            $meta[] = '(<b>'.trans('dict.lang'). '</b>: '. join(' <span class="warning">'.trans('search.or').'</span> ', 
                    array_map(function ($id) {return Lang::getNameByID($id); }, 
                            $args['search_lang'])).')';
        }
        if (sizeof($args['search_dialect'])) {
            $meta[] = '(<b>'.trans('dict.dialect'). '</b>: '. join(' <span class="warning">'.trans('search.or').'</span> ',
                    array_map(function ($id) {return Dialect::getNameByID($id); }, 
                            $args['search_dialect'])).')';
        }
        if (sizeof($args['search_corpus'])) {
            $meta[] = '(<b>'.trans('corpus.corpus'). '</b>: '. join(' <span class="warning">'.trans('search.or').'</span> ',
                    array_map(function ($id) {return Corpus::getNameByID($id); }, 
                            $args['search_corpus'])).')';
        }
        if (sizeof($args['search_genre'])) {
            $meta[] = '(<b>'.trans('corpus.genre'). '</b>: '. join(' <span class="warning">'.trans('search.or').'</span> ',
                    array_map(function ($id) {return Genre::getNameByID($id); }, 
                            $args['search_genre'])).')';
        }
        if ($args['search_year_from']) {
            $meta[] = '<b>'.trans('search.year_from'). '</b>: '. 
                            $args['search_year_from'].'';
        }
        if ($args['search_year_to']) {
            $meta[] = '<b>'.trans('search.year_to'). '</b>: '. 
                            $args['search_year_to'].'';
        }
    
        return sizeof($meta) ? [join(' <span class="warning">'.trans('search.and').'</span> ', $meta)] : [];
    }    
    
    /**
     * Устанавить разметку с блоками слов

     * @param array $search_w         - array ID of searching word object
     * 
     * @return string                 - transformed text with markup tags
     **/
    public function addWordBlocks($search_w=[], $markup_text=null){
        if (!$markup_text) {
            $markup_text = $this->text_xml;
        }
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
    
    /**
     * Устанавить разметку с блоками слов

     * @param array $search_w         - array ID of searching word object
     * 
     * @return string                 - transformed text with markup tags
     **/
    public function markSearchWords($search_w=[], $markup_text=null){
        if (!$markup_text) {
            $markup_text = $this->text_xml;
        }
        list($sxe,$error_message) = Text::toXML($markup_text,'');
        if ($error_message || !sizeof($search_w)) {
            return $markup_text;
        }

        $words = $sxe->xpath('//w');
        foreach ($words as $word) {
//            $word = $this->addWordBlock($word, $search_w);
            $w_id = (int)$word->attributes()->id;
            if (!$w_id) { continue; }
            if (in_array($w_id,$search_w)) {
                $word->addAttribute('class', 'word-marked');
            }
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
