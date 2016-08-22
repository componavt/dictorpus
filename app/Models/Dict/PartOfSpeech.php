<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

class PartOfSpeech extends Model
{
    protected $table = 'parts_of_speech';
    
    public $timestamps = false;
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    public static function boot()
    {
        parent::boot();
    }
    
    /** Gets localised name of this part of speech (current $locale used).
     * 
     * @return String
     */
    public function getNameAttribute()
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "name_" . $locale;
        return $this->{$column};
    }
        
    // PartOfSpeech __has_many__ Lemma
    public function lemmas()
    {
        return $this->hasMany(Lemma::class);
    }
    
}

// 
// + sequence_number TINYINT, see http://universaldependencies.org/u/pos/index.html
// (1) Open class words
// (2) Closed class words
// (3) Other
// 
//      + Auxiliary verb, вспомогательный глагол, AUX 
//      + Determiner, детерминатив, DET 
// rename: INTER -> INTJ (interjection, междометие)
// rename: N     -> NOUN (noun, существительное)
// 
//      + Proper noun, имя собственное, PROPN
//      + Subordinating conjunction, подчинительный союз, SCONJ
//      
// (3) Other
//      + Punctuation, пунктуация, PUNCT
//      + Symbol, символ, SYM
//      + Other, другое, X