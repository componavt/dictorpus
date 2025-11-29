<?php namespace App\Traits\Search;

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

use App\Models\Corpus\Genre;
use App\Models\Corpus\Sentence;
use App\Models\Corpus\Text;

trait SentenceSearch
{
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
    
    public static function searchTexts(Array $url_args) {
        if (empty($url_args['search_corpus']) && empty($url_args['search_dialect']) && empty($url_args['search_genre']) && empty($url_args['search_lang']) && empty($url_args['search_year_from']) && empty($url_args['search_year_to'])) {
            return null;
        }
        $texts = Text::select('id');        
        $texts = Text::searchByCorpuses($texts, $url_args['search_corpus']);
        $texts = Text::searchByDialects($texts, $url_args['search_dialect']);
        $texts = Text::searchByGenres($texts, $url_args['search_genre']);
        $texts = Text::searchByLang($texts, $url_args['search_lang']);
        $texts = Text::searchByYear($texts, $url_args['search_year_from'], $url_args['search_year_to']);
//dd(to_sql($texts));
        return $texts->get()->pluck('id')->toArray();
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
        if (!empty($lang_ids) && !empty($lang_ids[0])) {
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
    
/* скорее всего уже не используются
 * 
    public static function searchWordsByWordRawForNotChangablePOS($word, $lang_ids=[]) {
        $out = '';
        if (isset($word['w']) && $word['w']) {
            $out .= " and word rlike '".$word['w']."'";
        }

        $lemma_conds = ['pos_id in ('. join(', ', $word['p']). ')'];
        
        if (isset($word['l']) && $word['l']) {
            $lemma_conds[] = "lemma_for_search rlike '". $word['l']."'";
        }
        if (!empty($lang_ids) && !empty($lang_ids[0])) {
            $lemma_conds[] = 'lang_id in ('. join(', ', $lang_ids). ')';                                
        }
        
        return ' and id in (select word_id from meaning_text where relevance>0'
                 . ' and meaning_id in (select id from meanings'
                 . ' where lemma_id in (select id from lemmas where '
                 . join(' and ', $lemma_conds). ')))';
    }
 */
    
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
            $out[$i]['w'] = $out[$i]['l']  = $out[$i]['gs'] = null;
            $out[$i]['p'] = $out[$i]['g'] = [];
            
            if (!empty($word['w'])) {
                $word['w'] = trim($word['w']);                
                if (preg_match("/^\"(.+)\"$/", $word['w'], $regs)) {
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

            if (!empty($word['g'])) {
                foreach (preg_split('/,/', $word['g']) as $orGroup) {
                    foreach (preg_split('/\|/', $orGroup) as $g_code) {
                        $gram = Gram::getByCode(trim($g_code));
                        $out[$i]['g']['gram_id_'.$gram->gramCategory->name_en][] = $gram->id; 
                    }
                }
            }

            if (!empty($word['gs'])) {
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
     * select count(*) from `text_wordform` where `relevance` > 0 and `wordform_id` in (select `wordform_id` from `lemma_wordform` where `lemma_id` in (select `id` from `lemmas` where `lemma_for_search` like 'kačahtuakseh'))
     * 
     * @param type $word1
     * @param type $word2
     * @param type $distance_from
     * @param type $distance_to
     * @return collection
     */
    public static function entryNumber($args) {
//dd($args['words']);        
        $texts = Sentence::searchTexts($args);
        if (!is_array($texts)) {
            return [0, null];
        }
        $builder = self::searchWords($args['words'], $texts, $args['search_lang'])
                ->limit(5000); // !!! ограничение
        
        // Создаём отдельный подзапрос для подсчёта
        $countQuery = "SELECT COUNT(*) as count FROM (
              " . $builder->toSql() . "
        ) as unique_sentences";

        $count = DB::select($countQuery, $builder->getBindings())[0]->count;
//dd(to_sql($builder));
        return [(int) $count, $builder];
    }
    
/*
 *  Выносим логику дистанции в отдельный метод
0<=A<=B: from 1 to 3
t2.word_number>t1.word_number 
AND t2.word_number-t1.word_number>=A 
AND t2.word_number-t1.word_number<=B;

A<0<B: from -3 to 3
(t1.word_number>t2.word_number 
AND t1.word_number-t2.word_number<=|A|
OR t2.word_number>t1.word_number 
AND t2.word_number-t1.word_number<=B);

B<=A<=0: from -3 to -5
t1.word_number>t2.word_number 
AND t1.word_number-t2.word_number>=|A|
AND t1.word_number-t2.word_number<=|B|;
*/                    
    private static function applyDistanceCondition($join, $i, $A, $B) {
        if ($A >= 0 && $B >= 0) {
            if ($A > $B) { list($A, $B) = [$B, $A]; }
            $join->where(DB::raw("t{$i}.word_number"), '>', DB::raw("t".($i-1).".word_number"))
                 ->where(DB::raw("t{$i}.word_number - t".($i-1).".word_number"), '>=', $A)
                 ->where(DB::raw("t{$i}.word_number - t".($i-1).".word_number"), '<=', $B);
        } elseif ($A < 0 && $B > 0) {
            $join->where(function ($q) use ($i, $A, $B) {
                $q->where(DB::raw("t".($i-1).".word_number"), '>', DB::raw("t{$i}.word_number"))
                  ->where(DB::raw("t".($i-1).".word_number - t{$i}.word_number"), '<=', abs($A))
                  ->orWhere(function ($qq) use ($i, $B) {
                      $qq->where(DB::raw("t{$i}.word_number"), '>', DB::raw("t".($i-1).".word_number"))
                         ->where(DB::raw("t{$i}.word_number - t".($i-1).".word_number"), '<=', $B);
                  });
            });
        } elseif ($A <= 0 && $B <= 0) {
            if ($A < $B) { list($A, $B) = [$B, $A]; }
            $join->where(DB::raw("t".($i-1).".word_number"), '>', DB::raw("t{$i}.word_number"))
                 ->where(DB::raw("t".($i-1).".word_number - t{$i}.word_number"), '>=', abs($A))
                 ->where(DB::raw("t".($i-1).".word_number - t{$i}.word_number"), '<=', abs($B));
        }
    }    
    
/*
 *  старая функция
0<=A<=B: from 1 to 3
t2.word_number>t1.word_number 
AND t2.word_number-t1.word_number>=A 
AND t2.word_number-t1.word_number<=B;

A<0<B: from -3 to 3
(t1.word_number>t2.word_number 
AND t1.word_number-t2.word_number<=|A|
OR t2.word_number>t1.word_number 
AND t2.word_number-t1.word_number<=B);

B<=A<=0: from -3 to -5
t1.word_number>t2.word_number 
AND t1.word_number-t2.word_number>=|A|
AND t1.word_number-t2.word_number<=|B|;
*/                    
    public static function searchWordsByNumbers($builder, $words) {
        foreach ($words as $i => $word) {
            if ($i==1) {
                continue;
            }
            $A = $word['d_f'];
            $B = $word['d_t'];
            
            if ($A>=0 && $B>=0) { // from 3 to 1
                if ($A > $B) { 
                    list($A,$B)=[$B,$A];
                }
                $builder->whereRaw('t'.$i.'.word_number > t'.($i-1).'.word_number')
                         ->whereRaw('t'.$i.'.word_number-t'.($i-1).'.word_number  >= '. $A)
                         ->whereRaw('t'.$i.'.word_number-t'.($i-1).'.word_number <= '. $B);
                
            } elseif ($A<0 && $B>0 || $B<0 && $A>0) { // from 3 to -3
                if ($A > $B) { 
                    list($A,$B)=[$B,$A];
                }
                $builder->where(function($q) use ($i, $A, $B) {
                        $q->where('t'.($i-1).'.word_number', '>', 't'.$i.'.word_number')
                          ->where(DB::raw('t'.($i-1).'.word_number-t'.$i.'.word_number'), '<=', abs($A))
                          ->orWhere('t'.$i.'.word_number', '>', 't'.($i-1).'.word_number')
                          ->where(DB::raw('t'.$i.'.word_number-t'.($i-1).'.word_number'), '<=', $B);
                    });
            } elseif ($A<=0 && $B<=0) { // from -5 to -3
                if ($A < $B) { 
                    list($A,$B)=[$B,$A];
                }
                $builder->where('t'.($i-1).'.word_number', '>', 't'.$i.'.word_number')
                        ->where(DB::raw('t'.($i-1).'.word_number-t'.$i.'.word_number'), '>=', abs($A))
                        ->where(DB::raw('t'.($i-1).'.word_number-t'.$i.'.word_number'), '<=', abs($B));
            }
        }
        return $builder;
    }

    public static function searchWordsByWord($builder, $table_name, $word, $lang_ids=[]) {
//dd($word);        
//\Log::info("searchWordsByWord start", ['table_name' => $table_name, 'word' => $word]);
    
        $search_by_pos = !empty($word['p']) && sizeof($word['p']);
        if ($search_by_pos && PartOfSpeech::isExistNonChangableIDs($word['p'])) {
            return self::searchWordsByWordForNotChangablePOS($builder, $table_name, $word, $lang_ids);
        }
        
        if (!empty($word['w']) && is_string($word['w'])) {
//\Log::info("Adding where word", ['word' => $word['w']]);
            $builder=$builder->where($table_name.'word', 'rlike', $word['w']);
        }
        $search_by_lemma = !empty($word['l']) && is_string($word['l']);
        $search_by_grams = !empty($word['g']) && sizeof($word['g']);
        $search_by_gramset = !empty($word['gs']) && is_numeric($word['gs']);

        if (!$search_by_lemma && !$search_by_pos && !$search_by_grams && !$search_by_gramset) {
            return $builder;
        }
        $twf_alias = 'twf_' . str_replace('.', '_', $table_name);
        $builder->join("text_wordform as {$twf_alias}", function ($join) use ($table_name, $twf_alias) {
            $join->on("{$twf_alias}.word_id", '=', DB::raw($table_name.'id'));
        })
        ->where("{$twf_alias}.relevance", '>', 0);

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
                        if (!empty($lang_ids) && !empty($lang_ids[0])) {
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
       
//\Log::info("searchWordsByWord end");
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
                    if (!empty($lang_ids) && !empty($lang_ids[0])) {
                        $query2->whereIn('lang_id', $lang_ids);                                
                    }
                });
            });
        });
    }
    
