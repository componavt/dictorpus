<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LaravelLocalization;
//use Illuminate\Support\Facades\Redirect;

use App\Library\Ldl;

use App\Models\Dict\Concept;
//use App\Models\Dict\ConceptCategory;
//use App\Models\Dict\Dialect;
//use App\Models\Dict\Label;
use App\Models\Dict\Lemma;
use App\Models\Dict\Meaning;
//use App\Models\Dict\PartOfSpeech;
//use App\Models\Dict\Relation;

class LdlController extends Controller
{
    public $url_args=[];
    public $args_by_get='';
    
    public function __construct(Request $request)
    {        
        $this->url_args = url_args($request) + 
            [
/*                'by_alpha'  => (int)$request->input('by_alpha'),
                'search_concept_category'  => $request->input('search_concept_category'),
                'search_concept'  => (int)$request->input('search_concept'),
                'search_gram'    => $request->input('search_gram'),
                'search_lemma'    => $request->input('search_lemma'),*/
                'search_letter'    => $request->input('search_letter'),
/*                'search_meaning'    => $request->input('search_meaning'),
                'search_pos'    => $request->input('search_pos'),
                'search_word'    => $request->input('search_word'),
                'with_audios'    => (int)$request->input('with_audios'),
                'with_photos'    => (int)$request->input('with_photos'),
                'with_template'    => (int)$request->input('with_template'),*/
            ];
        $this->url_args['limit_num'] = 5;
        $this->args_by_get = search_values_by_URL($this->url_args);
    }
    
    public function index() {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        $locale = LaravelLocalization::getCurrentLocale();
        
        $alphabet = Ldl::alphabet();
        if ((!$url_args['search_letter'] || !in_array($url_args['search_letter'], $alphabet)) && isset($alphabet[0])) {
            $url_args['search_letter'] = $alphabet[0];
        }
        
        $concepts = Ldl::concepts();
        
        return view('ldl.index',
                compact('alphabet', 'concepts', 'args_by_get', 'url_args'));
    }
    
    public function concept(int $concept_id) {
        $concept = Concept::find($concept_id);
        if (!$concept) {
            return;
        }
        
        $lang_id=6;
        $without_dialect = 45;
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $alphabet = Ldl::alphabet();
        
        $lemmas = Lemma::whereLangId($lang_id)
                       ->forLdl()
                       ->forConcept($concept_id)
//dd(to_sql($lemmas));       
                       ->orderBy('lemma')->get();

        return view('ldl.concept',
                compact('alphabet', 'concept', 'lemmas', 'without_dialect',
                        'args_by_get', 'url_args'));
    }
    
    /**
     * test: /dict/meaning/examples/reload/23813
     * 
     * @param INT $id
     * @return \Illuminate\Http\Response
     */
    public function loadExamples (int $id, Request $request) {
        $limit = 5;
        $start = (int)$request->input('start');
        $meaning = Meaning::find($id);
        if (!$meaning) {
            return NULL;
        }
        
        $sentence_count = $meaning->countSentences(false, 4);
        if (!$sentence_count) {
            return '';
        }
        $sentences = $meaning->sentences(false, $limit, $start);
        $count=1+$start;   
//dd($sentences);        
        return view('ldl.examples', 
                compact('meaning', 'limit', 'start', 'count',
                        'sentence_count', 'sentences')); 
    }
    
    public function stats() {
        $lang_id=6;
        $without_dialect = 45;
        $symbols = 0;
        $lemmas_c = 0;
        $meanings_c = 0;
        $sentences_c = 0;
        $wordforms_c = 0;
        
        $concepts = Ldl::concepts();
        $concepts_c = sizeof($concepts);
        
        foreach ($concepts as $concept) {
            $lemmas = Lemma::whereLangId($lang_id)
                           ->forLdl()
                           ->forConcept($concept->id)->get();
            $lemmas_c += sizeof($lemmas);
            
            foreach ($lemmas as $lemma) {
//                $symbols += mb_strlen($lemma->lemma); 
                $meanings_c +=$lemma->meanings()->count();
                foreach ($lemma->meanings as $meaning) {
/*                    foreach($meaning->meaningTexts()->pluck('meaning_text')->toArray() as $meaning_text) {
                        $symbols += mb_strlen($meaning_text);                     
                    } */
                    $sentences_c += $meaning->countSentences(false, 4);
/*                    $sentences = $meaning->sentences(false, '', 0, 10);
                    $sentences_c += sizeof($sentences);
                    foreach ($sentences as $sent) {
                        $symbols += mb_strlen($sent['s']);                                         
                        $symbols += mb_strlen($sent['trans_s']);                                         
                    }*/
                }
                foreach ($lemma->wordforms()->wherePivot('dialect_id','<>', $without_dialect)->get()
                            as $wordform) {
                    $wordforms_c += 1;
//                    $symbols += mb_strlen($wordform->wordform);                     
                }
            }
        }
        
//dd($lemmas);        
        return view('ldl.stats',
                compact('concepts_c', 'lemmas_c', 'meanings_c', 'sentences_c', 'symbols', 'wordforms_c'));
    }
}
