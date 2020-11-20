<?php
namespace App\Library;

use App\Library\Grammatic;

use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

class Predictor
{
    /**
     * Prediction a lemma and a gramset for a word by analogy
     * 
     * 1) Search unchangable lemmas in other languages
     * 2) Search wordforms in other languages
     * 3) Search unchangable lemma in the word language by 
     * 
     * @param string $uword - unknown word form for prediction
     * @param int $lang_id - language ID of given word form
     * @return array
     * 
     */
    public static function lemmaGramsetByAnalog(string $uword, int $lang_id): array {
        $rang_enough = 0.01;
        $first_letter = mb_substr($uword, 0, 1);
        $maybe_proper_noun = $first_letter == mb_strtoupper($first_letter);
        $uword_for_search = Grammatic::toSearchForm($uword);
        list ($total_founded, $out1) = self::lemmasFromOtherLangsByAnalog($uword_for_search, $lang_id);
//print "<pre>";        
    //var_dump($out1);                    
        list ($total_founded1, $out1) = self::wordformsFromOtherLangsByAnalog($uword_for_search, $lang_id, $out1, $maybe_proper_noun);
    //var_dump($out1);                    
        list ($total_founded2, $out) = self::lemmasWordformsByAnalog($uword_for_search, $lang_id, $maybe_proper_noun);
        
        foreach ($out1 as $id=>$count) {
            $out[$id] = $count + ($out[$id] ?? 0);
        }
        arsort($out);
        $total = $total_founded+$total_founded1+$total_founded2;
//        return [$total, $out];
        
        $result=[];
        foreach ($out as $id=>$count) {
            $rang = $count / $total;
            if ($rang >  $rang_enough) {
                $result[$id] = $rang;
            }
        }
        return $result;
    }
    
    /**
     * Search unchangable lemmas in other languages by analogy
     * 
     * @param string $uword
     * @param int $lang_id
     * @return array
     */
    public static function lemmasFromOtherLangsByAnalog(string $uword, int $lang_id): array {
        $total_founded=0;
        $out = [];
        $lemmas = Lemma::where('lemma_for_search', 'like', $uword)
                       ->whereIn('pos_id', PartOfSpeech::notChangeablePOSIdList())
                       ->where('lang_id', '<>', $lang_id)->get();
        foreach ($lemmas as $lemma) {
            $i=$lemma->lemma. '_'. $lemma->pos_id. '_';
            $out[$i] = 1+ ($out[$i] ?? 0);
        }
        return [$total_founded, $out];
    }
    
