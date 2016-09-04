<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }
}
