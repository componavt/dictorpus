<?php

namespace App\Models\Corpus;

use LaravelLocalization;

use Illuminate\Database\Eloquent\Model;

use App\Models\Corpus\Sentence;

use App\Models\Dict\Lang;

class SentenceTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['sentence_id','lang_id','text'];

    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.


    public static function boot()
    {
        parent::boot();
    }
    
    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Lang;
    use \App\Traits\Relations\BelongsTo\Sentence;

    public static function getTextForLocale($sentence_id) {
        $locale = LaravelLocalization::getCurrentLocale();
        
        $translation = self::getByLangCode($sentence_id, $locale);
        if ($translation) {
            return $translation->text;
        }
        
        if (!$locale == 'ru') {
            return;
        }
                
        $translation = self::getByLangCode($sentence_id, 'ru');
        if ($translation) {
            return $translation->text;
        }        
    }
    
    public static function getByLangCode($sentence_id, $lang_code) {
        $lang_id = Lang::getIDByCode($lang_code);
        return self::getByLangId($sentence_id, $lang_id);
    }
    
    public static function getByLangId($sentence_id, $lang_id) {
        return self::whereSentenceId($sentence_id)->whereLangId($lang_id)->first();
    }
}
