<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

class Punct extends Model
{
    public $timestamps = false;
    protected $fillable = ['text_id', 'sentence_id', 's_id', 'p_id', 'p_number', 'punct', 'putype_id', 'left_w_id', 'right_w_id'];

    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Sentence;
    use \App\Traits\Relations\BelongsTo\Text;
    use \App\Traits\Relations\BelongsTo\Putype;
}
