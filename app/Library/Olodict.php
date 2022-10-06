<?php

namespace App\Library;

use App\Models\Dict\Concept;
use App\Models\Dict\ConceptCategory;
use App\Models\Dict\Label;
use App\Models\Dict\Lemma;
//use App\Models\Dict\LemmaWordform;
//use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Relation;

//use App\Models\Corpus\Word;

class Olodict
{
    const Dialect = 44;
    
    public static function conceptCategoryList() {
        $objs = ConceptCategory::whereIn('id', function ($q1) {
            $q1->select('concept_category_id')->from('concepts')
               ->whereIn('id', function ($q2) {
                  $q2->select('concept_id')->from('concept_meaning')
                     ->whereIn('meaning_id', function ($q3) {
                         $q3->select('id')->from('meanings')
                            ->whereIn('lemma_id', Label::checkedOloLemmas());
                     });
               });
        })->orderBy('id')->get();
        
        $list = array();
        foreach ($objs as $row) {
            $list[$row->id] = $row->id .'. '. $row->name;
        }
        
        return $list;         
        
    }

    /** Gets list for dropdown field
     * 
     * @return Array [<key> => <value>,..]
     */
    public static function conceptList($category_id=NULL, $pos_id=NULL)
    {     
        $objs = Concept::whereIn('id', function ($q1) {
             $q1->select('concept_id')->from('concept_meaning')
                ->whereIn('meaning_id', function ($q3) {
                    $q3->select('id')->from('meanings')
                       ->whereIn('lemma_id', Label::checkedOloLemmas());
                });
        })->orderBy('id');
        
        if ($category_id) {                 
            $objs = $objs ->where('concept_category_id',$category_id);
        }
        
        if ($pos_id) {                 
            $objs = $objs ->where('pos_id',$pos_id);
        }
        
        $objs = $objs->get();
        $list = array();
        foreach ($objs as $row) {
            $list[$row->id] = $row->text;
        }
        
        return $list;         
    }
    
    
    public static function lemmaList($url_args) {
        $lemmas = Lemma::whereIn('id', Label::checkedOloLemmas());
        
        if ($url_args['search_gram']) {
            $lemmas -> where('lemma_for_search', 'like', $url_args['search_gram'].'%');
            
        } elseif ($url_args['search_letter']) {
            $lemmas -> where('lemma_for_search', 'like', $url_args['search_letter'].'%');
            
        } else {
            if ($url_args['search_pos']) {
                $lemmas -> where('pos_id', $url_args['search_pos']);
            }

            $lemmas = self::searchByWord($lemmas, $url_args['search_word'], $url_args['with_template']);
            $lemmas = self::searchByMeaning($lemmas, $url_args['search_meaning'], $url_args['with_template']);
            $lemmas = self::searchByAudios($lemmas, $url_args['with_audios']);
            $lemmas = self::searchByConcept($lemmas, $url_args['search_concept'], $url_args['search_concept_category']);
        }
//dd(to_sql($lemmas));        
        return $lemmas ->orderBy('lemma_for_search')
                ->groupBy('lemma');
    }
    
    public static function searchByAudios($lemmas, $with_audios) {
        if (!$with_audios) {
            return $lemmas;
        }
        return $lemmas->whereIn('id',function($query){
                        $query->select('lemma_id')
                            ->from('audio_lemma');
                        });
    }
    
    public static function searchByWord($lemmas, $word, $with_template) {
        if (!$word) {
            return $lemmas;
        }
        $word_for_search = Grammatic::changeLetters($word, 5);
        if (!$with_template) {
            $word_for_search = '%'.$word_for_search.'%';
        }

        return $lemmas->where(function ($q) use ($word_for_search) {
                    $q->where('lemma_for_search', 'like', $word_for_search)
                      ->orWhereIn('id',function($q2) use ($word_for_search){
                            $q2->select('lemma_id')->from('lemma_wordform')
                               ->where('wordform_for_search', 'like', $word_for_search);
                            });
                });
    }
    
    public static function searchByMeaning($lemmas, $meaning, $with_template) {
        if (!$meaning) {
            return $lemmas;
        }
        if (!$with_template) {
            $meaning = '%'.$meaning.'%';
        }
        return $lemmas->whereIn('id',function($query) use ($meaning){
                    $query->select('lemma_id')
                        ->from('meanings')
                        ->whereIn('id',function($q) use ($meaning){
                            $q->select('meaning_id')
                            ->from('meaning_texts')
                            ->where('meaning_text', 'like', $meaning);
                        });
                    });
    }
    
    public static function searchByConcept($lemmas, $concept_id, $concept_category_id) {
        if (!$concept_id && !$concept_category_id) {
            return $lemmas;
        }
        return $lemmas->whereIn('id',function($query) use ($concept_id, $concept_category_id){
                    $query->select('lemma_id')->from('meanings')
                          ->whereIn('id',function($q1) use ($concept_id, $concept_category_id){
                              $q1->select('meaning_id')->from('concept_meaning');
                              if ($concept_id) {
                                  $q1->whereConceptId($concept_id);
                              }
                              if ($concept_category_id) {
                                  $q1->whereIn('concept_id', function($q2) use ($concept_category_id) {
                                    $q2->select('id')->from('concepts')
                                       ->where('concept_category_id', $concept_category_id);
                                  });
                              }
                        });
                    });
    }
    public static function gramLinks($first_letter) {
        if (!$first_letter) {
            return collect();
        }
        return Lemma::where('lemma_for_search', 'like', $first_letter.'%')
                         ->whereIn('id', Label::checkedOloLemmas())
                         ->selectRaw('substr(lemma_for_search,1,3) as gram')
                         ->groupBy('gram')
                         ->orderBy('gram')
                         ->get();
    }
    
    public static function search($url_args) {
        if (!$url_args['search_lemma']) {
            return collect();
        }
        $lemmas = Lemma::whereIn('id', Label::checkedOloLemmas())
                      ->where('lemma', 'like', $url_args['search_lemma'])->get();
        return $lemmas;
    }
}
