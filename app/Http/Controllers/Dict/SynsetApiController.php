<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;

use App\Models\Dict\Meaning;
use App\Models\Dict\Synset;
use App\Models\Dict\Syntype;

class SynsetApiController extends Controller
{
    public $url_args=[];
    public $args_by_get='';
    
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth:dict.edit,/dict/synset', 
                          ['except'=>['membersList']]);
        
    }
       
    public function setStatus(int $id, int $status) {
        $synset = Synset::findOrFail($id);
        $synset->status = $status == 1 ? 1 : 0;
        $synset->save();
        return $synset->status;
    }
    
    public function removeMeaning(int $id, int $meaning_id)
    {
        $synset = Synset::find($id);

        return $synset->meanings()->detach($meaning_id);
    }

    public function potentialMembersEdit(int $id, Request $request)
    {
        $synset = Synset::find($id);
        if (empty($synset)) {
            $synset = new Synset;
            $synset->lang_id = (int)$request->lang_id;
            $synset->pos_id = (int)$request->pos_id;
            $synset->save();
        }
        $syntype_values = Syntype::getList(1);
        
        $potential_members = $synset->searchPotentialMembers($request->comment, $request->without);
        
        return view('dict.synset._potential_rows',
                compact('potential_members', 'synset', 'syntype_values'));        
    }
    
    public function membersList(Request $request)
    {
        $limit = 1000;
        $search_lang = (int)$request->search_lang;
        $search_pos = (int)$request->search_pos;
        $term = '%'.$request->input('q').'%';
        $list = [];
        
        $meanings = Meaning::select('meanings.*')
                         ->join('lemmas', 'meanings.lemma_id', '=', 'lemmas.id')
                         ->whereLangId($search_lang)
                         ->wherePosId($search_pos)
                         ->whereNotIn('meanings.id', (array)$request->without)
                         ->where(function ($q) use ($term) {
                             $q->where('lemma','like', $term)
                               ->orWhereIn('meanings.id', function ($q1) use ($term) {
                                   $q1->select('meaning_id')->from('meaning_texts')
                                      ->where('meaning_text', 'like', $term);
                               });
                         })->take($limit)
                         ->orderBy('lemma')->orderBy('lemma_id')->orderBy('meaning_n')
                         ->get();

        foreach($meanings as $meaning) {
            $list[] = ['id'  => $meaning->id, 
                       'text'=> $meaning->lemma->lemma. ' ('.$meaning->meaning_n.'. '.$meaning->getMeaningTextLocale().')'];
        }

        return Response::json($list);        
    }
    
    public function newMembersEdit(Request $request)
    {
        $members = Meaning::whereIn('id', (array)$request->meanings)->get();
        $syntype_values = Syntype::getList(1);
        
        return view('dict.synset._member_rows',
                compact('members', 'syntype_values'));        
    }
}
