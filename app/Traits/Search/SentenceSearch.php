<?php

namespace App\Traits\Search;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Library\Grammatic;
use App\Library\Str;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gram;
use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\PartOfSpeech;

use App\Models\Corpus\Corpus;
use App\Models\Corpus\Genre;
use App\Models\Corpus\Sentence;
use App\Models\Corpus\Putype;
use App\Models\Corpus\Text;

trait SentenceSearch
{
    public static function urlArgs($request)
    {
        $url_args = Str::urlArgs($request) + [
            'only_checked'  => (int)$request->input('only_checked'),
            'search_corpus'   => (array)$request->input('search_corpus'),
            'search_dialect'  => (array)$request->input('search_dialect'),
            'search_genre'    => (array)$request->input('search_genre'),
            'search_lang'     => (array)$request->input('search_lang'),
            'search_year_from' => (int)$request->input('search_year_from'),
            'search_year_to'  => (int)$request->input('search_year_to'),

            'search_words' => (array)$request->input('search_words'),
        ];

        if (!isset($url_args['search_words'][1])) {
            $url_args['search_words'][1]['w'] = '';
        }
        if (!isset($url_args['search_words'][1]['p'])) {
            $url_args['search_words'][1]['p'] = '';
        }

        foreach ($url_args['search_words'] as $i => $word) {
            if (!isset($url_args['search_words'][$i]['bt_mode']) || !$url_args['search_words'][$i]['bt_mode']) {
                $url_args['search_words'][$i]['bt_mode'] = 'ignore';
            }
            if (!isset($url_args['search_words'][$i]['bt_types']) || !is_array($url_args['search_words'][$i]['bt_types'])) {
                $url_args['search_words'][$i]['bt_types'] = [];
            }
        }

        $last_word = $url_args['search_words'][sizeof($url_args['search_words'])];
        if (
            sizeof($url_args['search_words']) > 1 && (!isset($last_word['w']) || !$last_word['w'])
            && (!isset($last_word['p']) || !$last_word['p'])
            && (!isset($last_word['g']) || !$last_word['g'])
        ) {
            unset($url_args['search_words'][sizeof($url_args['search_words'])]);
        }

        return $url_args;
    }

