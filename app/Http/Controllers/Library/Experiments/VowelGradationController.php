<?php

namespace App\Http\Controllers\Library\Experiments;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

use App\Library\Str;
use App\Library\Grammatic\KarGram;

use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Lemma;

class VowelGradationController extends Controller
{
/* нет редактирования, можно открыть
 *     public function __construct()
    {
        $this->middleware('auth:dict.edit,/'); 
    }
*/
    /**
     * 1) делим каждый документ на 2 по частям речи (получаем 4 выборки)
     * 2) выделяем из каждой слова, у которых в ном. ед. 2, 3 и 4 слога (получаем 12 выборок)
     * 3) в каждой отделяем слова, у которых парт. мн. заканчивается на oi / öi и отдельно на ii + отдельно те, у которых 2 варианта дается (получаем 36 выборок) и располагаем слова внутри каждой в алфавитном порядке с конца слова (по форме парт. мн.). Итого получаем 36 выборок
     * 
     */
    public function index() {
        $pos_list = ['NOUN' => 'существительные', 'ADJ' => 'прилагательные'];
        $u = [1=>'u', 2=>'y'];
        $a = [1=>'a', 2=>'ä'];
        $parts = [1=>[1=>'парт. мн.ч. на oi', 2=>'парт. мн.ч. на öi'], 2=> 'парт. мн.ч. на ii', 3=>'два парт. мн.ч.', 4=> 'парт. мн.ч. на Ci'];
        return view('experiments/vowel_gradation/index', compact('pos_list', 'a', 'u', 'parts'));
    }
    
    /**
     * Для начала нужно из словоформ ливвиковского младописьменного варианта выбрать только имена (существительные и прилагательные), которые:
     * 1) в форме номинатива ед. заканчиваются на u или a, при этом в форме генитива ед. заканчиваются на an, далее нужно выбрать для них формы партитива множественного числа;
     * 2) в форме номинатива ед. заканчиваются на y или ä, при этом в форме генитива ед. заканчиваются на än, далее нужно выбрать для них формы партитива множественного числа.
     * Так мы выберем только нужные для анализа формы, а вот потом придется закономерности искать))
     * 
     * Закономерности проверить нужно будет по следующим параметрам: 
     * 1) отдельно работаем с двусложными формами и отдельно с трех- и четырехсложными 
     * (если слово сложное, то учитываем только последний компонент): т.е. предыдущие 2 выборки надо разбить еще на 2 и дать отдельно формы с окончанием на oi / öi и отдельно на Ci; 
     * 2) по двусложным формам: проверяем гласный компонент первого слога - тут должно быть все четко!, 
     * 3) по трехсложным формам: тут варианты есть: проверить по гласным второго слога, по последнему согласному (сочетаниям согласных), или в совокупности. Надо будет смотреть по ходу. 
     * Может, когда выборка будет перед глазами, еще вариант найдется))
     *  
     */
    public function nomGenPart($num, $pos_code, $sl, $part_gr) {
        if ($num==1) {
            $u='u';
            $a='a';
            $o='o';
        } else {
            $num=2;
            $u='y';
            $a='ä';
            $o='ö';
        }
        if ($pos_code != 'NOUN') {
            $pos_code = 'ADJ';
        }
        $pos_list = ['NOUN' => 'существительные', 'ADJ' => 'прилагательные'];
        $parts = [1=>'парт. мн.ч. на '.$o.'i', 2=> 'парт. мн.ч. на ii', 3=>'два парт. мн.ч.', 4=> 'парт. мн.ч. на Ci'];
        $part_gr_name = $parts[$part_gr];
        
        $lang_id = 5;
        $dialect_id = 44;
        $nomSg_id = 1;
        $genSg_id = 3;
        $partPl_id = 22;
        $words = [];
        
        $pos = PartOfSpeech::getByCode($pos_code);
        $pos_id = $pos->id;
        $pos_name = $pos_list[$pos_code];
        $lemmas = Lemma::where('pos_id',$pos_id)
                        ->where('lang_id',$lang_id)
                        ->join('lemma_wordform', 'lemma_wordform.lemma_id', '=', 'lemmas.id')
                        ->where('gramset_id', $nomSg_id)
                        ->where('dialect_id', $dialect_id)
                        ->join('wordforms', 'lemma_wordform.wordform_id', '=', 'wordforms.id')
                        ->where(function ($query) use($a,$u) {
                            $query->where('wordform', 'like', '%'.$u)
                                  ->orWhere('wordform', 'like', '%'.$a);
                        })
                        ->select(DB::raw("lemmas.id as id, wordform"))
                        ->get();
        foreach ($lemmas as $lemma) {
            $nom = $lemma->wordform;
            if (KarGram::countSyllable($nom)!=$sl) {
                continue;
            }
            $gens = preg_split("/,\s*/", $lemma->wordform($genSg_id, $dialect_id));
            $tmp = [];
            foreach ($gens as $gen) {
                if (preg_match("/".$a."n$/u", $gen)) {
                    $tmp[]=$gen;
                }
            }
            if (sizeof($tmp)) {
                $part = $lemma->wordform($partPl_id, $dialect_id);
                $part_list = preg_split("/,\s/", $part);
                if ($part_gr==1 && sizeof($part_list)==1 && preg_match("/".$o."i$/", $part)
                        || $part_gr==2 && sizeof($part_list)==1 && preg_match("/ii$/", $part)
                        || $part_gr==3 && sizeof($part_list)>1 
                        || $part_gr==4 && preg_match("/[".KarGram::consSet()."]i$/", $part)) {
                    $words[$lemma->id]=[
                        'nom' =>$nom,
                        'gen' =>join(', ', $tmp),
                        'part' =>$part,
                        'part_r' => Str::reverse($part)];
                }
            }
        }
        $words = collect($words);
        $words = $words->sortBy('part_r');
        return view('experiments/vowel_gradation/nom_gen_part', compact('num', 'words', 'pos_name', 'sl', 'part_gr_name'));
    }    
}
