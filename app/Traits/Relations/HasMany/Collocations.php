<?php

/**
 * Сочетаемости, в которых текущее значение является исходным.
 */

namespace App\Traits\Relations\HasMany;

use App\Models\Dict\Collocation;

trait Collocations
{
    public function collocations()
    {
        return $this->hasMany(
            Collocation::class,
            'meaning_id'
        );
    }

    /**
     * Проверяет участие значения в сочетаемостях с любой стороны.
     *
     * @return bool
     */
    public function hasCollocations()
    {
        return Collocation::where('meaning_id', $this->id)
            ->orWhere('collocate_meaning_id', $this->id)
            ->exists();
    }
}
