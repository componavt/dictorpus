<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;

use App\Library\Grammatic;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

class LemmaApiController extends Controller
{
    public function __construct(Request $request)
    {
        // permission= dict.edit, redirect failed users to /dict/lemma/, authorized actions list:
        $this->middleware('auth:dict.add,/dict/lemma/', 
                ['only' => ['suggestTemplates']]);
    }

    /**
     *
     * @return Response
     * 
     */
    public function suggestTemplates(Request $request)
    {
        $lemma = $request->input('lemma');
        $lang_id = (int)$request->input('lang_id');
        $pos_id = (int)$request->input('pos_id');
        $pos = PartOfSpeech::find($pos_id);
        if (!$lemma || !$lang_id || !$pos) {
            return null;
        }
        $is_reflexive = (int)$request->input('is_reflexive');
        $dialect_id = (int)$request->input('dialect_id');
        if (!$dialect_id) {
            $dialect_id = Lang::mainDialectByID($lang_id);
        }
        
        $templates = Grammatic::suggestTemplates($lang_id, $pos_id, $lemma);
        $gramsets = $pos->mainGramsets();

        $wordforms = [];
        foreach ($templates as $i => $template) {
            list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, null, $dialect_id, $is_reflexive);
            foreach ($gramsets as $gramset_id) {
                $wordforms[$i][$gramset_id] = Grammatic::wordformByStems($lang_id, $pos_id, $dialect_id, $gramset_id, $stems, $name_num, $is_reflexive);
            }
        }
//dd($templates);   
//dd($wordforms);        
        return view('dict.lemma.suggest_templates', compact('templates', 'wordforms'));
    }
    
    /**
     * Gets list of relations for drop down list in JSON format
     * Test url: /dict/lemma/meanings_list?lang_id=1&pos_id=1&lemma_id=2810
     * 
     * @return JSON response
     */
    public function meaningsList(Request $request)
    {
        $search_lemma = '%'.$request->input('q').'%';
        $lang_id = (int)$request->input('lang_id');
        $pos_id = (int)$request->input('pos_id');
        $lemma_id = (int)$request->input('lemma_id');

        $all_meanings = [];
        $lemmas = Lemma::where('lemma','like', $search_lemma)
                       ->where('lang_id',$lang_id);
        if ($pos_id) {
            $lemmas = $lemmas->whereIn('pos_id',[$pos_id, PartOfSpeech::getPhraseID()]);
        }
        if ($lemma_id) {
            $lemmas = $lemmas->where('id','<>',$lemma_id);
        }
        $lemmas = $lemmas->orderBy('lemma')->get();
        foreach ($lemmas as $lem) {
            foreach ($lem->meanings as $meaning) {
                $all_meanings[]=['id'  => $meaning->id, 
                                 'text'=> $lem->lemma .' ('.$meaning->getMultilangMeaningTextsString().')'];
            }
        }  

        return Response::json($all_meanings);
    }
    
    /**
     * Gets list of phrase lemmas for drop down list in JSON format
     * Test url: /dict/lemma/phrase_list?lang_id=5
     * 
     * @return JSON response
     */
    public function listWithPosMeaning(Request $request)
    {
        $limit = 1000;
        $search_lemma = $request->input('q').'%';
        $lang_id = (array)$request->input('lang_id');
        $list = [];
        
        $lemmas = Lemma::whereIn('lang_id',$lang_id)
//                       ->where('pos_id','<>',PartOfSpeech::getPhraseID())
                       ->where('lemma','like', $search_lemma)
                       ->take($limit)
                       ->orderBy('lemma')->get();
//dd($lemmas);        
        foreach($lemmas as $lemma) {
            $list[] = ['id'  => $lemma->id, 
                       'text'=> $lemma->lemma. ' ('.$lemma->pos->name.') '.$lemma->phraseMeaning()];
        }

        return Response::json($list);
    }
    
    /**
     * Gets list of lemmas for drop down list in JSON format
     * Test url: /dict/lemma/list?lang_id=5
     * 
     * @return JSON response
     */
    public function lemmaLangList(Request $request)
    {
        $limit = 1000;
//        $search_lemma = '%'.$request->input('q').'%';
        $search_lemma = $request->input('q').'%';
        $lang_id = (int)$request->input('lang_id');
        $list = [];
        
        $lemmas = Lemma::where('lang_id',$lang_id)
                       ->where('lemma','like', $search_lemma)
                       ->take($limit)
                       ->orderBy('lemma')->get();
        
        foreach($lemmas as $lemma) {
            $list[] = ['id'  => $lemma->id, 
                       'text'=> $lemma->lemma. ($lemma->pos ? ' ('.$lemma->pos->name.')' : '')];
//                       'text'=> $lemma->lemma. ' ('.$lemma->pos->name.') '.$lemma->phraseMeaning()];
        }

        return Response::json($list);
    }
}
