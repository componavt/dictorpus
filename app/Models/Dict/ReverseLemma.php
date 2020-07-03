<?php

namespace App\Models\Dict;

use DB;

use Illuminate\Database\Eloquent\Model;

class ReverseLemma extends Model
{
    public $timestamps = false;
    protected $fillable = ['reverse_lemma','id','lang_id','affix','stem'];//lemma_
    
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
    
    public static function inflexionGroups($lang_id, $pos_id, $dialect_id) {
        $groups = [];
        if (!$lang_id || !$dialect_id || ($pos_id != PartOfSpeech::getVerbID() && !in_array($pos_id, PartOfSpeech::getNameIDs()))) {
            return $groups;
        }
        $gramsets = Gramset::dictionaryGramsets($pos_id, NULL, $lang_id);
        $last = array_pop($gramsets);   
        array_unshift($gramsets,$last); 
        
        $lemmas = Lemma::where('lang_id', $lang_id)->where('pos_id', $pos_id)->orderBy('lemma')->get();
        foreach ($lemmas as $lemma) {
            $affixes = [];
            list($stem, $lemma_affix) = $lemma->getStemAffix();
            for ($i=0; $i<sizeof($gramsets)-1; $i++) {
                $wordform = $lemma->wordform($gramsets[$i], $dialect_id);
                if (!$wordform) {
                    continue;
                }
//if (preg_match("/,/",$wordform)) {dd($wordform); }               
                $wordforms = preg_split("/,\s*/", $wordform);
//if (preg_match("/,/",$wordform)) {dd($wordforms); }               
                $aff = [];
                foreach ($wordforms as $word) {
                    if (preg_match("/^".$stem."(.*)$/u", $word, $regs)) {
                        $aff[] = $regs[1] ?? '';
                    }
                }
//if (preg_match("/,/",$wordform)) {dd($aff); }                               
                $affixes[$i] = join(", ", $aff);
            }
            $affixes[3] = $lemma_affix;
//dd($affixes);            
            if (sizeof($affixes) == 4) {
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
}
