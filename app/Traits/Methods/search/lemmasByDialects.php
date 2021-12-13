<?php namespace App\Traits\Methods\search;

trait lemmasByDialects
{
    public static function searchByDialects($lemmas, $dialects) {
        if (!$dialects || !sizeof($dialects)) {
            return $lemmas;
        }
        return $lemmas->whereIn('id', function ($query) use ($dialects){
                            $query->select('lemma_id')->from('dialect_lemma')
                                  ->whereIn('dialect_id', $dialects);
        });
    }
}