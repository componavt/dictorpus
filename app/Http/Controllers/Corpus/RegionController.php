<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use DB;
use LaravelLocalization;

use App\Models\Corpus\Region;

class RegionController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:corpus.edit,/corpus/region/', ['only' => ['create','store','edit','update','destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $region_name = $request->input('region_name');
        $search_id = (int)$request->input('search_id');

        if (!$search_id) {
            $search_id = NULL;
        }
        
        $locale = LaravelLocalization::getCurrentLocale();
        $regions = Region::orderBy('name_'.$locale);

        if ($region_name) {
            $regions = $regions->where(function($q) use ($region_name){
                            $q->where('name_en','like', $region_name)
                              ->orWhere('name_ru','like', $region_name);
                    });
        } 

        if ($search_id) {
            $regions = $regions->where('id',$search_id);
        } 

        $numAll = $regions->count();

        $regions = $regions->get();
        
        return view('corpus.region.index')
                    ->with(['regions' => $regions,
                            'region_name' => $region_name,
                            'search_id'=>$search_id,
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
        return view('corpus.region.create');
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
        
        $region = Region::create($request->all());
        
        return Redirect::to('/corpus/region/?search_id='.$region->id)
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
        return Redirect::to('/corpus/region/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $region = Region::find($id); 
        
        return view('corpus.region.edit')
                  ->with(['region' => $region]);
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
        
        $region = Region::find($id);
        $region->fill($request->all())->save();
        
        return Redirect::to('/corpus/region/?search_id='.$region->id)
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
                $region = Region::find($id);
                if($region){
                    $region_name = $region->name;
                    $region->delete();
                    $result['message'] = \Lang::get('corpus.region_removed', ['name'=>$region_name]);
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
                return Redirect::to('/corpus/region/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/region/')
                  ->withSuccess($result['message']);
        }
    }
}
