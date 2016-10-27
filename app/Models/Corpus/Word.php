<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['text_id', 'w_id', 'word'];
    
    /** Word belongs_to Text
     * 
     * @return Relationship, Query Builder
     */
    public function text()
    {
        return $this->belongsTo(Text::class);
    } 
}
