<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;

use App\Library\Grammatic;

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
        $lang_id = (int)$request->input('lang_id');
        $pos_id = (int)$request->input('pos_id');
        $lemma = $request->input('lemma');
        
        $templates = Grammatic::suggestTemplates($lang_id, $pos_id, $lemma);
        
        return view('dict.lemma.suggest_templates', compact('templates'));
    }
    
}
