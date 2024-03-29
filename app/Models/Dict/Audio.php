<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use Storage;
use LaravelLocalization;

use App\Library\Grammatic;
use App\Library\Grammatic\KarGram;

use App\Models\Corpus\Informant;

use App\Models\Dict\Lemma;

class Audio extends Model
{
    protected $table = 'audios';
    protected $fillable = ['filename', 'informant_id'];
    const DISK = 'audios';
    const DIR = 'audio/lemmas/';
    const recordGroups = [
        'multidict-check' => 'ПРОВЕРЕННЫХ ливвиковских слов для Мультимедийного словаря',
        'multidict-phrase'   => 'ливвиковских ФРАЗЕОЛОГИЗМОВ для всех слов в Мультимедийном словаре',
        'multidict-all'   => 'ВСЕХ ливвиковских слов для Мультимедийного словаря',
        'lud-mikh'        => 'людиковских слов Михайловского говора'
    ];
    
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
    
    public static function addAudioFileToLemmas(string $filename, int $lemma_id, $informant_id=NULL) {   
        $audio = Audio::where('filename', 'rlike', '^'.$lemma_id.'\_'.$informant_id.'[_.]')->first();
        if (!$audio) {
            $audio=Audio::firstOrCreate(['filename'=>$filename]);
        }
        $oldname = $audio->filename;
        if ($oldname != $filename) {
            $audio->filename = $filename; 
            Storage::disk('audios')->delete($oldname);
        }
        if ($informant_id) {
            $audio->informant_id = $informant_id;
        }
        $audio->updated_at = date('Y-m-d H:i:s');
        $audio->save();
        
        $lemma= Lemma::find($lemma_id);
        if (!$lemma) {
            return;
        }
        // выбираем все леммы с таким же написанием в этом языке
        $lemmas = Lemma::whereLangId($lemma->lang_id)
                       ->where('lemma', 'like', $lemma->lemma)
                       ->get();
        foreach ($lemmas as $lemma) {
            if (!$lemma->audios()->whereInformantId($informant_id)->count()) {
                $lemma->audios()->attach($audio);
            }
            $lemma->informants()->detach($informant_id);
        }        
    }
    
    public static function getSpeakerList()
    {     
        $locale = LaravelLocalization::getCurrentLocale();
               
        $informants = Informant::whereIn('id', function ($q) {
                            $q->select('informant_id')->from('audios');
                        })->orderBy('name_'.$locale)->get();
        
        $list = array();
        foreach ($informants as $row) {
            $list[$row->id] = $row->informantString('',false);
        }
        
        return $list;         
    }
    
    public static function urlArgs($request) {
        $url_args = url_args($request) + [
                    'search_informant'=> $request->input('search_informant'),
                    'search_lang'     => (array)$request->input('search_lang'),
                    'search_lemma'    => $request->input('search_lemma'),
                    'search_dialect'  => (int)$request->input('search_dialect'),
                ];
        
        return $url_args;
    }
    
    public static function search(Array $url_args) {
        $recs = self::orderBy('updated_at', 'DESC');        
        $recs = self::searchByInformant($recs, $url_args['search_informant']);
        $recs = self::searchByLangOrLemma($recs, $url_args['search_lang'], $url_args['search_lemma']);
//dd($texts->toSql());                                

        return $recs;
    }
    
    public static function searchByInformant($recs, $informant) {
        if (!$informant) {
            return $recs;
        }
        return $recs->where('informant_id',$informant);
    }
    
    public static function searchByLangOrLemma($recs, $langs, $lemma) {
        if (!sizeof($langs) && !$lemma) {
            return $recs;
        }
        $lemma = preg_replace("/\|/", '', $lemma);
        return $recs->whereIn('id', function ($q1) use ($langs, $lemma) {
                    $q1->select('audio_id')->from('audio_lemma')
                            ->whereIn('lemma_id', function ($q) use ($langs, $lemma) {
                            $q->select('id')->from('lemmas');
                            if (sizeof($langs)) {
                                $q->whereIn('lang_id',$langs);
                            }
                            if ($lemma) {
                                $q->where(function ($q2) use ($lemma) {
                                    $q2->where('lemma_for_search', 'like', Grammatic::toSearchForm($lemma))
                                       ->orWhere('lemma_for_search', 'like', KarGram::changeLetters(Grammatic::toSearchForm($lemma)));
                                });
                            }
                        });
                });
    }
}
