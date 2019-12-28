<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

use LaravelLocalization;

use App\Models\Dict\Lemma;

class Concept extends Model
{
    public $timestamps = false;
    protected $fillable = ['id', 'concept_category_id', 'pos_id', 'text_en', 'text_ru'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    public function getTextAttribute() : String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "text_" . $locale;
        $text = $this->{$column};
        
        if (!$text && $locale!='ru') {
            $text = $this->text_ru;
        }
        
        return $text;
    }
    
    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\ConceptCategory;
    use \App\Traits\Relations\BelongsTo\POS;

    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Meanings;
    
    public function getSectionAttribute() : String
    {
        return trans("dict.concept_section_".substr($this->id, 0,1));
    }    

    public static function getPOSCodes() {
        return ['NOUN', 'VERB', 'ADJ'];
    }

    public static function getPOSList()
    {     
        $list = [];
        foreach (self::getPOSCodes() as $code) {
            $pos = PartOfSpeech::getByCode($code);
            $list[$pos->id] = $pos->name;
        }
        return $list;         
    }    
    
    /** Gets list dropdown form
     * 
     * @return Array [<key> => <value>,..]
     */
    public static function getList()
    {     
        $objs = self::orderBy('id')->get();
        
        $list = array();
        foreach ($objs as $row) {
            $list[$row->id] = $row->name;
        }
        
        return $list;         
    }
    
    public function countLemmas() {
        $concept_id = $this->id;
        return Lemma::whereIn('id', function($query) use ($concept_id) {
            $query->select('lemma_id')->from('meanings')
                  ->whereIn('id', function ($q) use ($concept_id) {
                      $q->select('meaning_id')->from('concept_meaning')
                        ->where('concept_id', $concept_id);
                  });
        })->count();
    }
}
