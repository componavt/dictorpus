<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;

//use App\Models\Dict\Meaning;

class Lemma extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.
    protected $revisionFormattedFields = array(
//        'title'  => 'string:<strong>%s</strong>',
//        'public' => 'boolean:No|Yes',
//        'modified_at' => 'datetime:d/m/Y g:i A',
//        'deleted_at' => 'isEmpty:Active|Deleted'
    );
    protected $revisionFormattedFieldNames = array(
//        'title' => 'Title',
//        'small_name' => 'Nickname',
//        'deleted_at' => 'Deleted At'
    );
    
    public static function boot()
    {
        parent::boot();
    }
    
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
//        $builder = $builder->withPivot('gramset_id','dialect_id');
//        $builder = $builder->join('gramsets', 'gramsets.id', '=', 'lemma_wordform.gramset_id');
        return $builder;//->get();
    }
    
    public function wordformsWithGramsets(){
        $wordforms = $this->wordforms()->get();
        foreach ($wordforms as $wordform) {
            $gramset = $wordform->lemmaDialectGramset($this->id);
            if ($gramset) {
                $wordform->gramset_id = $gramset->id;
                $wordform->gramsetString = $gramset->gramsetString();
                $wordform->sequence_number = $gramset->sequence_number;
            }
        }      
        $wordforms=$wordforms->sortBy('sequence_number');
        return $wordforms;
    }
    
    // Lemma has any Gramsets
    public function hasGramsets(){
        return $this->belongsToMany(Gramset::class, 'lemma_wordform')
             ->wherePivot('wordform_id')
             ->wherePivot('dialect_id');//->count();
    }
}
