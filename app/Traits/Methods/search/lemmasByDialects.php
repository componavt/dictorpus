<?php namespace App\Traits\Methods\search;

trait lemmasByDialects
{
    public static function searchByDialects($lemmas, $dialects) {
        if (!$dialects || !sizeof($dialects)) {
            return $lemmas;
        }
/*        return $lemmas->whereIn('id', function ($query) use ($dialects){
                            $query->select('lemma_id')->from('dialect_lemma')
                                  ->whereIn('dialect_id', $dialects);
        });*/
        return $lemmas -> whereIn('id', function ($q) use ($dialects) {
                $q->select('lemma_id')->from('meanings')
                  ->whereIn('id', function ($q2) use ($dialects) {
                      $q2->select('meaning_id')->from('dialect_meaning')
                        ->whereIn('dialect_id', $dialects);
                  });
            });
    }
}