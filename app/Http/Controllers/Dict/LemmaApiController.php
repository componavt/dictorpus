<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;

use App\Library\Grammatic;
use App\Models\Dict\Lang;
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
    
}
