<?php

namespace App\Traits\Relations\BelongsToMany;

use App\Models\Corpus\Bible;

trait Bibles
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bibles()
    {
        return $this->belongsToMany(Bible::class)
            ->withPivot('chapter')
            ->withPivot('verse_from')
            ->withPivot('verse_to')
            ->orderBy('sequence_number');
    }

    /**
     * Gets IDs of bibles for bible's form field
     *
     * @return Array
     */
    public function bibleValue(): array
    {
        $value = [];
        if ($this->bibles) {
            foreach ($this->bibles as $bible) {
                $value[] = $bible->id;
            }
        }
        return $value;
    }
}
