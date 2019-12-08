<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Models\Dict\Gram;
use App\Models\Dict\GramCategory;
use App\Models\Dict\Gramset;
use App\Models\Dict\GramsetCategory;
use App\Models\Dict\Lang;
use App\Models\Dict\PartOfSpeech;

class GramsetController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth:ref.edit,/dict/gramset/', ['only' => ['create','store','edit','update','destroy']]);
        $this->url_args = [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_category' => (int)$request->input('search_category'),
                    'search_lang'     => (int)$request->input('search_lang'),
                    'search_pos'      => (int)$request->input('search_pos'),
                ];
        
        if (!$this->url_args['page']) {
            $this->url_args['page'] = 1;
        }
        
        if ($this->url_args['limit_num']<=0) {
            $this->url_args['limit_num'] = 25;
        } elseif ($this->url_args['limit_num']>1000) {
            $this->url_args['limit_num'] = 1000;
        }   
        
        $this->args_by_get = Lang::searchValuesByURL($this->url_args);
    }
    
     /**
     * Show the list of gramsets.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $gramsets = Gramset::search($url_args);
        $numAll = sizeof($gramsets->get());
        $gramsets = $gramsets->paginate($url_args['limit_num']);         

        $pos_values = PartOfSpeech::getGroupedListWithQuantity('gramsets');
        $lang_values = Lang::getListWithQuantity('gramsets');
        $category_values = GramsetCategory::getList();

        $gram_fields = Gramset::fieldsForIndex($gramsets);
              
        $url_args_for_out = $url_args; // for links to lemmas and wordforms
        unset($url_args_for_out['page']);
        $args_by_get_for_out = Lang::searchValuesByURL($url_args_for_out);
        
        
        return view('dict.gramset.index',
                compact('gram_fields', 'category_values', 'gramsets', 
                        'lang_values', 'numAll', 'pos_values', 
                        'args_by_get', 'args_by_get_for_out', 'url_args'));
    }   

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $pos_values = PartOfSpeech::getGroupedList();
        $lang_values = Lang::getList();
        
        $gramset_category_values = GramsetCategory::getList();
        
        $grams = [];        
        foreach (GramCategory::all()->sortBy('sequence_number') as $gc) {         //   id is gram_category_id
            $grams[$gc->name_en] = ['name'=> $gc->name,
                                    'grams' => [NULL=>''] + Gram::getList($gc->id)];
        }

        return view('dict.gramset.create',
                  compact('grams', 'gramset_category_values',
                          'lang_values', 'pos_values',
                          'args_by_get', 'url_args'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'gram_id_number'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle',
            'gram_id_case'  => 'numeric|required_without_all:gram_id_number,gram_id_tense,gram_id_person,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle',
            'gram_id_tense'  => 'numeric|required_without_all:gram_id_case,gram_id_number,gram_id_person,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle',
            'gram_id_person'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_number,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle',
            'gram_id_mood'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle', 
            'gram_id_negation'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_mood,gram_id_infinitive,gram_id_voice,gram_id_participle', 
            'gram_id_infinitive'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_mood,gram_id_negation,gram_id_voice,gram_id_participle', 
            'gram_id_voice'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_participle', 
            'gram_id_participle'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice', 
            'gram_id_reflexive'  => 'numeric', 
            'sequence_number' => 'numeric',
            'parts_of_speech' => 'required|array',
            'langs' => 'required|array',
        ]);

        foreach (GramCategory::getNames() as $gc_name) {
            $column = 'gram_id_'.$gc_name;
            if (!$request[$column]) {
                $request[$column] = NULL;
            }
        }

        $gramset = Gramset::create($request->all());
        
//        $gramset ->parts_of_speech()->attach($request['parts_of_speech']);
        
        foreach ($request['parts_of_speech'] as $p_id) {
            foreach ($request['langs'] as $l_id) {
                $gramset-> parts_of_speech()->attach($p_id, ['lang_id'=>$l_id]);
                }
        }
 
        $back_url = '/dict/gramset/?'.$this->args_by_get;

        return Redirect::to($back_url)
                       ->withSuccess(\Lang::get('messages.created_success'));        
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Redirect::to('/dict/gramset/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $gramset = Gramset::find($id); 
        $pos_values = PartOfSpeech::getGroupedList();
        
        $pos_value = [];
        foreach ($gramset->parts_of_speech as $pos) {
            $pos_value[] = $pos->id;
        }        
        
        $lang_values = Lang::getList();
        $lang_value = [];
        foreach ($gramset->langs as $lang) {
            $lang_value[] = $lang->id;
        }        

        $gramset_category_values = GramsetCategory::getList();
        
        $grams = [];        
        foreach (GramCategory::all()->sortBy('sequence_number') as $gc) {         //   id is gram_category_id
            $grams[$gc->name_en] = ['name'=> $gc->name,
                                    'grams' => [NULL=>''] + Gram::getList($gc->id)];
        }

        return view('dict.gramset.edit',
                  compact('grams', 'gramset', 'gramset_category_values',
                          'lang_value', 'lang_values', 'pos_value',
                          'pos_values', 'args_by_get', 'url_args'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'gram_id_number'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle',
            'gram_id_case'  => 'numeric|required_without_all:gram_id_number,gram_id_tense,gram_id_person,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle',
            'gram_id_tense'  => 'numeric|required_without_all:gram_id_case,gram_id_number,gram_id_person,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle',
            'gram_id_person'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_number,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle',
            'gram_id_mood'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_negation,gram_id_infinitive,gram_id_voice,gram_id_participle', 
            'gram_id_negation'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_mood,gram_id_infinitive,gram_id_voice,gram_id_participle', 
            'gram_id_infinitive'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_mood,gram_id_negation,gram_id_voice,gram_id_participle', 
            'gram_id_voice'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_participle', 
            'gram_id_participle'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number,gram_id_mood,gram_id_negation,gram_id_infinitive,gram_id_voice', 
            'gram_id_reflexive'  => 'numeric', 
            'sequence_number' => 'numeric',
            'parts_of_speech' => 'required|array',
            'langs' => 'required|array',
        ]);

        foreach (GramCategory::getNames() as $gc_name) {
            $column = 'gram_id_'.$gc_name;
            if (!$request[$column]) {
                $request[$column] = NULL;
            }
        }
        
        $gramset = Gramset::find($id);
        $gramset->fill($request->all())->save();

        $gramset->parts_of_speech()->detach();
//        $gramset->parts_of_speech()->attach($request['parts_of_speech']);

        foreach ($request['parts_of_speech'] as $pos_id) {
            foreach ($request['langs'] as $lang_id) {
                $gramset-> parts_of_speech()->attach($pos_id, ['lang_id'=>$lang_id]);
                }
        }
 
        $back_url = '/dict/gramset/'.$this->args_by_get;

        return Redirect::to($back_url)
                       ->withSuccess(\Lang::get('messages.updated_success'));        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $error = false;
        $status_code = 200;
        $result =[];

        $back_url = '/dict/gramset/'.$this->args_by_get;
                
        if($id != "" && $id > 0) {
            try{
                $gramset = Gramset::find($id);
                if($gramset){
                    $parts_of_speech = $gramset->parts_of_speech();
                    if (!$gramset->wordforms()->count()) {
                        $gramset_name = $gramset->gramsetString();
                        $parts_of_speech->detach();
                        $gramset->delete();
                        $result['message'] = \Lang::get('dict.gramset_removed', ['name'=>$gramset_name]);
                    } else {
                        $error = true;
                        $result['error_message'] = \Lang::get('dict.gramset_has_wordform');
                    }    
                } else {
                    $error = true;
                    $result['error_message'] = \Lang::get('messages.record_not_exists');
                }
          }catch(\Exception $ex){
                    $error = true;
                    $status_code = $ex->getCode();
                    $result['error_code'] = $ex->getCode();
                    $result['error_message'] = $ex->getMessage();
                }
        }else{
            $error =true;
            $status_code = 400;
            $result['message']='Request data is empty';
        }
        
        if ($error) {
                return Redirect::to($back_url)
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to($back_url)
                  ->withSuccess($result['message']);
        }
    }

    /**
     * Gets list of gramsets for drop down list in JSON format
     * Test url: /dict/gramset/list?lang_id=1&pos_id=1
     * 
     * @return JSON response
     */
    public function gramsetList(Request $request)
    {

        $search_gramset = $request->input('q');
        $lang_id = (int)$request->input('lang_id');
        $pos_id = (int)$request->input('pos_id');
        $gramsets = Gramset::getList($pos_id,$lang_id,true);

        $list = [];
        foreach ($gramsets as $gramset_id =>$gramset_name) {
            if (preg_match("/".$search_gramset."/", $gramset_name)) {
                $list[]=['id'  => $gramset_id, 
                         'text'=> $gramset_name];
            }
        }  
//dd($list);        
        return Response::json($list);
    }
}
