<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
//use DB;
use LaravelLocalization;
use Response;

use App\Models\Corpus\District;
use App\Models\Corpus\Region;
use App\Models\Corpus\Text;

class DistrictController extends Controller
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
        $this->middleware('auth:corpus.edit,/corpus/district/', 
                ['only' => ['create','store','edit','update','destroy']]);
        $this->url_args = District::urlArgs($request);          
        $this->args_by_get = search_values_by_URL($this->url_args);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $districts = District::search($url_args);       
        $numAll = $districts->count();
        $districts = $districts->paginate($url_args['limit_num']);
        
        $region_values = Region::getListWithQuantity('districts');
        
        return view('corpus.district.index',
                    compact('districts', 'region_values', 'numAll', 
                            'args_by_get', 'url_args'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $region_values = Region::getList();
        
        return view('corpus.district.create', compact('region_values'));
    }

    public function validateRequest(Request $request) {
        $this->validate($request, [
            'name_en'  => 'max:150',
            'name_ru'  => 'required|max:150',
            'region_id' => 'numeric',
            ]);
        
        $data = $request->all();
        return $data;
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $district = District::create($this->validateRequest($request));
        
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
        
        return view('corpus.district.edit', compact('district', 'region_values'));
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
        $district = District::find($id);
        $district->fill($this->validateRequest($request))->save();
        
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
                return Redirect::to('/corpus/district/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/district/')
                  ->withSuccess($result['message']);
        }
    }
    
    /**
     * Gets list of districts for drop down list in JSON format
     * Test url: /corpus/district/list?region_id=1
     * 
     * @return JSON response
     */
    public function districtList(Request $request)
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $district_name = '%'.$request->input('q').'%';
        $region_id = $request->input('region_id');

        $list = [];
        $districts = District::where(function($q) use ($district_name){
                            $q->where('name_en','like', $district_name)
                              ->orWhere('name_ru','like', $district_name);
                         });
        if ($region_id) {                 
            $districts = $districts -> where('region_id',$region_id);
        }
        
        $districts = $districts->orderBy('name_'.$locale)->get();
                         
        foreach ($districts as $district) {
            $list[]=['id'  => $district->id, 
                     'text'=> $district->name];
        }  
//dd($list);        
//dd(sizeof($places));
        return Response::json($list);

    }
    
    /**
     * Gets list of districts for drop down list in JSON format
     * Test url: /corpus/district/list?search_region=3
     * 
     * @return JSON response
     */
    public function birthDistrictList(Request $request)
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $district_name = '%'.$request->input('q').'%';
        $region_id = $request->input('region_id');

        $list = [];
        $districts = District::where(function($q) use ($district_name){
                            $q->where('name_en','like', $district_name)
                              ->orWhere('name_ru','like', $district_name);
                         })->whereIn('id', function ($q) {
                            $q->select('district_id')->from('places')
                              ->whereIn('id', function ($q2) {
                                $q2->select('birth_place_id')->from('informants');
                              });
                         });
                         
        if ($region_id) { 
            $districts = $districts -> where('region_id',$region_id);
        }
        $districts = $districts->orderBy('name_'.$locale)->get();
                         
        foreach ($districts as $district) {
            $list[]=['id'  => $district->id, 
                     'text'=> $district->name];
        }  
        return Response::json($list);
    }    
    
    public function textCount($id, Request $request) {
        $without_link = $request->without_link;
        $district = District::find($id);     
        $count = Text::whereIn('event_id', function ($q) use ($id) {
                        $q->select('id')->from('events')
                          ->whereIn('place_id', function ($q2) use ($id) {
                             $q2->select('id')->from('places')
                                ->whereDistrictId($id);
                          });                
                     })->count();
        $count = number_format($count, 0, ',', ' ');
        if (!$count || $without_link) {
            return $count;
        }
        return '<a href="'.LaravelLocalization::localizeURL('/corpus/text?search_district='.$id).'">'.$count.'</a>';
    }
}
