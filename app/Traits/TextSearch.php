<?php namespace App\Traits;

use App\Library\Grammatic;
use App\Library\Str;

use App\Models\Corpus\Genre;
use App\Models\Corpus\Sentence;
use App\Models\Corpus\Transtext;

trait TextSearch
{
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'search_author'   => $request->input('search_author'),
                    'search_birth_district'  => (array)$request->input('search_birth_district'),
                    'search_birth_place' => (array)$request->input('search_birth_place'),
                    'search_birth_region' => $request->input('search_birth_region'),
                    'search_collection'   => (int)$request->input('search_collection'),
                    'search_corpus'   => (array)$request->input('search_corpus'),
                    'search_cycle'     => (array)$request->input('search_cycle'),
                    'search_dialect'  => (array)$request->input('search_dialect'),
                    'search_district'  => (array)$request->input('search_district'),
                    'search_genre'    => (array)$request->input('search_genre'),
                    'search_informant'=> $request->input('search_informant'),
                    'search_lang'     => (array)$request->input('search_lang'),
                    'search_motive'     => (array)$request->input('search_motive'),
                    'search_place'    => (array)$request->input('search_place'),
                    'search_plot'    => (array)$request->input('search_plot'),
                    'search_recorder' => $request->input('search_recorder'),
                    'search_region' => $request->input('search_region'),
                    'search_sentence' => (int)$request->input('search_sentence'),
                    'search_source'    => $request->input('search_source'),
                    'search_title'    => $request->input('search_title'),
                    'search_topic'    => (array)$request->input('search_topic'),
                    'search_text'     => $request->input('search_text'),
                    'search_w'     => $request->input('search_w'),
                    'search_wid'     => (array)$request->input('search_wid'),
                    'search_without_genres' => (boolean)$request->input('search_without_genres'),
                    'search_word'     => $request->input('search_word'),
//                    'search_year'     => (int)$request->input('search_year'),
                    'search_year_from'=> (int)$request->input('search_year_from'),
                    'search_year_to'  => (int)$request->input('search_year_to'),
                    'with_audio' => (boolean)$request->input('with_audio'),
                    'with_transtext' => (boolean)$request->input('with_transtext'),
                ];
        
        if ($url_args['search_without_genres']) {
            $url_args['search_genre'] = [];
        }
        return $url_args;
    }
    
    public static function search(Array $url_args) {
        // select * from `texts` where (`transtext_id` in (select `id` from `transtexts` where `title` = '%nitid_') or `title` like '%nitid_') and `lang_id` = '1' order by `title` asc limit 10 offset 0
        // select texts by title from texts and translation texts
//        $texts = self::orderBy('title');        
        $texts = self::orderBy('id', 'DESC');        
        $texts = self::searchByAuthor($texts, $url_args['search_author']);
//        $texts = self::searchByAuthors($texts, $url_args['search_author']);
        $texts = self::searchByBirthPlace($texts, $url_args['search_birth_place'], $url_args['search_birth_district'], $url_args['search_birth_region']);
        $texts = self::searchByCorpuses($texts, $url_args['search_corpus']);
        $texts = self::searchByDialects($texts, $url_args['search_dialect']);
        $texts = self::searchByInformant($texts, $url_args['search_informant']);
        $texts = self::searchByLang($texts, $url_args['search_lang']);
        $texts = self::searchByPlace($texts, $url_args['search_place'], $url_args['search_district'], $url_args['search_region']);
        $texts = self::searchByRecorder($texts, $url_args['search_recorder']);
        $texts = self::searchByTitle($texts, $url_args['search_title']);
        $texts = self::searchByWid($texts, $url_args['search_wid']);
        $texts = self::searchByWord($texts, $url_args['search_word']);
        $texts = self::searchByText($texts, $url_args['search_text']);
        $texts = self::searchByGenres($texts, $url_args['search_genre'], $url_args['search_without_genres']);
        $texts = self::searchByPlots($texts, $url_args['search_plot']);
        $texts = self::searchByTopics($texts, $url_args['search_topic']);
        $texts = self::searchByYear($texts, $url_args['search_year_from'], $url_args['search_year_to']);
        $texts = self::searchBySource($texts, $url_args['search_source']);
        $texts = self::searchWithAudio($texts, $url_args['with_audio']);
        
        $texts = self::searchByPivot($texts, 'text', 'motive', $url_args['search_motive']);
        
/*        if ($url_args['search_corpus']) {
            $texts = $texts->whereIn('corpus_id',$url_args['search_corpus']);
        } */
        if ($url_args['with_transtext']) {
            $texts = $texts->whereNotNull('transtext_id');
        } 
/*
        if ($url_args['search_text']) {
            $texts = $texts->where('text','like','%'.$url_args['search_text'].'%');
        } */
//dd(to_sql($texts));        
//dd($texts->toSql());                                

        return $texts;
    }

    use \App\Traits\Methods\search\byPivot;

    public static function searchWithSentences(Array $url_args) {
        $texts = self::orderBy('title');        
        if ($url_args['search_corpus']) {
            $texts = $texts->whereIn('corpus_id',$url_args['search_corpus']);
        } 
        $texts = self::searchByDialects($texts, $url_args['search_dialect']);
        $texts = self::searchByGenres($texts, $url_args['search_genre']);
        $texts = self::searchByLang($texts, $url_args['search_lang']);
        $texts = self::searchByYear($texts, $url_args['search_year_from'], $url_args['search_year_to']);
        
        $texts = $texts->whereIn('id',array_unique(Sentence::searchWords($url_args['words'])->pluck('t1.text_id')));
//Sentence::searchByWords($texts, 'id', $url_args['words']);
//dd(vsprintf(str_replace(array('?'), array('\'%s\''), $texts->toSql()), $texts->getBindings()));            
        
        return $texts;
    }
        
    public static function searchWithAudio($texts, $with_audio) {
        if (!$with_audio) {
            return $texts;
        }
        return $texts->whereIn('id',function($query){
                    $query->select('text_id')
                    ->from('audiotexts');
                });
    }
        
    public static function searchBySource($texts, $source) {
        if (!$source) {
            return $texts;
        }
        return $texts->whereIn('source_id',function($query) use ($source){
                    $query->select('id')
                    ->from('sources')
                    ->where('title', 'rlike', $source)
                    ->orWhere('author', 'rlike', $source)
                    ->orWhere('comment', 'rlike', $source);
                });
    }
    
    public static function searchByBirthPlace($texts, $place_ids, $district_ids, $region_id) {
        if (!sizeof($place_ids) && !sizeof($district_ids) && !$region_id) {
            return $texts;
        }
        return $texts->whereIn('event_id', function($query) use ($place_ids, $district_ids, $region_id){
                    $query->select('event_id')->from('event_informant')
                    ->whereIn('informant_id', function($q) use ($place_ids, $district_ids, $region_id){
                        $q->select('id')->from('informants');
                        if (sizeof($place_ids)) {
                            $q->whereIn('birth_place_id',$place_ids);
                        }
                        if (sizeof($district_ids) || $region_id) {
                            $q->whereIn('birth_place_id',function($q2) use ($district_ids, $region_id){
                                $q2->select('id')->from('places');
                                if (sizeof($district_ids)) {
                                    $q2->whereIn('district_id',$district_ids);
                                }
                                if ($region_id) {
                                    $q2->whereIn('district_id', function($q3) use ($region_id){
                                        $q3->select('id')->from('districts')
                                           ->whereRegionId($region_id);                                        
                                    });
                                }
                            });                            
                        }
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
    
    public static function searchByCorpuses($texts, $corpuses) {
        if (!sizeof($corpuses)) {
            return $texts;
        }
        return $texts->whereIn('id',function($query) use ($corpuses){
                    $query->select('text_id')
                    ->from("corpus_text")
                    ->whereIn('corpus_id',$corpuses);
                });
    }
    
    public static function searchByGenres($texts, $genres, $without_genres=false) {
        if ($without_genres) {
            return $texts->whereNotIn('id',function($query){
                        $query->select('text_id')
                        ->from("genre_text");
                    });            
        }
        
        if (!sizeof($genres)) {
            return $texts;
        }

        foreach (Genre::whereIn('parent_id', $genres)->get() as $g) {
            $genres[] = $g->id;
        }
        return $texts->whereIn('id',function($query) use ($genres){
                    $query->select('text_id')
                    ->from("genre_text")
                    ->whereIn('genre_id',$genres);
                });
    }
    
   
    public static function searchByPlots($texts, $plots) {
        if (!sizeof($plots)) {
            return $texts;
        }
        return $texts->whereIn('id',function($query) use ($plots){
                    $query->select('text_id')
                    ->from("plot_text")
                    ->whereIn('plot_id',$plots);
                });
    }
    
    public static function searchByTopics($texts, $topics) {
        if (!sizeof($topics)) {
            return $texts;
        }
        return $texts->whereIn('id',function($query) use ($topics){
                    $query->select('text_id')
                    ->from("text_topic")
                    ->whereIn('topic_id',$topics);
                });
    }
    
    public static function searchByAuthor($texts, $author) {
        if (!$author) {
            return $texts;
        }
        return $texts->where(function ($q) use ($author) {
                    $q->whereIn('id',function($query) use ($author){
                            $query->select('text_id')
                            ->from("author_text")
                            ->where('author_id',$author);
                    })->orWhereIn('transtext_id',function($q2) use ($author){
                            $q2->select('transtext_id')
                            ->from("author_transtext")
                            ->where('author_id',$author);
                    });
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
    
    public static function searchByPlace($texts, $places, $districts, $region) {
        if (!sizeof($places) && !sizeof($districts) && !$region) {
            return $texts;
        }
        return $texts->whereIn('event_id',function($query) use ($places, $districts, $region){
                    $query->select('id')->from('events');
                    if (sizeof($places)) {
                        $query->whereIn('place_id',$places);
                    }
                    if (sizeof($districts) || $region) {
                        $query->whereIn('place_id', function ($q2) use ($districts, $region){
                            $q2->select('id')->from('places');
                            if (sizeof($districts)) {
                                $q2->whereIn('district_id',$districts);
                            }
                            if ($region) {
                                $q2->whereIn('district_id', function ($q3) use ($region){
                                    $q3->select('id')->from('districts')
                                       ->whereRegionId($region);                                    
                                });
                            }
                        });
                    }
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

    public static function searchByWid($texts, Array $wids) {
        if (!sizeof($wids)) {
            return $texts;
        }
        return $texts->whereIn('id',function($query) use ($wids){
                                $query->select('text_id')
                                ->from('words')
                                ->whereIn('w_id', $wids);
                            });
    }

    public static function searchByText($texts, $str) {
        if (!$str) {
            return $texts;
        }
        return $texts->/*where(function($q) use ($str){
                        $q->whereIn('transtext_id',function($query) use ($str){
                            $query->select('id')
                            ->from(with(new Transtext)->getTable())
                            ->where('text','like', '%'.$str.'%');
                        })->or*/Where('text','like', '%'.$str.'%')/*;
                })*/;
                       //->whereOr('transtexts.title','like', $text_title);
    }

    public static function searchByYear($texts, $year_from, $year_to) {
        if (!$year_from && !$year_to) {
            return $texts;
        }
        $year_from = $year_from ? $year_from : 1;
        $year_to = $year_to ? $year_to : 3000;

        return $texts->where(function ($query1) use ($year_from, $year_to) {
            $query1->where(function ($q) use ($year_from, $year_to) {
                $q->whereNotNull('event_id')
                  ->whereIn('event_id',function($query) use ($year_from, $year_to){
                    $query->select('id')->from('events')
                    ->where('date', '>=', $year_from)
                    ->where('date', '<=', $year_to);
                   });
                })->orWhere(function ($q) use ($year_from, $year_to) {
                    $q->whereNull('event_id')
                      ->WhereIn('source_id',function($query) use ($year_from, $year_to){
                        $query->select('id')->from('sources')
                        ->where('year', '>=', $year_from)
                        ->where('year', '<=', $year_to);
                        });                                       
                   });                   
        });
    }
    
    public static function simpleSearch (string $word) {
        $word = Grammatic::toSearchForm(preg_replace("/\|/", '', $word));
        return 
        self::where('title', 'rlike', $word)
            ->orWhere('text', 'rlike', $word)
            ->orWhereIn('id', function ($q) use ($word) {
                $q->select('text_id')->from('words')
                  ->where('word', 'rlike', $word);
            })->orWhereIn('transtext_id', function ($q) use ($word) {
                $q->select('id')->from('transtexts')
                  ->where('title', 'rlike', $word)
                  ->orWhere('text', 'rlike', $word);
            });
    }

}