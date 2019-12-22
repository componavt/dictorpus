<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;

use App\Models\Dict\ConceptCategory;

class ConceptCategoryController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:ref.edit,/dict/concept_category', ['only' => ['create','store','edit','update','destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $concept_categories = ConceptCategory::orderBy('id')->get();
//dd($concept_categories);        
        return view('dict.concept_category.index',compact('concept_categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $concept_category_values = [NULL=>''] + ConceptCategory::getList();

        return view('dict.concept_category.create')
                  ->with(['concept_category_values' => $concept_category_values]);
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
            'id' => 'required|max:4',
            'name_en'  => 'max:45',
            'name_ru'  => 'required|max:45',
        ]);
        
        $concept_category = ConceptCategory::create($request->all());
        
        return Redirect::to('/dict/concept_category/')
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
        return Redirect::to('/dict/concept_category/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $concept_category = ConceptCategory::find($id); 
        $concept_category_values = [NULL=>''] + $concept_category->getList();
        
        return view('dict.concept_category.edit')
                  ->with(['concept_category' => $concept_category,
                          'concept_category_values' => $concept_category_values,
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
            'name_en'  => 'max:45',
            'name_ru'  => 'required|max:45',
        ]);
//dd($request);       
        
        if (!$request->reverse_concept_category_id) {
            $request->reverse_concept_category_id = NULL;
        }
        
        $concept_category = ConceptCategory::whereId($id)->first();
        $concept_category->fill($request->all())->save();
        
        return Redirect::to('/dict/concept_category/')
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
        if($id != "") {
            try{
                $concept_category = ConceptCategory::whereId($id)->first();
                if($concept_category){
                    $concept_category_name = $concept_category->name;
                    $concept_category->delete();
                    $result['message'] = \Lang::get('dict.category_removed', ['name'=>$concept_category_name]);
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
                return Redirect::to('/dict/concept_category/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/dict/concept_category/')
                  ->withSuccess($result['message']);
        }
    }
}
