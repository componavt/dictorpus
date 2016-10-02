<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Models\Dict\Gram;
use App\Models\Dict\GramCategory;
use App\Models\Dict\Gramset;
use App\Models\Dict\PartOfSpeech;

class GramsetController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:ref.edit,/dict/gramset/', ['only' => ['create','store','edit','update','destroy']]);
    }
    
     /**
     * Show the list of gramsets.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $pos_id = (int)$request->input('pos_id');
        
        $pos_obj = PartOfSpeech::find($pos_id);
        $gram_fields = GramCategory::getNames();
        
        $gramsets = NULL;
        if ($pos_obj) {
            $gramsets = $pos_obj->gramsets()->get();
        }
        
        $pos_values = PartOfSpeech::getGroupedListWithQuantity('gramsets');
                
        return view('dict.gramset.index')
                ->with(['pos_id'=>$pos_id, 
                        'pos_values' => $pos_values, 
                        'gram_fields' => $gram_fields,
                        'gramsets' => $gramsets]);
    }   

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pos_values = PartOfSpeech::getGroupedList();
        
        $grams = [];        
        foreach (GramCategory::all() as $gc) {         //   id is gram_category_id
            $grams[$gc->name_en] = ['name'=> $gc->name,
                                    'grams' => [NULL=>''] + Gram::getList($gc->id)];
        }

        return view('dict.gramset.create')
                  ->with(['grams' => $grams,
                          'pos_values'=>$pos_values
                         ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $back_url = '/dict/gramset/';

        $this->validate($request, [
            'gram_id_number'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_mood',
            'gram_id_case'  => 'numeric|required_without_all:gram_id_number,gram_id_tense,gram_id_person,gram_id_mood',
            'gram_id_tense'  => 'numeric|required_without_all:gram_id_case,gram_id_number,gram_id_person,gram_id_mood',
            'gram_id_person'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_number,gram_id_mood',
            'gram_id_mood'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number', 
            'sequence_number' => 'numeric',
            'parts_of_speech' => 'required|array'
        ]);

        foreach (GramCategory::getNames() as $gc_name) {
            $column = 'gram_id_'.$gc_name;
            if (!$request[$column]) {
                $request[$column] = NULL;
            }
        }

        $gramset = Gramset::create($request->all());
        
        $gramset ->parts_of_speech()->attach($request['parts_of_speech']);
        
        if (isset($request['parts_of_speech'][0])) {
            $back_url .= '?pos_id='. $request['parts_of_speech'][0];
        }
        
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
        $gramset = Gramset::find($id); 
        $pos_values = PartOfSpeech::getGroupedList();
        
        $pos_value = [];
        foreach ($gramset->parts_of_speech as $pos) {
            $pos_value[] = $pos->id;
        }        
        
        $grams = [];        
        foreach (GramCategory::all() as $gc) {         //   id is gram_category_id
            $grams[$gc->name_en] = ['name'=> $gc->name,
                                    'grams' => [NULL=>''] + Gram::getList($gc->id)];
        }

        return view('dict.gramset.edit')
                  ->with(['grams' => $grams,
                          'pos_values'=>$pos_values,
                          'pos_value'=>$pos_value,
                          'gramset' => $gramset,
                         ]);
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
        $back_url = '/dict/gramset/';
        $this->validate($request, [
            'gram_id_number'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_mood',
            'gram_id_case'  => 'numeric|required_without_all:gram_id_number,gram_id_tense,gram_id_person,gram_id_mood',
            'gram_id_tense'  => 'numeric|required_without_all:gram_id_case,gram_id_number,gram_id_person,gram_id_mood',
            'gram_id_person'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_number,gram_id_mood',
            'gram_id_mood'  => 'numeric|required_without_all:gram_id_case,gram_id_tense,gram_id_person,gram_id_number', 
            'sequence_number' => 'numeric',
            'parts_of_speech' => 'required|array'
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
        $gramset->parts_of_speech()->attach($request['parts_of_speech']);
        
        if (isset($request['parts_of_speech'][0])) {
            $back_url .= '?pos_id='. $request['parts_of_speech'][0];
        }
        
        return Redirect::to($back_url)
                       ->withSuccess(\Lang::get('messages.updated_success'));        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $error = false;
        $status_code = 200;
        $result =[];
        $back_url = '/dict/gramset/';
        
        if($id != "" && $id > 0) {
            try{
                $gramset = Gramset::find($id);
                if($gramset){
                    $parts_of_speech = $gramset->parts_of_speech();
                    if ($parts_of_speech->count()) {
                        $back_url .= '?pos_id='. $parts_of_speech->first()->id;
                    }
                    
                    if (!$gramset->wordforms) {
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
                    $result['error_message'] = \Lang::get('record_not_exists');
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
}
