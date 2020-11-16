<?php
namespace App\Library;

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
        $uword_for_search = toSearchForm($uword);
        $out = array_merge(self::lemmasFromOtherLangsByAnalog($uword_for_search, $lang_id),
                                      self::wordformsFromOtherLangsByAnalog($uword_for_search, $lang_id));
        $out = self::lemmasWordformsByAnalog($uword_for_search, $lang_id, $out);
        return $out;
    }
    
    /**
     * Search unchangable lemmas in other languages by analogy
     * 
     * @param string $uword
     * @param int $lang_id
     * @return array
     */
    public static function lemmasFromOtherLangsByAnalog(string $uword, int $lang_id): array {
        $out = [];
        $lemmas = Lemma::where('lemma_for_search', 'like', $uword)
                       ->whereIn('pos_id', PartOfSpeech::notChangeablePOSIdList())
                       ->where('lang_id', '<>', $lang_id)->get();
        foreach ($lemmas as $lemma) {
            $i=$lemma->lemma. '_'. $lemma->pos_id. '_';
            $out[$i] = 1+ ($out[$i] ?? 0);
        }
        return $out;
    }
    
    /**
     * Search wordforms in other languages by analogy
     * 
     * @param string $uword
     * @param int $lang_id
     * @return array
     */
    public static function wordformsFromOtherLangsByAnalog(string $uword, int $lang_id): array {
        $out = [];
        $lemmas = Lemma::where('lang_id', '<>', $lang_id)
                       ->whereIn('pos_id', PartOfSpeech::changeablePOSIdList())
                       ->join('lemma_wordform', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                       ->whereNotNull('gramset_id')
                       ->where('gramset_id', '<>', 0)
                       ->whereIn('wordform_id', function ($query) use ($uword) {
                           $query->select('id')->from('wordforms')
                                 ->where('wordform_for_search', 'like', $uword);
                       })->groupBy('lemma_id', 'gramset_id')->get();
        foreach ($lemmas as $lemma) {
            $i=$lemma->lemma. '_'. $lemma->pos_id. '_'. $lemma->gramset_id;
            $out[$i] = 1+ ($out[$i] ?? 0);
        }
        return $out;
    }
    
    /**
     * Search wordforms in the same language by analogy
     * 
     * Unchanheable lemmas:
     * 
     * Wordforms:
     * SELECT pos_id, gramset_id, reverse_lemmas.affix, lemma_wordform.affix, count(*) FROM lemmas, wordforms, lemma_wordform, reverse_lemmas WHERE lemmas.lang_id=5 
     *  AND  reverse_lemmas.id=lemmas.id AND lemmas.id=lemma_wordform.lemma_id AND wordforms.id=lemma_wordform.wordform_id 
     *  AND pos_id in (select pos_id from gramset_pos) AND reverse_lemmas.affix not like '#' AND lemma_wordform.affix not like '#' AND wordform like '%iloi' 
     *  group by pos_id, gramset_id, reverse_lemmas.affix, lemma_wordform.affix order by count(*) desc;
     * 
     * @param string $uword
     * @param int $lang_id
     * @param array $out other predictions
     * @return array
     */
    public static function lemmasWordformsByAnalog(string $uword, int $lang_id, array $out): array {
        $found_enough = 10; // порог общего количество найденных вариантов
        $rang_enough=0.5; // порог доли первой пары pos_id-gramset_id среди всех найденных
        $total_founded=0;
        $rang1=0;
        $uleft='';
        $uright=$uword;
        $len_right = mb_sublen($uright);
        $pos_list = PartOfSpeech::changeablePOSIdList();
        
        while ($total_founded < $found_enough && $rang1 < $rang_enough && $len_right > 1) {
            $uleft .= mb_substr($uright,0,1);
            $uright = mb_substr($uright,1);
            $lemmas = Lemma::whereLangid($lang_id)
                           ->whereIn('pos_id', $pos_list)
                           ->join('lemma_wordform', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                           ->join('wordforms', 'wordforms.id', '=', 'lemma_wordform.wordform_id')
                           ->join('reverse_lemmas', 'lemmas.id', '=', 'reverse_lemmas.lemma_id')
                           ->whereNotNull('gramset_id')
                           ->where('gramset_id', '<>', 0)
                           ->where('reverse_lemmas.affix', 'not like', '#')
                           ->where('lemma_wordform.affix', 'not like', '#')
                           ->where('wordform', 'like', '%'.$uright)
                           ->selectRaw('pos_id, gramset_id, reverse_lemmas.affix as l_affix, lemma_wordform.affix as w_affix, count(*) as count')
                           ->groupBy('pos_id', 'gramset_id', 'reverse_lemmas.affix', 'lemma_wordform.affix')->get();
            foreach ($lemmas as $lemma) {
                list($l_affix, $w_affix) = Str::trimEqualSubstrFromLeft($lemma->l_affix, $lemma->w_affix);
                $i=$lemma->lemma. '_'. $lemma->pos_id. '_'. $lemma->gramset_id;
                $out[$i] = 1+ ($out[$i] ?? 0);
            }
            asort($out);
        }
        return $out;
    }
}