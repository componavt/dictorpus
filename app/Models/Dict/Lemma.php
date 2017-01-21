<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;
use URL;

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
        'updated_at' => 'datetime:m/d/Y g:i A'
    );
    protected $revisionFormattedFieldNames = array(
//        'title' => 'Title',
//        'small_name' => 'Nickname',
//        'deleted_at' => 'Deleted At'
    );
    
    protected $fillable = ['lemma','lang_id','pos_id'];
    
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
        $builder = $builder->withPivot('gramset_id','dialect_id');
//        $builder = $builder->join('gramsets', 'gramsets.id', '=', 'lemma_wordform.gramset_id');
        return $builder;//->get();
    }
    
    /**
     * Gets a collection of wordforms with gramsets and sorted by sequence_number of gramsets
     * @return Wordform Object
     */
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
//print "<pre>";        
//dd($wordforms);
//print "</pre>";        
        return $wordforms;
    }
    
    /**
     * Gets a collection of wordforms without gramsets and sorted by id
     * @return Wordform Object
     */
    public function wordformsWithoutGramsets(){
        $wordforms = $this->wordforms()->wherePivot('gramset_id',NULL)->get();
        return $wordforms;
    }
    
    /**
     * Gets a collection of wordforms for ALL gramsets and sorted by sequence_number of gramsets
     * 
     * FOR NULL DIALECT
     * 
     * @return Wordform Object
     */
    public function wordformsWithAllGramsets(){
        $gramsets = Gramset::getList($this->pos_id,$this->lang_id);
        $dialects = ['NULL'=>''] + Dialect::getList($this->lang_id);
        
        $wordforms = NULL;
        
        foreach (array_keys($gramsets) as $gramset_id) {
    //                         ->withPivot('dialect_id',NULL)
            foreach (array_keys($dialects) as $dialect_id) {
                $wordform = $this->wordforms()
                                 ->wherePivot('gramset_id',$gramset_id)
                                 ->wherePivot('dialect_id', $dialect_id)
                                 ->first();
                $wordforms[$gramset_id][$dialect_id] = $wordform;
            }
        }
        
        return $wordforms;
    }
    
    // Lemma has any Gramsets
    public function hasGramsets(){
        return $this->belongsToMany(Gramset::class, 'lemma_wordform')
             ->wherePivot('wordform_id')
             ->wherePivot('dialect_id');//->count();
    }
    
    /**
     * Gets meaning_n for next meaning created
     * 
     * @return int
     */
    public function getNewMeaningN(){
        $builder = DB::table('meanings')->select(DB::raw('max(meaning_n) as max_meaning_n'))->where('lemma_id',$this->id)->first();
        if ($builder) {
            $max_meaning_n = $builder->max_meaning_n;
        } else {
            $max_meaning_n = 0;
        }
        return 1+ $max_meaning_n;
    }
    
    /**
     * Gets count of sentence-examples
     * 
     * @return int
     */
    public function countExamples(){
        $count = 0;
        foreach ($this->meanings as $meaning) {
            if ($meaning->texts()) {
//print "<p>meaning:".$meaning->id.', count: '.$meaning->texts()->count()."</p>";                
                $count = $count + $meaning->texts()->count();
            }
        }
        return $count;
    }
    
    /**
     * Gets Delete link created in a view
     * Generates a CSRF token and put it inside a custom data-delete attribute
     * @param bool $is_button Is this button or link?
     */
/*    public function buttonDelete($is_button=true)
    {
//        $format = '<a href="%s" data-toggle="tooltip" data-delete="%s" title="%s" class="btn btn-default"><i class="fa fa-trash-o"></i></a>';
        $format = '<a href="%s" data-toggle="tooltip" data-delete="%s" title="%s"';
        if ($is_button) {
            $format .= ' class="btn btn-xs btn-danger"';
        }
        $format .= '>%s</a>';
        $link = URL::route('lemma.destroy', ['id' => $this->id]);
        $token = csrf_token();
        $title = \Lang::get('messages.delete');
        return sprintf($format, $link, $token, $title, $title);
    }
 * 
 */
}
