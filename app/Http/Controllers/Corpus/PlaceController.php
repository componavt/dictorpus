<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
//use DB;
use LaravelLocalization;
use Response;

use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;

use App\Models\Corpus\District;
use App\Models\Corpus\Place;
use App\Models\Corpus\PlaceName;
use App\Models\Corpus\Region;

class PlaceController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth:corpus.edit,/corpus/place/', ['only' => ['create','store','edit','update','destroy']]);

        $this->url_args = Place::urlArgs($request);  
        
        $this->args_by_get = search_values_by_URL($this->url_args);
    }

    /**
     * Show the list of places.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $places = Place::search($url_args);

        $numAll = $places->count();

        $places = $places->paginate($url_args['limit_num']);
        
        $region_values = Region::getListWithQuantity('places');
        $district_values = District::getListWithQuantity('places');

        return view('corpus.place.index', 
                    compact('places', 'region_values', 'district_values', 
                            'numAll', 'args_by_get', 'url_args'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $region_values = Region::getList();
        $district_values = [NULL => ''] + District::getList();
        $lang_values = [NULL => ''] + Lang::getList([Lang::getIDByCode('en'), 
                                      Lang::getIDByCode('ru')]);
        $dialect_values = Dialect::getList(); 
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        return view('corpus.place.create',
                  compact(['dialect_values', 'district_values', 'lang_values', 
                           'region_values', 'args_by_get', 'url_args']));
    }

    public function validateRequest(Request $request) {
        $this->validate($request, [
            'name_en'  => 'max:150',
            'name_ru'  => 'required|max:150',
            'district_id' => 'required|numeric',
            'region_id' => 'required|numeric',
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
        $this->validateRequest($request);
        $place = Place::create($request->only('district_id','region_id','name_en','name_ru', 'latitude', 'longitude'));

        foreach ($request->other_names as $lang => $other_name) {
            if ($other_name) {
                $name= PlaceName::create(['place_id'=>$place->id, 
                                          'lang_id'=>$lang,
                                          'name'=>$other_name]);
            }
        }
        
        $place->dialects()->attach($request->dialects);
        
        return Redirect::to('/corpus/place/?search_id='.$place->id.($this->args_by_get))
            ->withSuccess(\Lang::get('messages.created_success'));        
    }

    public function simpleStore(Request $request)
    {
        $this->validateRequest($request);
        $place = Place::create($request->all());
        $place->dialects()->attach($request->dialects);
        $lang_id=Lang::getIDByCode(LaravelLocalization::getCurrentLocale());
        return Response::json([$place->id, $place->placeString($lang_id)]);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Redirect::to('/corpus/place/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $place = Place::find($id); 
        $region_values = Region::getList();
        $district_values = [NULL => ''] + District::getList();
        $lang_values = [NULL => ''] + Lang::getList([Lang::getIDByCode('en'), 
                                      Lang::getIDByCode('ru')]);
        
        $other_names =[];
        foreach($place->other_names as $other_name) {
            $other_names[$other_name->lang_id] = $other_name->name;
        }

        $dialect_values = Dialect::getList(); 
        $dialect_value = $place->dialectValue();
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        return view('corpus.place.edit',
                  compact(['dialect_value', 'dialect_values', 'district_values',
                          'lang_values', 'other_names', 'place', 'region_values',
                          'args_by_get', 'url_args']));
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
            'district_id' => 'required|numeric',
            'region_id' => 'required|numeric',
        ]);
        $place = Place::find($id);
        $place->fill($request->only('district_id','region_id','name_en','name_ru', 'latitude', 'longitude'))->save();
        
        foreach ($place->other_names as $other_name) {
            $other_name->delete();
        }
        
        foreach ($request->other_names as $lang => $other_name) {
            if ($other_name) {
                $name= PlaceName::create(['place_id'=>$place->id, 
                                          'lang_id'=>$lang,
                                          'name'=>$other_name]);
            }
        }
        
        $place->dialects()->detach();
        $place->dialects()->attach($request->dialects);
        
        return Redirect::to('/corpus/place/'.($this->args_by_get))
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
                $place = Place::find($id);
                if($place){
                    $place_name = $place->name;
                    foreach ($place->other_names as $other_name) {
                       $other_name->delete();
                    }
                    if (!empty($place->texts()->count()) || !empty($place->eventTexts()->count())) {
                        $error = true;
                        $result['error_message'] = \Lang::get('messages.text_exists');
                    } elseif ($place->informants()->count() >0) {
                        $error = true;
                        $result['error_message'] = \Lang::get('messages.informant_exists');
                    } else {
                        foreach ($place->events as $event) {
                            $event->recorders()->detach();
                            $event->delete();
                        }
                        $place->dialects()->detach();
                        $place->delete();
                        $result['message'] = \Lang::get('corpus.place_removed', ['name'=>$place_name]);
                    }
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
                return Redirect::to('/corpus/place/'.$this->args_by_get)
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/place/'.$this->args_by_get)
                  ->withSuccess($result['message']);
        }
    }
    
    /**
     * Gets list of places for drop down list in JSON format
     * Test url: /corpus/place/list?lang_id[]=1
     * 
     * @return JSON response
     */
    public function placeList(Request $request)
    {
        $locale = LaravelLocalization::getCurrentLocale();

        $place_name = '%'.$request->input('q').'%';
        $lang_ids = (array)$request->input('lang_id');
        $with_meanings = (boolean)$request->input('with_meanings');

        $region_id = $request->input('region_id');
        $district_ids = (array)$request->input('district_id');
        
        $list = [];
        $places = Place::where(function($q) use ($place_name){
                            $q->where('name_en','like', $place_name)
                              ->orWhere('name_ru','like', $place_name);
                         });
        if (sizeof($district_ids)) {                 
            $places = $places -> whereIn('district_id',$district_ids);
        }
                         
        if ($region_id) {                 
            $places = $places -> whereIn('district_id', function ($q) use ($region_id) {
                $q->select('id')->from('districts')
                  ->whereRegionId($region_id);
            });
        }
                         
        if (sizeof($lang_ids)) {                 
            $places = $places -> whereIn('id', function ($q) use ($lang_ids) {
                            $q->select('place_id')->from('dialect_place')
                              ->whereIn('dialect_id', function ($q2) use ($lang_ids) {
                                  $q2->select('id')->from('dialects')
                                     ->whereIn('lang_id',$lang_ids);
                              });
                        });
        }
        
        if ($with_meanings) {
            $places = $places->whereIn('id',function ($query) use ($lang_ids) {
                $query->select('place_id')->from('meaning_place')
                      ->whereIn('meaning_id', function($q1) use ($lang_ids) {
                        $q1->select('id')->from('meanings')
                           ->whereIn('lemma_id', function($q2) use ($lang_ids) {
                            $q2->select('id')->from('lemmas')
                               ->whereIn('lang_id',$lang_ids);
                          });
                      });
            });            
        }
        
        $places = $places->orderBy('name_'.$locale)->get();//->pluck('name_'.$locale, 'id')->toArray();
//        return Response::json($places);
                         
        foreach ($places as $place) {
            $list[]=['id'  => $place->id, 
                     'text'=> $place->name];
        }  
//dd($list);        
//dd(sizeof($places));
        return Response::json($list);

    }    
    
    /**
     * Gets list of places for drop down list in JSON format
     * Test url: /corpus/place/list?search_region=3&search_district[]=13
     * 
     * @return JSON response
     */
    public function birthPlaceList(Request $request)
    {
        $locale = LaravelLocalization::getCurrentLocale();

        $place_name = '%'.$request->input('q').'%';

        $region_id = $request->input('region_id');
        $district_ids = (array)$request->input('district_id');
        
        $list = [];
        $places = Place::where(function($q) use ($place_name){
                            $q->where('name_en','like', $place_name)
                              ->orWhere('name_ru','like', $place_name);
                         });
                         
        if ($region_id || sizeof($district_ids)) { 
            $places->whereIn('id', function ($q) use ($region_id, $district_ids) {
                $q->select('birth_place_id')->from('informants')
                  ->whereIn('birth_place_id', function ($q2) use ($region_id, $district_ids) {
                    $q2->select('id')->from('places');
                    if (sizeof($district_ids)) {                 
                        $q2 -> whereIn('district_id',$district_ids);
                    }
                    if ($region_id) {                 
                        $q2 -> whereIn('district_id', function ($q3) use ($region_id) {
                            $q3->select('id')->from('districts')
                              ->whereRegionId($region_id);
                        });
                    }                      
                  });
            });
        }                
                         
        $places = $places->orderBy('name_'.$locale)->get();//->pluck('name_'.$locale, 'id')->toArray();
//        return Response::json($places);
                         
        foreach ($places as $place) {
            $list[]=['id'  => $place->id, 
                     'text'=> $place->name];
        }  
//dd($list);        
//dd(sizeof($places));
        return Response::json($list);

    }    
}
