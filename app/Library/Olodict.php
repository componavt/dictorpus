<?php

namespace App\Library;

use App\Models\Dict\Label;
use App\Models\Dict\Lemma;
//use App\Models\Dict\LemmaWordform;
//use App\Models\Dict\PartOfSpeech;

//use App\Models\Corpus\Word;

class Olodict
{
    const Dialect = 44;
    
    public static function lemmaList($url_args) {
        $lemmas = Lemma::whereIn('id', Label::checkedOloLemmas());
        
        if ($url_args['search_gram']) {
            $lemmas -> where('lemma_for_search', 'like', $url_args['search_gram'].'%');
            
        } elseif ($url_args['search_letter']) {
            $lemmas -> where('lemma_for_search', 'like', $url_args['search_letter'].'%');
        }
        
        return $lemmas ->orderBy('lemma_for_search')
                ->groupBy('lemma')
                ->paginate($url_args['limit_num']);
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
