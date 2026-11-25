<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

class CollocationExample extends Model
{
    protected $table = 'collocation_examples';
    const STATUS_ATTACHED = 'attached';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'collocation_id',
        'text_id',
        'source_w_id',
        'partner_w_id',
        'sentence_xml',
        'status',
    ];

    protected $casts = [
        'collocation_id' => 'integer',
        'text_id' => 'integer',
        'source_w_id' => 'integer',
        'partner_w_id' => 'integer',
    ];

    public function collocation()
    {
        return $this->belongsTo(Collocation::class);
    }
}
