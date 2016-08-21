<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

//use App\Models\Dict\Meaning;

class Lemma extends Model
{
    
    // Lemma __belongs_to__ Lang
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }    
    
    // Lemma __belongs_to__ PartOfSpeech
    // $pos_name = PartOfSpeech::find(9)->name_ru;
    public function pos()
    {
        return $this->belongsTo(PartOfSpeech::class);
    }
    
    // Lemma __has_many__ Meanings
    public function meanings()
    {
        return $this->hasMany(Meaning::class);
//        return $this->hasMany('App\Models\Dict\Meaning'); // is working too
    }
    
    // Lemma has many MeaningTexts through Meanings
    public function meaning_texts()
    {
        return $this->hasManyThrough('App\Models\Dict\Meaning', 'App\Models\Dict\MeaningText');
    }

    // Lemma __has_many__ Wordforms
    public function wordforms(){
        $builder = $this->belongsToMany('App\Models\Dict\Wordform','lemma_wordform');
//        $builder->getQuery()->getQuery()->distinct = TRUE;
        return $builder;//->get();
    }
    
    // Lemma has any Gramsets
    public function hasGramsets(){
        return $this->belongsToMany(Gramset::class, 'lemma_wordform')
             ->wherePivot('wordform_id')
             ->wherePivot('dialect_id');//->count();
    }
}
