<?php

namespace App\Http\Controllers\Library\Experiments;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;
//use App\Http\Requests;
use App\Http\Controllers\Controller;
//use Storage;

use App\Library\Grammatic;
use App\Library\Grammatic\KarGram;
use App\Library\Experiments\Ludgen;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lemma;
use App\Models\Dict\ReverseLemma;

class LudgenController extends Controller
{
    public function __construct(Request $request)
    {
        // permission= corpus.edit, redirect failed users to /corpus/text/, authorized actions list:
        $this->middleware('auth:corpus.edit,/', 
                         ['only' => ['calculate']]);
    }

    public function index() {
        return view('experiments.ludgen.index');
    }
    
    public function words(Request $request) {
        $what = $request->what; 
        list($words, $pos_id) = Ludgen::getLemmas($what);
//dd($words, $pos_id);        
        $gramsets = Gramset::getGroupedList($pos_id, Ludgen::lang_id);
        
        $lemmas = Ludgen::groupedLemmas($words, $gramsets);
//dd($lemmas);
        return view('experiments.ludgen.words',
                compact('gramsets', 'lemmas', 'what'));
    }
    
    public function affixes(Request $request) {
        $what = $request->what; 
        list($lemmas, $pos_id) = Ludgen::getLemmas($what);
        
        $gramsets = Gramset::getGroupedList($pos_id, Ludgen::lang_id);
        
        $affixes = Ludgen::getAffixes($lemmas, $gramsets, $what);
//dd($affixes[array_key_first($affixes)]);
        $cols = array_keys($affixes[array_key_first($affixes)]);
        
        return view('experiments.ludgen.affixes',
                compact('affixes', 'cols', 'gramsets', 'what'));
    }
    
    public function bases(Request $request) {
        $what = $request->what; 
        list($words) = Ludgen::getLemmas($what);
        
        $gramsets = Ludgen::getMainGramsets($what);
        $wordforms = Ludgen::getWordforms($words, array_keys($gramsets));
        $bases = Ludgen::getBases($words);
        $dict_forms = Ludgen::dictForms($words);
//dd($bases);        
        return view('experiments.ludgen.bases',
                compact('bases', 'dict_forms', 'gramsets', 'what', 'wordforms'));
    }
    
    public function dataForTests(Request $request) {
        $what = $request->what; 
        list($words, $pos_id) = Ludgen::getLemmas($what);
        $dialect_id = Ludgen::dialect_id;
        
        print "<pre>";
/*        foreach (Ludgen::dictForms($words) as $lemma_id => $template) {
            print $template."\n";
        }*/
        
        print "\t\$templates = [\n";
        foreach (Ludgen::dictForms($words) as $lemma_id => $template) {
            print "\t    ".$lemma_id ." => '". $template."',\n";
        }
        print "\t];\n\n\n";

        print "\t\$expected = [\n";
        foreach (Ludgen::getBases($words) as $lemma_id => $bases) {
            $lemma = Lemma::find($lemma_id);
            print "\t    ".$lemma_id ." => [0 => [";
            foreach ($bases as $i=>$base) {
                print $i." => ";
                if ($i==10) {
                    print ($base ? 'true': 'false');
                } else {
                    print "'". $base."', ";
                } 
            }
            print "], ".
                  "1 => null, ".
                  "2 => '".$lemma->reverseLemma->stem."', ".
                  "3 => '".$lemma->reverseLemma->affix."'";
            print "],\n";
        }
        print "\t];\n";

        print "\t\$expected = [\n";
        foreach ($words as $id) {
            $lemma = Lemma::find($id);
            print "\t    ".$id ." => [";
            foreach (Grammatic::getListForAutoComplete(Ludgen::lang_id, $pos_id) as $gramset_id) {
                print $gramset_id."=>'".$lemma->wordform($gramset_id, $dialect_id)."', ";
            }
            print "],\n";
        }
        print "\t];\n";
        print "</pre>";
    }

    public function verbTypes() {
        $lang_id = Ludgen::lang_id;
        $dialect_id = Ludgen::dialect_id;
        $pos_id=11;
        $C = "[".KarGram::consSet()."]";
        $V = "[".KarGram::vowelSet()."]";
        $verbs = [];
        
/*        $rlemmas = ReverseLemma::whereIn('id', function ($q) use ($lang_id, $pos_id) {
                    $q->select('id')->from('lemmas')
                      ->whereLangId($lang_id)
                      ->wherePosId($pos_id);
                })->whereIn('id', function ($q) use ($dialect_id) {
                    $q->select('lemma_id')->from('dialect_lemma')
                      ->whereDialectId($dialect_id);
                })->orderBy('reverse_lemma')->get();
        
        
        foreach ($rlemmas as $rlemma) {
            $o = mb_substr($rlemma->reverse_lemma, 0, 5);
            $lemma = $rlemma->lemma;
            $verbs[$o][$rlemma->id] = $lemma->lemma;
        }*/
        
        $lemmas = Lemma::whereLangId($lang_id)
                       ->wherePosId($pos_id)
                       ->whereIn('id', function ($q) use ($dialect_id) {
                            $q->select('lemma_id')->from('dialect_lemma')
                              ->whereDialectId($dialect_id);
                        })->orderBy('lemma')->get();
                        
        foreach ($lemmas as $lemma) {
/*           if (preg_match("/".$C.$V."/", $lemma->lemma, $regs)) {               
           }*/
            for ($i=1; $i<5; $i++) {
                ${'o'.$i} = mb_substr($lemma->lemma_for_search, -1*$i, 1);
                if (in_array(${'o'.$i}, ['a', 'ä'])) {
                    ${'o'.$i} = 'A';
                }
                if (in_array(${'o'.$i}, ['u', 'y'])) {
                    ${'o'.$i} = 'U';
                }
                if (in_array(${'o'.$i}, ['o', 'ö'])) {
                    ${'o'.$i} = 'O';
                }
            }
//            $verbs[$o1][$o2.$o1][$o3.$o2.$o1][$o4.$o3.$o2.$o1][$o5.$o4.$o3.$o2.$o1][$lemma->id] = $lemma->lemma;
            $verbs[$o1]['words'][$o2.$o1]['words'][$o3.$o2.$o1]['words'][$o4.$o3.$o2.$o1]['words'][$lemma->id] = $lemma->lemma;
            $verbs[$o1]['count'] = empty($verbs[$o1]['count']) ? 1 : 1+$verbs[$o1]['count'];
            $verbs[$o1]['words'][$o2.$o1]['count'] = empty($verbs[$o1]['words'][$o2.$o1]['count']) ? 1 : 1+$verbs[$o1]['words'][$o2.$o1]['count'];
            $verbs[$o1]['words'][$o2.$o1]['words'][$o3.$o2.$o1]['count'] = empty($verbs[$o1]['words'][$o2.$o1]['words'][$o3.$o2.$o1]['count']) ? 1 
                    : 1+$verbs[$o1]['words'][$o2.$o1]['words'][$o3.$o2.$o1]['count'];
        }
        ksort($verbs);
//dd($verbs);        
        return view('experiments.ludgen.verb_types',
                compact('verbs'));
    }
}
