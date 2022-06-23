<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use Storage;

use App\Models\Dict\Lemma;

class Audio extends Model
{
    protected $table = 'audios';
    protected $fillable = ['filename', 'informant_id'];
    const DISK = 'audios';
    const DIR = 'storage/audio/lemmas/';
    
    public $timestamps = false;
    
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
    use \App\Traits\Relations\BelongsTo\Informant;
    
    // Belongs To Many Relations
    public function lemmas(){
        return $this->belongsToMany(Lemma::class);
    }   
    
    public function url() {
        return Storage::url(self::DIR . $this->filename);
    }   
    
    public static function getUrlsByLemmaId($lemma_id) {
        $audios = self::whereIn('id', function ($q) use ($lemma_id) {
            $q->select('audio_id')->from('audio_lemma')
              ->whereLemmaId($lemma_id);
        });
        if (!$audios->count()) {
            return [];
        }
        $urls = [];
        foreach ($audios->get() as $audio) {
            $urls[] = $audio->url();
        }
        return $urls;
    }
    
    public static function addAudioFileToLemmas($filename, $lemma_id) {
        $audio=Audio::firstOrCreate(['filename'=>$filename]);
        $lemma= Lemma::find($lemma_id);
        if (!$lemma) {
            return;
        }
        // выбираем все леммы с таким же написанием в этом языке
        $lemmas = Lemma::whereLangId($lemma->lang_id)
                       ->where('lemma', 'like', $lemma->lemma)
                       ->get();
        foreach ($lemmas as $lemma) {
            if (!$lemma->audios()->count()) {
                $lemma->audios()->attach($audio);
            }
        }        
    }
}
