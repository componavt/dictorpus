<?php

namespace App\Models\Dict;

use Illuminate\Database\Eloquent\Model;
use DB;

use App\Models\Dict\Meaning;

class Synset extends Model
{
    use \App\Traits\Select\SynsetSelect;
    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }

    protected $guarded = ['id', 'created_at', 'updated_at'];   

    const RELATION_FULL = 7;
    const RELATION_NEAR = 11;
    const StopWords =
            ['более','довольно','как','недостаточно','очень','соответствующий','также'];

    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Lang;
    use \App\Traits\Relations\BelongsTo\POS;

    public function dominant()
    {
        return $this->belongsTo(Meaning::class);
    }   
    
    public function meanings(){
        return $this->belongsToMany(Meaning::class)
                ->withPivot('syntype_id');
    }

    public function core(){
        return $this->belongsToMany(Meaning::class, 'meaning_synset', 'synset_id', 'meaning_id')
                    ->wherePivot('syntype_id', Syntype::TYPE_FULL)
                    ->withPivot('syntype_id')
                    ->join('lemmas', 'meanings.lemma_id', '=', 'lemmas.id')
                    ->orderBy('lemmas.lemma')
                    ->select('meanings.*', 'meaning_synset.syntype_id'); 
    }
    
    public function meaningsWithFrequencies($meanings){
        // разом получаем частоты для всех meaning_id
        $freqs = DB::table('meaning_text')
            ->select('meaning_id', DB::raw('count(*) as freq'))
            ->whereIn('meaning_id', $meanings->pluck('id'))
            ->where('relevance', '>', 0)
            ->groupBy('meaning_id')
            ->pluck('freq', 'meaning_id'); // получаем [meaning_id => freq]

        // навешиваем частоты на модели
        $meanings->each(function($meaning) use ($freqs) {
            $meaning->freq = $freqs[$meaning->id] ?? 0;
        });
        return $meanings->sortByDesc('freq');
    }
    
    public function coreWithFrequencies(){
        return $this->meaningsWithFrequencies($this->core);
    }
    
    public function coreWithoutDominant(){
        $builder = $this->belongsToMany(Meaning::class, 'meaning_synset', 'synset_id', 'meaning_id')
                    ->wherePivot('syntype_id', Syntype::TYPE_FULL)
                    ->withPivot('syntype_id')
                    ->join('lemmas', 'meanings.lemma_id', '=', 'lemmas.id')
                    ->orderBy('lemmas.lemma')
                    ->select('meanings.*', 'meaning_synset.syntype_id'); 
        if ($this->dominant) {
            $builder -> where('meanings.id', '<>', $this->dominant_id);
        }
        return $builder;
    }
    
    public function periphery(){
/*        return $this->belongsToMany(Meaning::class)
                ->where('syntype_id', '<>', Syntype::TYPE_FULL)
                ->withPivot('syntype_id');*/
        return $this->belongsToMany(Meaning::class, 'meaning_synset', 'synset_id', 'meaning_id')
                    ->wherePivot('syntype_id', '<>', Syntype::TYPE_FULL)
                    ->withPivot('syntype_id')
                    ->join('lemmas', 'meanings.lemma_id', '=', 'lemmas.id')
                    ->orderBy('lemmas.lemma')
                    ->select('meanings.*', 'meaning_synset.syntype_id'); 
    }

    public function getNameAttribute(){
        return '№'.$this->id;
    }
    
    public static function removeStopWords($terms) {
        $termsArray = preg_split('/\s+/u', $terms);
        return implode(' ', array_diff($termsArray, self::StopWords));
    }
    
    public static function urlArgs($request) {
        $url_args = url_args($request) + [
                    'search_lang'     => (int)$request->input('search_lang'),
                    'search_pos'    => (int)$request->input('search_pos'),
                ];
        
        return $url_args;
    }
    
}