    /**
     * Search wordforms in other languages by analogy
     * 
     * @param string $uword
     * @param int $lang_id
     * @return array
     */
    public static function wordformsFromOtherLangsByAnalog(string $uword, int $lang_id, array $out, bool $maybe_proper_noun): array {
        $total_founded=0;
        $lemmas = Lemma::where('lang_id', '<>', $lang_id)
                       ->whereIn('pos_id', PartOfSpeech::changeablePOSIdList())
                       ->join('lemma_wordform', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                       ->whereNotNull('gramset_id')
                       ->where('gramset_id', '<>', 0)
                       ->whereIn('wordform_id', function ($query) use ($uword) {
                           $query->select('id')->from('wordforms')
                                 ->where('wordform_for_search', 'like', $uword);
                       })
//                       ->selectRaw('pos_id, gramset_id, count(*) as count')
                       ->groupBy('pos_id', 'gramset_id')->get();
        foreach ($lemmas as $lemma) {
            list ($total, $out) = self::fillByPosGramset($uword, $lemma->pos_id, $lemma->gramset_id, 1, $out, $maybe_proper_noun);
            $total_founded += $total;
        }
        return [$total_founded, $out];
    }
    
    /**
     * Search wordforms in the same language by analogy
     * a) among unchanheable lemmas
     * b) among wordforms
     * 
     * @param string $uword
     * @param int $lang_id
     * @return array
     */
    public static function lemmasWordformsByAnalog(string $uword, int $lang_id, bool $maybe_proper_noun): array {
        $found_enough = 10; // порог общего количество найденных вариантов
        $rang_enough=0.5; // порог доли первой пары pos_id-gramset_id среди всех найденных
        $total_founded=0;
        $rang1=0;
        $uleft='';
        $uright=$uword;
        $len_right = mb_strlen($uright);
        
        while ($len_right > 1 && ($total_founded < $found_enough || $total_founded >= $found_enough && $rang1 <= $rang_enough)) {
            $out = [];
            $total_founded=0;
            
            $uleft .= mb_substr($uright,0,1);
            $uright = mb_substr($uright,1);
            $len_right = mb_strlen($uright);
//print "uright: $uright\n";            
            list ($total_founded, $out) = self::lemmasByAnalog($uword, $uright, $lang_id, $total_founded, $out);
//var_dump($out);            
            list ($total_founded, $out) = self::wordformsByAnalog($uword, $uright, $lang_id, $total_founded, $out, $maybe_proper_noun);
            arsort($out);
//var_dump($out);            
            $rang1 = $total_founded ? ($out[array_key_first($out)] ?? 0) / $total_founded : 0;
//print "total_founded: $total_founded, rang1: $rang1, len_right: $len_right\n\n\n";   
        }
        return [$total_founded, $out];
    }
    
    /**
     * 
     * SELECT pos_id, count(*) FROM lemmas WHERE lang_id=5 AND pos_id in (select id from parts_of_speech where id not in (select pos_id from gramset_pos)) and lemma like '%iloi' group by pos_id;
     *
     * @param string $uword
     * @param string $uright
     * @param int $lang_id
     * @param int $total_founded
     * @param array $out
     * @return array
     */
    public static function lemmasByAnalog(string $uword, string $uright, int $lang_id, int $total_founded, array $out): array {
        $pos_list = PartOfSpeech::notChangeablePOSIdList();
        $lemmas = Lemma::whereLangId($lang_id)
                       ->whereIn('pos_id', $pos_list)
                       ->where('lemma_for_search', 'like', '%'.$uright)
                       ->selectRaw('pos_id, count(*) as count')
                       ->groupBy('pos_id')->get();
        foreach ($lemmas as $lemma) {
            $i=$uword. '_'. $lemma->pos_id. '_';
            $out[$i] = $lemma->count + ($out[$i] ?? 0);
            $total_founded += $lemma->count;
        }
        return [$total_founded, $out];        
    }
    
    /**
     * 
     * SELECT pos_id, gramset_id, reverse_lemmas.affix, lemma_wordform.affix, count(*) FROM lemmas, wordforms, lemma_wordform, reverse_lemmas WHERE lemmas.lang_id=5 
     *  AND  reverse_lemmas.id=lemmas.id AND lemmas.id=lemma_wordform.lemma_id AND wordforms.id=lemma_wordform.wordform_id 
     *  AND pos_id in (select pos_id from gramset_pos) AND reverse_lemmas.affix not like '#' AND lemma_wordform.affix not like '#' AND wordform like '%iloi' 
     *  group by pos_id, gramset_id, reverse_lemmas.affix, lemma_wordform.affix order by count(*) desc;
     * 
     * @param string $uword
     * @param string $uright
     * @param int $lang_id
     * @param int $total_founded
     * @param array $out
     * @return array
     */
    public static function wordformsByAnalog(string $uword, string $uright, int $lang_id, int $total_founded, array $out, bool $maybe_proper_noun): array {
        $pos_list = PartOfSpeech::changeablePOSIdList();
        $lemmas = Lemma::where('lemmas.lang_id', $lang_id)
                       ->whereIn('pos_id', $pos_list)
                       ->join('lemma_wordform', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                       ->join('wordforms', 'wordforms.id', '=', 'lemma_wordform.wordform_id')
                       ->join('reverse_lemmas', 'lemmas.id', '=', 'reverse_lemmas.id')
                       ->whereNotNull('gramset_id')
                       ->where('gramset_id', '<>', 0)
                       ->where('reverse_lemmas.affix', 'not like', '#')
                       ->where('lemma_wordform.affix', 'not like', '#')
                       ->where('wordform_for_search', 'like', '%'.$uright)
                       ->selectRaw('pos_id, gramset_id, reverse_lemmas.affix as l_affix, lemma_wordform.affix as w_affix, count(*) as count')
                       ->groupBy('pos_id', 'gramset_id', 'reverse_lemmas.affix', 'lemma_wordform.affix')->get();
        foreach ($lemmas as $lemma) {
            list($l_affix, $w_affix) = Str::trimEqualSubstrFromLeft($lemma->l_affix, $lemma->w_affix);
            if (preg_match("/^(.+)".$w_affix."$/u", $uword, $regs)) {
                $predict_lemma = $regs[1]. $l_affix;
//print "$uword - $w_affix + $l_affix = $predict_lemma\n";                
                list ($total, $out) = self::fillByPosGramset($predict_lemma, $lemma->pos_id, $lemma->gramset_id, $lemma->count, $out, $maybe_proper_noun);
                $total_founded += $total;
            }
        }
        return [$total_founded, $out];        
    }
    
    public static function fillByPosGramset(string $predict_lemma, int $pos_id, int $gramset_id, $count, array $out, bool $maybe_proper_noun) {
        if ($maybe_proper_noun && $pos_id==14) {
            $predict_lemma = mb_strtoupper(mb_substr($predict_lemma, 0, 1)). mb_substr($predict_lemma, 1);
        }
        $total_founded = 0;
        $i=$predict_lemma. '_'. $pos_id. '_'. $gramset_id;
        $out[$i] = $count + ($out[$i] ?? 0);
        $total_founded += $count;
        if ($maybe_proper_noun && $pos_id==5) {
            $i=mb_strtoupper(mb_substr($predict_lemma, 0, 1)). mb_substr($predict_lemma, 1). '_14_'. $gramset_id;
            $out[$i] = $count + ($out[$i] ?? 0);
            $total_founded += $count;
        } elseif (!$maybe_proper_noun && $pos_id==14) {
            $i= mb_strtolower($predict_lemma). '_5_'. $gramset_id;
            $out[$i] = $count + ($out[$i] ?? 0);
            $total_founded += $count;
        }
        return [$total_founded, $out];
    }
}