public static function searchWordsBySteps($words, $text_ids = [], $lang_ids = []) {
    $word_total = count($words);

    if ($word_total === 0) return DB::table('words')->whereRaw('1=0');
    if ($word_total === 1) return self::searchWordsByWordStep($words[1], $text_ids, $lang_ids);

    // Шаг 1: Ищем все пары первых двух слов
    $firstTwo = array_slice($words, 0, 2);
    $pairBuilder = self::searchWordsByWordStep($firstTwo[1], $text_ids, $lang_ids)
        ->join('words as t2', function ($join) use ($firstTwo) {
            $join->on('t2.sentence_id', '=', 't1.sentence_id')
                 ->whereRaw('t2.word_number > t1.word_number')
                 ->whereRaw('t2.word_number - t1.word_number >= ? AND t2.word_number - t1.word_number <= ?', [
                     $firstTwo[2]['d_f'] ?? 1,
                     $firstTwo[2]['d_t'] ?? 1
                 ]);
        })
        ->select('t1.text_id as text1_id', 't1.sentence_id as sentence1_id', 't1.w_id as w1_id', 't2.w_id as w2_id');

    // Ограничиваем результаты — критично!
    $pairResults = $pairBuilder->limit(5000)->get();

    if ($pairResults->isEmpty()) {
        return DB::table('words')->whereRaw('1=0');
    }

    // Если 3 слова — ищем третье в этих предложениях
    if ($word_total >= 3) {
        $sentenceIds = $pairResults->pluck('sentence1_id')->unique()->toArray();

        $w3 = $words[3];
        $w3Builder = DB::table('words as t3')
            ->whereIn('t3.sentence_id', $sentenceIds)
            ->select('t3.sentence_id as sentence1_id', 't3.w_id as w3_id', 't3.word_number as w3_word_number');

        $w3Builder = self::searchWordsByWordStep($w3, null, $lang_ids, 't3.');

        $w3Results = $w3Builder->get();

        // Фильтруем в PHP по дистанции
        $finalResults = collect();

        foreach ($pairResults as $pair) {
            $sentenceId = $pair->sentence1_id;
            $w1_word_number = DB::table('words')
                ->where('sentence_id', $sentenceId)
                ->where('w_id', $pair->w1_id)
                ->value('word_number');

            $w2_word_number = DB::table('words')
                ->where('sentence_id', $sentenceId)
                ->where('w_id', $pair->w2_id)
                ->value('word_number');

            $matchingW3 = $w3Results->where('sentence1_id', $sentenceId);

            foreach ($matchingW3 as $w3) {
                $w3_word_number = $w3->w3_word_number;

                // Проверяем дистанцию между w2 и w3
                $dist_w2_w3 = abs($w3_word_number - $w2_word_number);
                $d_f = $w3['d_f'] ?? 1;
                $d_t = $w3['d_t'] ?? 1;

                if ($dist_w2_w3 >= $d_f && $dist_w2_w3 <= $d_t) {
                    $finalResults->push([
                        'text1_id' => $pair->text1_id,
                        'sentence1_id' => $sentenceId,
                        'w1_id' => $pair->w1_id,
                        'w2_id' => $pair->w2_id,
                        'w3_id' => $w3->w3_id,
                    ]);
                }
            }
        }

        // Возвращаем билдер с результатами
        if ($finalResults->isEmpty()) {
            return DB::table('words')->whereRaw('1=0');
        }

        return DB::table(DB::raw('(' . implode(',', $finalResults->pluck('sentence1_id')->map(fn($id) => "($id)") . ')') . ') as fake'))
            ->selectRaw('text1_id, sentence1_id, w1_id, w2_id, w3_id')
            ->whereIn('sentence1_id', $finalResults->pluck('sentence1_id')->toArray());
    }

    // Если только 2 слова — возвращаем пары
    return DB::table(DB::raw('(' . implode(',', $pairResults->pluck('sentence1_id')->map(fn($id) => "($id)") . ')') . ') as fake'))
        ->selectRaw('text1_id, sentence1_id, w1_id, w2_id')
        ->whereIn('sentence1_id', $pairResults->pluck('sentence1_id')->toArray());
}    
    
    /**
     *  $words = [
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
        ]
     */
    public static function searchWords($words, $text_ids=[], $lang_ids=[]) {
        $table_names = [];
        $word_total = sizeof($words);
        for ($i=1; $i<$word_total; $i++) {
            $table_names[$i] = 'tmp_words_'.$i;
            Schema::create('tmp_words_'.$i, function (Blueprint $table) {
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
            DB::statement($query);
        }
        $select = 't'.$word_total.'.text_id as text1_id, t'.$word_total.'.sentence_id as sentence1_id';
        $from = [];
        if ($word_total>1) {
            for ($i=1; $i<$word_total; $i++) {
                $from[] = $table_names[$i].' as t'.$i; 
                $select .= ', t'.$i.'.w_id as w'.$i.'_id';
            }
        }
        $from[] = 'words as t'.$word_total; 
        $select .= ', t'.$word_total.'.w_id as w'.$word_total.'_id';
        
        $builder = DB::table(DB::raw(join(', ', $from)))->select(DB::raw($select));
        
        if ($word_total>1) {
            // Связываем все таблицы по sentence_id
            for ($i = 2; $i <= $word_total; $i++) {
                $builder = $builder->whereRaw("t{$i}.sentence_id = t1.sentence_id");
            }

            // Применяем дистанцию для каждого слова, начиная со 2-го
            for ($i = 2; $i <= $word_total; $i++) {
                $builder = self::searchWordsByNumbers($builder, [$i => $words[$i]]);
            }
        } elseif ($word_total==1 && sizeof($text_ids)) {
            $builder = $builder->whereIn('t1.text_id', $text_ids);
        }
        $builder = self::searchWordsByWord($builder, 't'.$word_total.'.', $words[$word_total], $lang_ids);
        
        return $builder; 
    }
 
}