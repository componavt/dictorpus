<?php

namespace App\Traits\Relations\BelongsToMany;

use App\Models\Corpus\Publication;

trait Publications
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function publications()
    {
        return $this->belongsToMany(Publication::Class);
    }

    /**
     * Gets IDs of authors for author's form field
     *
     * @return array
     */
    public function publicationValue(): array
    {
        $author_value = [];
        foreach ($this->publications as $publication) {
            $publication_value[] = $publication->id;
        }
        return $publication_value;
    }
}
