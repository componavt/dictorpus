<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use DB;
use LaravelLocalization;

use App\Models\Dict\Relation;

class RelationController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:ref.edit,/dict/relation/', ['only' => 'create','store','edit','update','destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
//        $locale = LaravelLocalization::getCurrentLocale();
//        $relations = Relation::orderBy('name_'.$locale)->get();
        $relations = Relation::orderBy('sequence_number')->get();
        
        return view('dict.relation.index')
                    ->with(['relations' => $relations]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $relation_values = [NULL=>''] + Relation::getList();

        return view('dict.relation.create')
                  ->with(['relation_values' => $relation_values]);
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
            'name_en'  => 'max:150',
            'name_ru'  => 'required|max:150',
        ]);
        
        if (!$request['reverse_relation_id']) {
            $request['reverse_relation_id'] = NULL;
        }
//dd($request);        
        $relation = Relation::create($request->all());
        
        return Redirect::to('/dict/relation/')
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
        return Redirect::to('/dict/relation/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $relation = Relation::find($id); 
        $relation_values = [NULL=>''] + $relation->getList();
        
        return view('dict.relation.edit')
                  ->with(['relation' => $relation,
                          'relation_values' => $relation_values,
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
        
        if (!$request->reverse_relation_id) {
            $request->reverse_relation_id = NULL;
        }
        
        $relation = Relation::find($id);
        $relation->fill($request->all())->save();
        
        return Redirect::to('/dict/relation/')
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
                $relation = Relation::find($id);
                if($relation){
                    $relation_name = $relation->name;
                    $relation->delete();
                    $result['message'] = \Lang::get('dict.relation_removed', ['name'=>$relation_name]);
                }
                else{
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
                return Redirect::to('/dict/relation/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/dict/relation/')
                  ->withSuccess($result['message']);
        }
    }
}