    public static function searchTexts(array $url_args)
    {
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

    public static function searchWordsByWordRaw($word, $lang_ids = [])
    {
        $search_by_pos = isset($word['p']) && $word['p'] && sizeof($word['p']);
        if ($search_by_pos && PartOfSpeech::isExistNonChangableIDs($word['p'])) {
            return self::searchWordsByWordRawForNotChangablePOS($word, $lang_ids);
        }

        $out = '';
        if (isset($word['w']) && $word['w']) {
            $out .= " and word rlike '" . $word['w'] . "'";
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
            $lemma_conds[] = "lemma_for_search rlike '" . $word['l'] . "'";
        }
        if ($search_by_pos) {
            $lemma_conds[] = 'pos_id in (' . join(', ', $word['p']) . ')';
        }
        if (!empty($lang_ids) && !empty($lang_ids[0])) {
            $lemma_conds[] = 'lang_id in (' . join(', ', $lang_ids) . ')';
        }
        if (sizeof($lemma_conds)) {
            $out .= ' and wordform_id in (select wordform_id from lemma_wordform'
                .  ' where lemma_id in (select id from lemmas where '
                . join(' and ', $lemma_conds) . '))';
        }
        if ($search_by_gramset) {
            $out .= ' and gramset_id = ' . $word['gs'];
        }
        if ($search_by_grams) {
            $gram_conds = [];
            foreach ($word['g'] as $field => $group) {
                $gram_conds[] = $field . ' in (' . join(', ', $group) . ')';
            }
            $out .= ' and gramset_id in (select id from gramsets where ' . join(' and ', $gram_conds) . ')';
        }
        return $out . ')';
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
    public static function preparedWordsForSearch($words)
    {
        $out = [];

        foreach ($words as $i => $word) {
            if ((!isset($word['w']) || !$word['w'])
                && (!isset($word['p']) || !$word['p'])
                && (!isset($word['g']) || !$word['g'])
                && (!isset($word['gs']) || !$word['gs'])
            ) {
                break;
            }

            $out[$i]['w']  = $out[$i]['l']  = $out[$i]['gs'] = null;
            $out[$i]['p']  = $out[$i]['g']  = [];
            $out[$i]['pb'] = [];
            $out[$i]['pa'] = [];
            $out[$i]['bt_mode']  = 'ignore';
            $out[$i]['bt_types'] = [];

            // ---- слово/часть речи/грампризнаки (как было) ----

            if (!empty($word['w'])) {
                $word['w'] = trim($word['w']);
                if (preg_match('/^\"(.+)\"$/u', $word['w'], $regs)) {
                    $out[$i]['w'] = Grammatic::toSearchByPattern($regs[1]);
                } else {
                    $out[$i]['l'] = Grammatic::toSearchByPattern($word['w']);
                }
            }

            foreach (preg_split('/\|/', $word['p']) as $p_code) {
                $p_id = PartOfSpeech::getIDByCode(trim($p_code));
                if ($p_id) {
                    $out[$i]['p'][] = $p_id;
                }
            }

            if (!empty($word['g'])) {
                foreach (preg_split('/,/', $word['g']) as $orGroup) {
                    foreach (preg_split('/\|/', $orGroup) as $g_code) {
                        $gram = Gram::getByCode(trim($g_code));
                        $out[$i]['g']['gram_id_' . $gram->gramCategory->name_en][] = $gram->id;
                    }
                }
            }

            if (!empty($word['gs'])) {
                $out[$i]['gs'] = (int)$word['gs'];
            }

            // ---- дистанция как раньше ----

            if ($i > 1) {
                $out[$i]['d_f'] = $word['d_f'] ?? 1;
                $out[$i]['d_t'] = $word['d_t'] ?? 1;
            }

            // ---- пунктуация перед/после слова ----

            $out[$i]['pb'] = self::normalizePunctField($word['pb'] ?? []);
            $out[$i]['pa'] = self::normalizePunctField($word['pa'] ?? []);

            // ---- пунктуация между словами (режим + типы; пока только any) ----

            if ($i > 1) {
                $bt_mode = $word['bt_mode'] ?? 'ignore';
                if (!in_array($bt_mode, ['ignore', 'require_any', 'forbid_any'], true)) {
                    $bt_mode = 'ignore';
                }
                $out[$i]['bt_mode'] = $bt_mode;

                // на будущее: конкретные типы
                $out[$i]['bt_types'] = self::normalizePunctField($word['bt'] ?? []);
            }
        }

        return $out;
    }

    protected static function normalizePunctField($value): array
    {
        if (is_string($value)) {
            $value = preg_split('/[,|]/u', $value);
        }

        if (!is_array($value)) {
            return [];
        }

        $out = [];

        foreach ($value as $item) {
            $item = trim((string)$item);
            if ($item === '') {
                continue;
            }
            // допустим либо 'any', либо slug из putypes
            if ($item === 'any') {
                $out[] = 'any';
                continue;
            }
            $out[] = $item;
        }

        // убираем дубли
        return array_values(array_unique($out));
    }

    public static function searchQueryToString($args)
    {
        $out = self::searchQueryToStringMeta($args);

        foreach ($args['search_words'] as $i => $word) {
            if (!isset($word['w']) && !isset($word['p']) && !isset($word['g'])) {
                continue;
            }

            $tmp = [];

            if (isset($word['w']) && $word['w']) {
                $tmp[] = '<i>' . e($word['w']) . '</i>';
            }

            if ($word['p']) {
                $tmp[] = '(' . join(
                    ' <span class="warning">' . trans('search.or') . '</span> ',
                    array_map(function ($code) {
                        return PartOfSpeech::getNameByCode(trim($code));
                    }, preg_split('/\|/', $word['p']))
                ) . ')';
            }

            if (isset($word['g']) && $word['g']) {
                $groups = [];
                foreach (preg_split('/\,/', $word['g']) as $gr) {
                    $groups[] = '(' . join(
                        ' <span class="warning">' . trans('search.or') . '</span> ',
                        array_map(function ($code) {
                            return Gram::getNameByCode(trim($code));
                        }, preg_split('/\|/', $gr))
                    ) . ')';
                }
                $tmp[] = '(' . join(' <span class="warning">' . trans('search.and') . '</span> ', $groups) . ')';
            }

            if (isset($word['gs']) && $word['gs']) {
                $tmp[] = Gramset::getStringByID($word['gs']);
            }

            // ---- добавим краткое описание пунктуации ----
            if (!empty($word['pb'])) {
                $tmp[] = trans('search.punct_before') . ': ' . self::punctListToString($word['pb']);
            }

            if (!empty($word['pa'])) {
                $tmp[] = trans('search.punct_after') . ': ' . self::punctListToString($word['pa']);
            }

            if (isset($word['bt_mode']) && $word['bt_mode'] && $i > 1) {
                $tmp[] = self::betweenModeToString($word['bt_mode']);
            }

            if (!empty($word['bt_types']) && is_array($word['bt_types']) && $i > 1) {
                $putypes = Putype::whereIn('slug', $word['bt_types'])->pluck('name_' . LaravelLocalization::getCurrentLocale(), 'slug')->toArray();

                if (!empty($putypes)) {
                    $tmp[] = trans('search.putypes') . ': ' . join(', ', $putypes);
                }
            }

            $out[] =
                (isset($word['d_f']) && isset($word['d_t'])
                    ? trans('search.in_distance', ['from' => $word['d_f'], 'to' => $word['d_t']]) : '')
                . ' <b>' . trans('corpus.word') . " $i</b>: "
                . join(' <span class="warning">' . trans('search.and') . '</span> ', $tmp);
        }

        return join(' <span class="warning">' . trans('search.and') . '</span><br>', $out);
    }

    protected static function punctListToString(array $slugs): string
    {
        if (in_array('any', $slugs, true)) {
            return trans('search.punct_any');
        }

        // можно сделать маппинг slug → человекочитаемое имя, сейчас просто slug’и
        return implode(', ', array_map('e', $slugs));
    }

    protected static function betweenModeToString(string $mode): string
    {
        switch ($mode) {
            case 'require_any':
                return trans('search.punct_between_require_any');
            case 'forbid_any':
                return trans('search.punct_between_forbid_any');
            default:
                return '';
        }
    }

    public static function searchQueryToStringMeta($args)
    {
        $meta = [];
        if (sizeof($args['search_lang'])) {
            $meta[] = '(<b>' . trans('dict.lang') . '</b>: ' . join(
                ' <span class="warning">' . trans('search.or') . '</span> ',
                array_map(
                    function ($id) {
                        return Lang::getNameByID($id);
                    },
                    $args['search_lang']
                )
            ) . ')';
        }
        if (sizeof($args['search_dialect'])) {
            $meta[] = '(<b>' . trans('dict.dialect') . '</b>: ' . join(
                ' <span class="warning">' . trans('search.or') . '</span> ',
                array_map(
                    function ($id) {
                        return Dialect::getNameByID($id);
                    },
                    $args['search_dialect']
                )
            ) . ')';
        }
        if (sizeof($args['search_corpus'])) {
            $meta[] = '(<b>' . trans('corpus.corpus') . '</b>: ' . join(
                ' <span class="warning">' . trans('search.or') . '</span> ',
                array_map(
                    function ($id) {
                        return Corpus::getNameByID($id);
                    },
                    $args['search_corpus']
                )
            ) . ')';
        }
        if (sizeof($args['search_genre'])) {
            $meta[] = '(<b>' . trans('corpus.genre') . '</b>: ' . join(
                ' <span class="warning">' . trans('search.or') . '</span> ',
                array_map(
                    function ($id) {
                        return Genre::getNameByID($id);
                    },
                    $args['search_genre']
                )
            ) . ')';
        }
        if ($args['search_year_from']) {
            $meta[] = '<b>' . trans('search.year_from') . '</b>: ' .
                $args['search_year_from'] . '';
        }
        if ($args['search_year_to']) {
            $meta[] = '<b>' . trans('search.year_to') . '</b>: ' .
                $args['search_year_to'] . '';
        }

        if ($args['only_checked']) {
            $meta[] = trans('corpus.only_checked');
        }

        return sizeof($meta) ? [join(' <span class="warning">' . trans('search.and') . '</span> ', $meta)] : [];
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
    public static function entryNumber($args)
    {
        //dd($args['words']);        
        $texts = Sentence::searchTexts($args);
        //dd($texts);        
        if (!is_array($texts)) {
            return [0, collect([]), false];
        }
        $result = self::searchWordsBySteps($args['words'], $texts, $args['search_lang'], $args['only_checked']);
        $results = $result['results'];
        $is_limited = $result['is_limited'];

        $count = $result['results']->count();
        return [$count, $results, $is_limited];
    }

    /*
 *  Выносим логику дистанции в отдельный метод
0<=d_f<=d_t: from 1 to 3
t2.word_number>t1.word_number 
AND t2.word_number-t1.word_number>=d_f 
AND t2.word_number-t1.word_number<=d_t;

d_f<0<d_t: from -3 to 3
(t1.word_number>t2.word_number 
AND t1.word_number-t2.word_number<=|d_f|
OR t2.word_number>t1.word_number 
AND t2.word_number-t1.word_number<=d_t);

d_t<=d_f<=0: from -3 to -5
t1.word_number>t2.word_number 
AND t1.word_number-t2.word_number>=|d_f|
AND t1.word_number-t2.word_number<=|d_t|;
*/
    protected static function applyDistanceCondition($join, $step, $d_f = 1, $d_t = 1)
    {
        $prev = 't' . ($step - 1) . '.word_number';
        $curr = 't' . $step . '.word_number';

        if ($d_f >= 0 && $d_t >= 0) {
            if ($d_f > $d_t) {
                [$d_f, $d_t] = [$d_t, $d_f];
            }

            $join->on($curr, '>', $prev)
                ->where(DB::raw("$curr - $prev"), '>=', $d_f)
                ->where(DB::raw("$curr - $prev"), '<=', $d_t);

            return;
        }

        if ($d_f < 0 && $d_t > 0) {
            $join->where(function ($q) use ($prev, $curr, $d_f, $d_t) {
                $q->where(function ($q2) use ($prev, $curr, $d_f) {
                    $q2->on($prev, '>', $curr)
                        ->where(DB::raw("$prev - $curr"), '<=', abs($d_f));
                })->orWhere(function ($q2) use ($prev, $curr, $d_t) {
                    $q2->on($curr, '>', $prev)
                        ->where(DB::raw("$curr - $prev"), '<=', $d_t);
                });
            });

            return;
        }

        if ($d_f <= 0 && $d_t <= 0) {
            if ($d_f < $d_t) {
                [$d_f, $d_t] = [$d_t, $d_f];
            }

            $join->on($prev, '>', $curr)
                ->where(DB::raw("$prev - $curr"), '>=', abs($d_f))
                ->where(DB::raw("$prev - $curr"), '<=', abs($d_t));
        }
    }

    /* *  старая функция
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
    public static function searchWordsByNumbers($builder, $words)
    {
        foreach ($words as $i => $word) {
            if ($i == 1) {
                continue;
            }
            $A = $word['d_f'];
            $B = $word['d_t'];

            if ($A >= 0 && $B >= 0) { // from 3 to 1
                if ($A > $B) {
                    list($A, $B) = [$B, $A];
                }
                $builder->whereRaw('t' . $i . '.word_number > t' . ($i - 1) . '.word_number')
                    ->whereRaw('t' . $i . '.word_number-t' . ($i - 1) . '.word_number  >= ' . $A)
                    ->whereRaw('t' . $i . '.word_number-t' . ($i - 1) . '.word_number <= ' . $B);
            } elseif ($A < 0 && $B > 0 || $B < 0 && $A > 0) { // from 3 to -3
                if ($A > $B) {
                    list($A, $B) = [$B, $A];
                }
                $builder->where(function ($q) use ($i, $A, $B) {
                    $q->where('t' . ($i - 1) . '.word_number', '>', 't' . $i . '.word_number')
                        ->where(DB::raw('t' . ($i - 1) . '.word_number-t' . $i . '.word_number'), '<=', abs($A))
                        ->orWhere('t' . $i . '.word_number', '>', 't' . ($i - 1) . '.word_number')
                        ->where(DB::raw('t' . $i . '.word_number-t' . ($i - 1) . '.word_number'), '<=', $B);
                });
            } elseif ($A <= 0 && $B <= 0) { // from -5 to -3
                if ($A < $B) {
                    list($A, $B) = [$B, $A];
                }
                $builder->where('t' . ($i - 1) . '.word_number', '>', 't' . $i . '.word_number')
                    ->where(DB::raw('t' . ($i - 1) . '.word_number-t' . $i . '.word_number'), '>=', abs($A))
                    ->where(DB::raw('t' . ($i - 1) . '.word_number-t' . $i . '.word_number'), '<=', abs($B));
            }
        }
        return $builder;
    }

    public static function searchWordsByWord($builder, $table_name, $word, $lang_ids = [], $only_checked = false)
    {
        //\Log::info("searchWordsByWord start", ['table_name' => $table_name, 'word' => $word]);

        $search_by_pos = !empty($word['p']) && sizeof($word['p']);
        if ($search_by_pos && PartOfSpeech::isExistNonChangableIDs($word['p'])) {
            return self::searchWordsByWordForNotChangablePOS($builder, $table_name, $word, $lang_ids, $only_checked);
        }

        if (!empty($word['w']) && is_string($word['w'])) {
            //\Log::info("Adding where word", ['word' => $word['w']]);
            $builder = $builder->where($table_name . 'word', 'rlike', $word['w']);
        }
        $search_by_lemma = !empty($word['l']) && is_string($word['l']);
        $search_by_grams = !empty($word['g']) && sizeof($word['g']);
        $search_by_gramset = !empty($word['gs']) && is_numeric($word['gs']);

        if (!$search_by_lemma && !$search_by_pos && !$search_by_grams && !$search_by_gramset) {
            return $builder;
        }

        $builder = $builder->whereIn($table_name . 'id', function ($q) use ($word, $search_by_lemma, $search_by_pos, $search_by_grams, $search_by_gramset, $lang_ids, $only_checked) {
            $q->select('word_id')->from('text_wordform')
                ->where('relevance', '>', $only_checked ? 1 : 0);
            if ($search_by_lemma || $search_by_pos) {
                $q->whereIn('wordform_id', function ($query1) use ($word, $search_by_lemma, $search_by_pos, $lang_ids) {
                    $query1->select('wordform_id')
                        ->from('lemma_wordform')
                        ->whereIn('lemma_id', function ($query2) use ($word, $search_by_lemma, $search_by_pos, $lang_ids) {
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
                $q->whereIn('gramset_id', function ($query2) use ($word) {
                    $query2->select('id')->from('gramsets');
                    foreach ($word['g'] as $field => $group) {
                        $query2->whereIn($field, $group);
                    }
                });
            }
        });
        //dd($builder->count());
        //dd(to_sql($builder));

        //\Log::info("searchWordsByWord end");
        return $builder;
    }

    public static function searchWordsByWordForNotChangablePOS($builder, $table_name, $word, $lang_ids = [], $only_checked = false)
    {
        if (isset($word['w']) && $word['w']) {
            $builder = $builder->where($table_name . 'word', 'rlike', $word['w']);
        }
        $search_by_lemma = isset($word['l']) && $word['l'];

        return $builder->whereIn($table_name . 'id', function ($q) use ($word, $search_by_lemma, $lang_ids, $only_checked) {
            $q->select('word_id')->from('meaning_text')
                ->where('relevance', '>', $only_checked ? 1 : 0)
                ->whereIn('meaning_id', function ($query1) use ($word, $search_by_lemma, $lang_ids) {
                    $query1->select('id')->from('meanings')
                        ->whereIn('lemma_id', function ($query2) use ($word, $search_by_lemma, $lang_ids) {
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

    protected static function checkDistanceByWordNumber($prev, $curr, $d_f = 1, $d_t = 1): bool
    {
        if ($d_f >= 0 && $d_t >= 0) {
            if ($d_f > $d_t) {
                [$d_f, $d_t] = [$d_t, $d_f];
            }

            $diff = $curr - $prev;

            return $curr > $prev
                && $diff >= $d_f
                && $diff <= $d_t;
        }

        if ($d_f < 0 && $d_t > 0) {
            return (
                $prev > $curr
                && ($prev - $curr) <= abs($d_f)
            ) || (
                $curr > $prev
                && ($curr - $prev) <= $d_t
            );
        }

        if ($d_f <= 0 && $d_t <= 0) {
            if ($d_f < $d_t) {
                [$d_f, $d_t] = [$d_t, $d_f];
            }

            $diff = $prev - $curr;

            return $prev > $curr
                && $diff >= abs($d_f)
                && $diff <= abs($d_t);
        }

        return false;
    }


    public static function searchWordsBySteps($words, $text_ids = [], $lang_ids = [], $only_checked = false)
    {
        $word_total = count($words);
        $limit = 5000;
        $is_limited = false;

        if ($word_total === 0) {
            return ['results' => collect([]), 'is_limited' => false];
        }

        if ($word_total === 1) {
            $builder = self::buildWordStepQuery(1, $words[1], $text_ids, $lang_ids, $only_checked);
            $results = collect($builder->limit($limit + 1)->get());

            if ($results->count() > $limit) {
                $is_limited = true;
                $results = $results->take($limit);
            }

            return [
                'results' => self::formatStepSearchResults($results, $word_total),
                'is_limited' => $is_limited,
            ];
        }

        $builder1 = self::buildWordStepQuery(1, $words[1], $text_ids, $lang_ids, $only_checked);

        $pairBuilder = $builder1
            ->join('words as t2', function ($join) use ($words) {
                $join->on('t2.sentence_id', '=', 't1.sentence_id');

                $d_f = $words[2]['d_f'] ?? 1;
                $d_t = $words[2]['d_t'] ?? 1;

                self::applyDistanceCondition($join, 2, $d_f, $d_t);
            })
            ->select(
                't1.text_id as text1_id',
                't1.sentence_id as sentence1_id',
                't1.s_id as s1_id',
                't1.w_id as w1_id',
                't1.word_number as word_number1',
                't2.w_id as w2_id',
                't2.word_number as word_number2'
            );

        $pairBuilder = self::searchWordsByWord($pairBuilder, 't2.', $words[2], $lang_ids, $only_checked);
        self::applyWordPunctConditions($pairBuilder, 't2', $words[2]);

        $currentResults = collect($pairBuilder->limit($limit + 1)->get())
            ->filter(function ($row) use ($words) {
                return self::checkBetweenPunctByRow($row, 2, $words[2]);
            })
            ->values();

        if ($currentResults->count() > $limit) {
            $is_limited = true;
            $currentResults = $currentResults->take($limit);
        }

        if ($currentResults->isEmpty()) {
            return ['results' => collect([]), 'is_limited' => $is_limited];
        }

        for ($step = 3; $step <= $word_total; $step++) {
            $sentenceIds = $currentResults->pluck('sentence1_id')->unique()->values();

            if ($sentenceIds->isEmpty()) {
                return ['results' => collect([]), 'is_limited' => $is_limited];
            }

            $builderN = self::buildWordStepQuery($step, $words[$step], [], $lang_ids, $only_checked)
                ->whereIn('t' . $step . '.sentence_id', $sentenceIds);

            $stepResults = collect($builderN->get())->groupBy('sentence1_id');
            $nextResults = collect();

            foreach ($currentResults as $resultRow) {
                $sentenceId = $resultRow->sentence1_id;

                if (!isset($stepResults[$sentenceId])) {
                    continue;
                }

                $prevWordNumberField = 'word_number' . ($step - 1);
                $prevWordNumber = $resultRow->$prevWordNumberField;

                $d_f = $words[$step]['d_f'] ?? 1;
                $d_t = $words[$step]['d_t'] ?? 1;

                foreach ($stepResults[$sentenceId] as $stepRow) {
                    $currWordNumberField = 'word_number' . $step;
                    $currWordNumber = $stepRow->$currWordNumberField;

                    if (!self::checkDistanceByWordNumber($prevWordNumber, $currWordNumber, $d_f, $d_t)) {
                        continue;
                    }

                    $newRow = clone $resultRow;
                    $wIdField = 'w' . $step . '_id';
                    $wordNumberField = 'word_number' . $step;

                    $newRow->$wIdField = $stepRow->$wIdField;
                    $newRow->$wordNumberField = $currWordNumber;

                    if (!self::checkBetweenPunctByRow($newRow, $step, $words[$step])) {
                        continue;
                    }

                    $nextResults->push($newRow);

                    if ($nextResults->count() > $limit) {
                        $is_limited = true;
                        break 2;
                    }
                }
            }

            if ($nextResults->isEmpty()) {
                return ['results' => collect([]), 'is_limited' => $is_limited];
            }

            $currentResults = $nextResults->take($limit);
        }

        return [
            'results' => self::formatStepSearchResults($currentResults, $word_total),
            'is_limited' => $is_limited
        ];
    }

    protected static function formatStepSearchResults($results, int $word_total)
    {
        return $results->map(function ($row) use ($word_total) {
            $item = [
                'text_id' => $row->text1_id,
                'sentence_id' => $row->sentence1_id,
                's_id' => $row->s1_id ?? null,
                'words' => [],
            ];

            for ($step = 1; $step <= $word_total; $step++) {
                $wIdField = 'w' . $step . '_id';
                $wordNumberField = 'word_number' . $step;

                $item['words'][] = [
                    'w_id' => $row->$wIdField ?? null,
                    'word_number' => $row->$wordNumberField ?? null,
                ];
            }

            return $item;
        })->values();
    }

    protected static function buildWordStepQuery($step, $word, $text_ids = [], $lang_ids = [], $only_checked = false)
    {
        $alias = 't' . $step;

        $builder = DB::table('words as ' . $alias)
            ->select(
                $alias . '.text_id as text1_id',
                $alias . '.sentence_id as sentence1_id',
                $alias . '.s_id as s1_id',
                $alias . '.w_id as w' . $step . '_id',
                $alias . '.word_number as word_number' . $step
            );

        if ($step === 1 && $text_ids) {
            $builder->whereIn($alias . '.text_id', $text_ids);
        }

        $builder = self::searchWordsByWord($builder, $alias . '.', $word, $lang_ids, $only_checked);
        self::applyWordPunctConditions($builder, $alias, $word);

        return $builder;
    }

    public static function checkBetweenPunctByRow($row, int $step, array $word): bool
    {
        $mode = $word['bt_mode'] ?? 'ignore';

        if ($mode === 'ignore') {
            return true;
        }

        $prevField = 'word_number' . ($step - 1);
        $currField = 'word_number' . $step;

        if (!isset($row->$prevField) || !isset($row->$currField) || !isset($row->s1_id)) {
            return true;
        }

        $left = min($row->$prevField, $row->$currField);
        $right = max($row->$prevField, $row->$currField);

        if ($left === $right) {
            return $mode !== 'require_any';
        }

        $typeIds = self::putypeIdsBySlugs($word['bt_types'] ?? []);

        $query = DB::table('puncts')
            ->join('words', function ($join) {
                $join->on('words.w_id', '=', 'puncts.left_w_id')
                    ->on('words.text_id', '=', 'puncts.text_id');
            })
            ->where('puncts.s_id', $row->s1_id)
            ->where('words.word_number', '>=', $left)
            ->where('words.word_number', '<', $right);

        if ($typeIds) {
            $query->whereIn('puncts.putype_id', $typeIds);
        }

        $exists = $query->exists();

        if ($mode === 'require_any') {
            return $exists;
        }

        if ($mode === 'forbid_any') {
            return !$exists;
        }

        return true;
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
    public static function searchWords($words, $text_ids = [], $lang_ids = [])
    {
        $table_names = [];
        $word_total = sizeof($words);
        for ($i = 1; $i < $word_total; $i++) {
            $table_names[$i] = 'tmp_words_' . $i;
            Schema::create('tmp_words_' . $i, function (Blueprint $table) {
                $table->integer('sentence_id')->unsigned();
                $table->integer('word_number')->unsigned();
                $table->smallInteger('w_id')->unsigned();
                $table->index(['sentence_id', 'word_number']);
                $table->temporary();
            });

            $query = 'INSERT INTO tmp_words_' . $i . ' SELECT sentence_id, word_number, w_id from words where 1=1 ';
            if (sizeof($text_ids) && $i == 1) {
                $query .= 'and text_id in (' . join(',', $text_ids) . ')';
            }
            $query .= self::searchWordsByWordRaw($words[$i], $lang_ids);
            DB::statement($query);
        }
        $select = 't' . $word_total . '.text_id as text1_id, t' . $word_total . '.sentence_id as sentence1_id';
        $from = [];
        if ($word_total > 1) {
            for ($i = 1; $i < $word_total; $i++) {
                $from[] = $table_names[$i] . ' as t' . $i;
                $select .= ', t' . $i . '.w_id as w' . $i . '_id';
            }
        }
        $from[] = 'words as t' . $word_total;
        $select .= ', t' . $word_total . '.w_id as w' . $word_total . '_id';

        $builder = DB::table(DB::raw(join(', ', $from)))->select(DB::raw($select));

        if ($word_total > 1) {
            // Связываем все таблицы по sentence_id
            for ($i = 2; $i <= $word_total; $i++) {
                $builder = $builder->whereRaw("t{$i}.sentence_id = t1.sentence_id");
            }

            // Применяем дистанцию для каждого слова, начиная со 2-го
            for ($i = 2; $i <= $word_total; $i++) {
                $builder = self::searchWordsByNumbers($builder, [$i => $words[$i]]);
            }
        } elseif ($word_total == 1 && sizeof($text_ids)) {
            $builder = $builder->whereIn('t1.text_id', $text_ids);
        }
        $builder = self::searchWordsByWord($builder, 't' . $word_total . '.', $words[$word_total], $lang_ids);

        return $builder;
    }

    protected static function putypeIdsBySlugs(array $slugs): array
    {
        $slugs = array_filter($slugs, fn($s) => $s !== 'any');
        if (!$slugs) {
            return [];
        }

        return Putype::whereIn('slug', $slugs)->pluck('id')->all();
    }

    protected static function applyWordPunctConditions(
        \Illuminate\Database\Query\Builder $query,
        string $alias,
        array $word
    ): void {
        if (!empty($word['pb'])) {
            self::applyWordBeforePunctCondition($query, $alias, $word['pb']);
        }

        if (!empty($word['pa'])) {
            self::applyWordAfterPunctCondition($query, $alias, $word['pa']);
        }
    }

    protected static function applyWordBeforePunctCondition(
        \Illuminate\Database\Query\Builder $query,
        string $alias,
        array $pb
    ): void {
        $any = in_array('any', $pb, true);
        $typeIds = self::putypeIdsBySlugs($pb);

        $query->whereExists(function ($q) use ($alias, $any, $typeIds) {
            $q->from('puncts')
                ->whereColumn('puncts.s_id', $alias . '.s_id')
                ->whereColumn('puncts.right_w_id', $alias . '.w_id');

            if (!$any && $typeIds) {
                $q->whereIn('puncts.putype_id', $typeIds);
            }
        });
    }

    protected static function applyWordAfterPunctCondition(
        \Illuminate\Database\Query\Builder $query,
        string $alias,
        array $pa
    ): void {
        $any = in_array('any', $pa, true);
        $typeIds = self::putypeIdsBySlugs($pa);

        $query->whereExists(function ($q) use ($alias, $any, $typeIds) {
            $q->from('puncts')
                ->whereColumn('puncts.s_id', $alias . '.s_id')
                ->whereColumn('puncts.left_w_id', $alias . '.w_id');

            if (!$any && $typeIds) {
                $q->whereIn('puncts.putype_id', $typeIds);
            }
        });
    }

    protected static function applyBetweenPunctCondition(
        \Illuminate\Database\Query\Builder $query,
        string $prevAlias,  // t1
        string $currAlias,  // t2
        array $word         // word[i] с bt_mode/bt_types
    ): void {
        $mode = $word['bt_mode'] ?? 'ignore';
        if ($mode === 'ignore') {
            return;
        }

        $typeIds = self::putypeIdsBySlugs($word['bt_types'] ?? []);

        if ($mode === 'require_any') {
            self::applyBetweenRequireAny($query, $prevAlias, $currAlias, $typeIds);
            return;
        }

        if ($mode === 'forbid_any') {
            self::applyBetweenForbidAny($query, $prevAlias, $currAlias, $typeIds);
        }
    }

    protected static function applyBetweenRequireAny(
        \Illuminate\Database\Query\Builder $query,
        string $prevAlias,
        string $currAlias,
        array $typeIds
    ): void {
        $query->whereExists(function ($q) use ($prevAlias, $currAlias, $typeIds) {
            $q->from('puncts')
                ->join('words', 'words.id', '=', 'puncts.left_w_id')
                // тот же s_id
                ->whereColumn('puncts.s_id', $prevAlias . '.s_id')
                ->whereColumn('words.s_id', $prevAlias . '.s_id')
                // left_w_id лежит между word_number предыдущего и текущего слова
                ->whereColumn('words.word_number', '>=', $prevAlias . '.word_number')
                ->whereColumn('words.word_number', '<',  $currAlias . '.word_number');

            if ($typeIds) {
                $q->whereIn('puncts.putype_id', $typeIds);
            }
        });
    }

    protected static function applyBetweenForbidAny(
        \Illuminate\Database\Query\Builder $query,
        string $prevAlias,
        string $currAlias,
        array $typeIds
    ): void {
        $query->whereNotExists(function ($q) use ($prevAlias, $currAlias, $typeIds) {
            $q->from('puncts')
                ->join('words', 'words.id', '=', 'puncts.left_w_id')
                ->whereColumn('puncts.s_id', $prevAlias . '.s_id')
                ->whereColumn('words.s_id', $prevAlias . '.s_id')
                ->whereColumn('words.word_number', '>=', $prevAlias . '.word_number')
                ->whereColumn('words.word_number', '<',  $currAlias . '.word_number');

            if ($typeIds) {
                $q->whereIn('puncts.putype_id', $typeIds);
            }
        });
    }
}
