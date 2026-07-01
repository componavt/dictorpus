<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

class Putype extends Model
{
    public $timestamps = false;
    protected $fillable = ['slug', 'name_en', 'name_ru', 'symbols'];

    // Has Many Relations
    use \App\Traits\Relations\HasMany\Puncts;
}
