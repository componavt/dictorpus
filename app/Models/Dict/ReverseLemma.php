<?php

namespace App\Models\Dict;

//use DB;

use App\Library\Str;

use Illuminate\Database\Eloquent\Model;

class ReverseLemma extends Model
{
    public $timestamps = false;
    protected $fillable = ['reverse_lemma','id','lang_id','affix','stem'];//lemma_
    
    // Belongs To Methods
    use \App\Traits\Methods\search\lemmasByDialects;
    
    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Lang;

    public function lemma()
    {
        return $this->belongsTo(Lemma::class,'id');
    }    
        
    public static function search(Array $url_args) {
        $lemmas = self::orderBy('reverse_lemma');
        if (!$url_args['search_lang']) {
            return NULL;
        }
        $lemmas = self::searchByLang($lemmas, $url_args['search_lang']);
        $lemmas = self::searchByPOS($lemmas, $url_args['search_pos']);
        $lemmas = self::searchByLemma($lemmas, $url_args['search_lemma']); // in trait
        $lemmas = self::searchByDialects($lemmas, $url_args['search_dialects']);

        return $lemmas;
    }
    
    public static function searchByLang($lemmas, $lang) {
        if (!$lang) {
            return $lemmas;
        }
        return $lemmas->where('lang_id',$lang);
    }
    
    public static function searchByPOS($lemmas, $pos) {
        if (!$pos) {
            return $lemmas;
        }
        return $lemmas->whereIn('id',function ($query) use ($pos) {
            $query -> select ('id') -> from('lemmas')
                   -> where ('pos_id', $pos);
        });
    }

    public static function searchByLemma($lemmas, $lemma) {
        if (!$lemma) {
            return $lemmas;
        }
        
        return $lemmas->whereIn('id', function ($query) use ($lemma) {
                    $query->select('id')->from('lemmas');
                    $query=Lemma::searchByLemma($query, $lemma);
                });
    }    
    
    public static function inflexionGroups($lang_id, $pos_id, $dialect_id, $gramsets, $join_harmony) {
        $groups = [];
        if (!$lang_id || !$dialect_id || ($pos_id != PartOfSpeech::getVerbID() && !in_array($pos_id, PartOfSpeech::getNameIDs()))) {
            return $groups;
        }
//        $gramsets = Gramset::dictionaryGramsets($pos_id, NULL, $lang_id);
//        $last = array_pop($gramsets);   // drop initial gramset
//        array_unshift($gramsets,$last); 
        
        $lemmas = Lemma::where('lang_id', $lang_id)->where('pos_id', $pos_id)->orderBy('lemma')->get();
        foreach ($lemmas as $lemma) {
            $affixes = [];
//            list($stem, $lemma_affix) = $lemma->getStemAffix();
            for ($i=0; $i<sizeof($gramsets); $i++) {
                $wordforms = $lemma->wordforms()->wherePivot('gramset_id',$gramsets[$i])->wherePivot('dialect_id', $dialect_id)->get();
//dd($wordforms);                
                if (!$wordforms) {
                    continue;
                }
                $aff = [];
                foreach ($wordforms as $wordform) {
                    $affix = $wordform->pivot->affix;
                    if (!preg_match("/#/", $affix)) {
                        $aff[]=!$join_harmony ? $affix
                                : preg_replace(['/a/','/ä/','/o/','/ö/','/u/','/y/'], ['A','A','O','O','U','U'], $affix);
                    }
                }                
/*                
                $wordform = $lemma->wordform($gramsets[$i], $dialect_id);
                if (!$wordform) {
                    continue;
                }
                $wordforms = preg_split("/,\s*"."/", $wordform);
                $aff = [];
                foreach ($wordforms as $word) {
                    if (preg_match("/^".$stem."(.*)$/u", $word, $regs)) {
                        $aff[] = $regs[1] ?? '';
                    }
                }
*/                
                if (sizeof($aff)) {
                    $affixes[$i] = join(", ", $aff);
                }
            }
//            $affixes[3] = $lemma_affix;
//dd($lemma->id, $affixes);            
            if (sizeof($affixes) == sizeof($gramsets)) {
                $groups[join('_',$affixes)][$lemma->id] = $lemma->lemma;
            }
        }
        ksort($groups);
        return $groups;
    }
    
    public function updateStemAffixFromBase($base0) {
        if (preg_match("/^(.*)\|(.*)$/", $base0, $regs)) {
            $this->stem = $regs[1];
            $this->affix = $regs[2];
        } else {
            $this->stem = $base0;
            $this->affix = NULL;            
        }
        $this->save();
        return $this->stem. $this->affix;
    }
    
    public static function urlArgs($request) {
        $url_args = Str::urlArgs($request) + [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_dialect'  => (int)$request->input('search_dialect'),
                    'search_dialects'  => (array)$request->input('search_dialects'),
                    'search_lang'     => (int)$request->input('search_lang'),
                    'search_lemma'     => $request->input('search_lemma'),
                    'search_pos'      => (int)$request->input('search_pos'),
                    'join_harmony'    => (boolean)$request->input('join_harmony'),
                ];
        
        return $url_args;
    }
}
