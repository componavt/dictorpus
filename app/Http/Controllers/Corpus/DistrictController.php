<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use DB;
use LaravelLocalization;

use App\Models\Corpus\District;
use App\Models\Corpus\Region;

class DistrictController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:corpus.edit','/corpus/district/', ['only' => 'create','store','edit','update','destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $district_name = $request->input('district_name');
        $limit_num = (int)$request->input('limit_num');
        $region_id = (int)$request->input('region_id');
        $search_id = (int)$request->input('search_id');
        $page = (int)$request->input('page');

        if (!$page) {
            $page = 1;
        }
        
        if (!$search_id) {
            $search_id = NULL;
        }
        
        if ($limit_num<=0) {
            $limit_num = 10;
        } elseif ($limit_num>1000) {
            $limit_num = 1000;
        }   
        
        $locale = LaravelLocalization::getCurrentLocale();
        $districts = District::orderBy('name_'.$locale);

        if ($district_name) {
            $districts = $districts->where(function($q) use ($district_name){
                            $q->where('name_en','like', $district_name)
                              ->orWhere('name_ru','like', $district_name);
                    });
        } 

        if ($region_id) {
            $districts = $districts->where('region_id',$region_id);
        } 

        if ($search_id) {
            $districts = $districts->where('id',$search_id);
        } 

        $numAll = $districts->count();

        $districts = $districts->paginate($limit_num);
        
        $region_values = Region::getListWithQuantity('districts');
        
        return view('corpus.district.index')
                    ->with(['districts' => $districts,
                            'limit_num' => $limit_num,
                            'district_name' => $district_name,
                            'region_id'=>$region_id,
                            'search_id'=>$search_id,
                            'page'=>$page,
                            'region_values' => $region_values,
                            'numAll' => $numAll,
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $region_values = Region::getList();
        
        return view('corpus.district.create')
                  ->with(['region_values' => $region_values]);
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
            'region_id' => 'numeric',
        ]);
        
        $district = District::create($request->all());
        
        return Redirect::to('/corpus/district/?search_id='.$district->id)
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
        return Redirect::to('/corpus/district/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $district = District::find($id); 
        $region_values = Region::getList();
        
        return view('corpus.district.edit')
                  ->with(['region_values' => $region_values,
                          'district' => $district]);
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
            'region_id' => 'numeric',
        ]);
        
        $district = District::find($id);
        $district->fill($request->all())->save();
        
        return Redirect::to('/corpus/district/?search_id='.$district->id)
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
                $district = District::find($id);
                if($district){
                    $district_name = $district->name;
                    $district->delete();
                    $result['message'] = \Lang::get('corpus.district_removed', ['name'=>$district_name]);
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
                return Redirect::to('/corpus/district/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/district/')
                  ->withSuccess($result['message']);
        }
    }
}
