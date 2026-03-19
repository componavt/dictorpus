<?php

namespace App\Traits\Scopes;

use Illuminate\Database\Eloquent\Builder;

/*
 * for Text
 */

trait LemmasInTexts
{
    public function scopeInTexts(Builder $query): Builder
    {
        return $query->whereIn('id', function ($q) {
            $q->select('lemma_id')
                ->from('meanings')
                ->whereIn('id', function ($q1) {
                    $q1->select('meaning_id')->from('meaning_text');
                });
        });
    }
}
