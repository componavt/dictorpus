<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use App\Models\Dict\Concept;
use App\Models\Dict\ConceptCategory;

class ConceptController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:ref.edit,/dict/concept/', ['only' => ['create','store','edit','update','destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $concepts = Concept::orderBy('id')->get();
        
        return view('dict.concept.index')
                    ->with(['concepts' => $concepts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $concept_category_values = ConceptCategory::getList();
        $pos_values = Concept::getPOSList();

        return view('dict.concept.create', compact('concept_category_values', 'pos_values'));
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
            'concept_category_id'  => 'required|max:4',
            'pos_id' => 'required|numeric',
            'text_en'  => 'max:150',
            'text_ru'  => 'required|max:150',
        ]);
//dd($request);        
        $concept = Concept::create($request->all());
        
        return Redirect::to('/dict/concept/')
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
        return Redirect::to('/dict/concept/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $concept = Concept::find($id); 
        $concept_values = [NULL=>''] + $concept->getList();
        
        return view('dict.concept.edit')
                  ->with(['concept' => $concept,
                          'concept_values' => $concept_values,
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
        $this->validate($request, [
            'name_en'  => 'max:150',
            'name_ru'  => 'required|max:150',
        ]);
//dd($request);       
        
        $concept = Concept::find($id);
        $concept->fill($request->all())->save();
        
        return Redirect::to('/dict/concept/')
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
        if($id != "" && $id > 0) {
            try{
                $concept = Concept::find($id);
                if($concept){
                    $concept_name = $concept->name;
                    $concept->delete();
                    $result['message'] = \Lang::get('dict.concept_removed', ['name'=>$concept_name]);
                }
                else{
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
                return Redirect::to('/dict/concept/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/dict/concept/')
                  ->withSuccess($result['message']);
        }
    }
}
