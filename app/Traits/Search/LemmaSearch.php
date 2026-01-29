<?php namespace App\Traits\Search;

use App\Library\Grammatic;
use App\Library\Grammatic\KarGram;
use App\Library\Str;

trait LemmaSearch
{
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'search_affix'    => $request->input('search_affix'),
                    'search_concept_category'  => $request->input('search_concept_category'),
                    'search_concept'  => (int)$request->input('search_concept'),
//                    'search_dialect'  => (array)$request->input('search_dialect'),
                    'search_dialect'  => (int)$request->input('search_dialect'),
                    'search_dialects' => (array)$request->input('search_dialects'),
                    'search_gramset'  => (int)$request->input('search_gramset'),
                    'search_gramsets' => (array)$request->input('search_gramsets'),
                    'search_id'       => (int)$request->input('search_id'),
                    'search_label'    => (int)$request->input('search_label'),
                    'search_lang'     => (int)$request->input('search_lang'),
                    'search_lemma'    => $request->input('search_lemma'),
                    'search_meaning'  => $request->input('search_meaning'),
                    'search_pos'      => (int)$request->input('search_pos'),
                    'search_relation' => (int)$request->input('search_relation'),
                    'search_w' => (string)$request->input('search_w'),
                    'search_wordform' => $request->input('search_wordform'),
                    'search_wordforms'=> (array)$request->input('search_wordforms'),
                    'show_dialectal'      => (int)$request->input('show_dialectal'),
                    'with_audios'     => (int)$request->input('with_audios'),
                    'with_examples'   => (int)$request->input('with_examples')
                ];
        
        if (!$url_args['search_id']) {
            $url_args['search_id'] = NULL;
        }
        
        foreach ($url_args['search_wordforms'] as $i => $wordform) {
            if (!$wordform && !$url_args['search_gramsets'][$i]) {
                unset($url_args['search_wordforms'][$i]);
                unset($url_args['search_gramsets'][$i]);
            }
        }
        if (!isset($url_args['search_wordforms'][1])) {
            $url_args['search_wordforms'][1] = null;
        }
        
        if (!isset($url_args['search_gramsets'][1])) {
            $url_args['search_gramsets'][1] = null;
        }
        
        ksort($url_args['search_wordforms']);

        return $url_args;
    }
    
    public static function search(Array $url_args) {
        $lemmas = self::orderBy('lemma_for_search');//orderByRaw('lower(lemma)');//orderBy('lemma');
//        if ($url_args['search_wordform'] || $url_args['search_gramset']) {
  //          $lemmas = $lemmas->join('lemma_wordform', 'lemmas.id', '=', 'lemma_wordform.lemma_id');
//            $lemmas = self::searchByWordform($lemmas, $url_args['search_wordform'], $url_args['search_lang']);
            $lemmas = self::searchByGramset($lemmas, $url_args['search_gramset']);
    //    }    
        $lemmas = self::searchByLemma($lemmas, $url_args['search_lemma']); // in trait
        $lemmas = self::searchByLang($lemmas, $url_args['search_lang']);
        $lemmas = self::searchByPOS($lemmas, $url_args['search_pos']);
        $lemmas = self::searchByID($lemmas, $url_args['search_id']);
        $lemmas = self::searchByMeaning($lemmas, $url_args['search_meaning']);
        $lemmas = self::searchByLabel($lemmas, $url_args['search_label']);
        $lemmas = self::searchByConcept($lemmas, $url_args['search_concept']);
        $lemmas = self::searchByConceptCategory($lemmas, $url_args['search_concept_category']);
        $lemmas = self::searchByDialects($lemmas, $url_args['search_dialects']);
        $lemmas = self::searchWithAudios($lemmas, $url_args['with_audios']);
        $lemmas = self::searchWithExamples($lemmas, $url_args['with_examples']);
        
        if (empty($url_args['show_dialectal'])) {
            $lemmas->whereIsNorm(1);
        }

        $lemmas = $lemmas
                //->groupBy('lemmas.id') // отключено, неправильно показывает общее число записей
                         ->with(['meanings'=> function ($query) {
                                    $query->orderBy('meaning_n');
                                }]);
//dd($lemmas->toSql());                                
        return $lemmas;
    }
    
    public static function searchByLemma($lemmas, $lemma) {
        if (!$lemma) {
            return $lemmas;
        }
        
        return $lemmas->where(function ($query) use ($lemma) {
                        self::searchLemmas($query, $lemma);
                       });
    }    

    public static function searchLemmas($query, $lemma) {
        $lemma = preg_replace("/\|/", '', $lemma);
        return $query -> where('lemma_for_search', 'like', Grammatic::toSearchForm($lemma))
                       -> orWhere('lemma_for_search', 'like', KarGram::changeLetters(Grammatic::toSearchForm($lemma)))
                       -> orWhere('lemma_for_search', 'like', $lemma)
                       -> orWhereIn('id', function ($q) use ($lemma) {
                            $q->select('lemma_id')->from('phonetics')
                              ->where('phonetic', 'like', $lemma);
                        });
    }        
    
    /**
     * 
     * @param array $url_args
     * @return type
     */
    public static function searchByWordformGrams(Array $url_args) {
        $lemmas = self::orderBy('lemma');
        $lemmas = self::searchByLang($lemmas, $url_args['search_lang']);
        $lemmas = self::searchByPOS($lemmas, $url_args['search_pos']);

        $lemmas = self::searchByWordforms($lemmas, $url_args['search_wordforms'], 
                $url_args['search_gramsets'], 
                $url_args['search_lang'], 
                $url_args['search_dialects']);

        $lemmas = $lemmas->with(['meanings'=> function ($query) {
                                    $query->orderBy('meaning_n');
                                }]);
//        $query = str_replace(array('?'), array('\'%s\''), $lemmas->toSql());
//        $query = vsprintf($query, $lemmas->getBindings());     
//dd($query);                                
        return $lemmas;
    }
    
    public static function searchByWordforms($lemmas, $wordforms, $gramsets, $lang_id, $dialects) {
        if (!sizeof($wordforms)) {
            return $lemmas;
        }

        foreach ($wordforms as $i => $wordform) {
            $wordform_for_search = Grammatic::toSearchByPattern($wordform, $lang_id);
            $gramset_id = $gramsets[$i] ?? null;
            if ($wordform_for_search || $gramset_id) {
            $lemmas = $lemmas->whereIn('id',function($q) use ($wordform_for_search, $gramset_id, $dialects){
                            $q->select('lemma_id')->from('lemma_wordform');
                            if ($wordform_for_search) {
                                $q->where('wordform_for_search','rlike', $wordform_for_search);
                            }
                            if ($gramset_id) {
                                $q->where('gramset_id', $gramset_id);
                            }
                            if (isset($dialects[0]) && $dialects[0]) {
                                $q->whereIn('dialect_id', $dialects);
                            }
                        });
            }
        }
        return $lemmas;                            
    }
    
    public static function searchByWordform($lemmas, $wordform, $lang_id) {
        if (!$wordform) {
            return $lemmas;
        }
/*        return $lemmas->whereIn('wordform_id',function($query) use ($wordform){
                            $query->select('id')
                            ->from('wordforms')
                            ->where('wordform_for_search','like', Grammatic::toSearchForm($wordform));
                        });*/
        $wordform_for_search = Grammatic::changeLetters($wordform, $lang_id);
        return $lemmas->whereIn('id',function($q) use ($wordform_for_search){
                            $q->select('lemma_id')->from('lemma_wordform')
//                              ->whereIn('wordform_id',function($query) use ($wordform_for_search){
  //                                  $query->select('id')
    //                                ->from('wordforms')
                                    ->where('wordform_for_search','like', $wordform_for_search);
//                                });
                            });
    }
    
    public static function searchByGramset($lemmas, $gramset) {
        if (!$gramset) {
            return $lemmas;
        }
//        return $lemmas->where('gramset_id',$gramset);
        return $lemmas->whereIn('id',function($q) use ($gramset){
                            $q->select('lemma_id')->from('lemma_wordform')
                              ->where('gramset_id',$gramset);
                            });
    }
    
    public static function searchByLang($builder, $lang) {
        if (!$lang) {
            return $builder;
        }
        return $builder->where('lang_id',$lang);
    }
    
    public static function searchByPOS($lemmas, $pos) {
        if (!$pos) {
            return $lemmas;
        }
        return $lemmas->where('pos_id',$pos);
    }
    
    public static function searchByID($lemmas, $id) {
        if (!$id) {
            return $lemmas;
        }
        return $lemmas->where('id',$id);
    }
    
    public static function searchByMeaning($lemmas, $meaning) {
        if (!$meaning) {
            return $lemmas;
        }
        return $lemmas->whereIn('id',function($query) use ($meaning){
                    $query->select('lemma_id')
                        ->from('meanings')
                        ->whereIn('id',function($q) use ($meaning){
                            $q->select('meaning_id')
                            ->from('meaning_texts')
                            ->where('meaning_text','like', $meaning);
                        });
                    });
    }
    
    public static function searchWithAudios($lemmas, $with_audios) {
        if (!$with_audios) {
            return $lemmas;
        }
        return $lemmas->whereIn('id',function($query){
                    $query->select('lemma_id')
                        ->from('audio_lemma');
                    });
    }
    
    public static function searchWithExamples($lemmas, $with_meanings) {
        if (!$with_meanings) {
            return $lemmas;
        }
        return $lemmas->whereIn('id',function($query){
                    $query->select('lemma_id')
                        ->from('meanings')
                        ->whereIn('id',function($q){
                            $q->select('meaning_id')->from('meaning_text')
                              ->where('relevance', '>', 0);
                        });
                    });
    }
    
    public static function searchByConcept($lemmas, $concept_id) {
        if (!$concept_id) {
            return $lemmas;
        }
        return $lemmas->whereIn('id',function($query) use ($concept_id){
                    $query->select('lemma_id')
                        ->from('meanings')
                        ->whereIn('id',function($query) use ($concept_id){
                            $query->select('meaning_id')
                            ->from('concept_meaning')
                            ->where('concept_id', $concept_id);
                        });
                    });
    }
    
    public static function searchByConceptCategory($lemmas, $concept_category_id) {
        if (!$concept_category_id) {
            return $lemmas;
        }
        return $lemmas->whereIn('id',function($query) use ($concept_category_id){
                    $query->select('lemma_id')
                        ->from('meanings')
                        ->whereIn('id',function($q1) use ($concept_category_id){
                            $q1->select('meaning_id')
                            ->from('concept_meaning')
                            ->whereIn('concept_id', function($q2) use ($concept_category_id) {
                                $q2->select('id')
                                ->from('concepts')
                                ->where('concept_category_id', $concept_category_id);
                            });
                        });
                    });
    }
    
    public static function searchByLabel($lemmas, $label_id) {
        if (!$label_id) {
            return $lemmas;
        }
        return $lemmas->whereIn('id', function ($query) use ($label_id){
                            $query->select('lemma_id')->from('label_lemma')
                                  ->where('label_id', $label_id);
        });
    }
       
}