<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use DB;
use LaravelLocalization;

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
    public function __construct()
    {
        $this->middleware('auth:corpus.edit,/corpus/place/', ['only' => ['create','store','edit','update','destroy']]);
    }

    /**
     * Show the list of places.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $place_name = $request->input('place_name');
        $limit_num = (int)$request->input('limit_num');
        $region_id = (int)$request->input('region_id');
        $district_id = (int)$request->input('district_id');
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
        $places = Place::orderBy('name_'.$locale);

        if ($place_name) {
            $places = $places->where(function($q) use ($place_name){
                            $q->whereIn('id',function($query) use ($place_name){
                                $query->select('place_id')
                                ->from(with(new PlaceName)->getTable())
                                ->where('name','like', $place_name);
                            })->orWhere('name_en','like', $place_name)
                              ->orWhere('name_ru','like', $place_name);
                    });
        } 

        if ($region_id) {
            $places = $places->where('region_id',$region_id);
        } 

        if ($district_id) {
            $places = $places->where('region_id',$district_id);
        } 

        if ($search_id) {
            $places = $places->where('id',$search_id);
        } 

        $numAll = $places->count();

        $places = $places->paginate($limit_num);
        
        $region_values = Region::getListWithQuantity('places');
        $district_values = District::getListWithQuantity('places');

        return view('corpus.place.index')
                    ->with(['places' => $places,
                            'limit_num' => $limit_num,
                            'place_name' => $place_name,
                            'region_id'=>$region_id,
                            'district_id'=>$district_id,
                            'search_id'=>$search_id,
                            'page'=>$page,
                            'region_values' => $region_values,
                            'district_values' => $district_values,
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
        $district_values = [NULL => ''] + District::getList();
        $lang_values = Lang::getList([Lang::getIDByCode('en'), 
                                      Lang::getIDByCode('ru')]);
        
        return view('corpus.place.create')
                  ->with(['region_values' => $region_values,
                          'district_values' => $district_values,
                          'lang_values' => $lang_values,
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
        $this->validate($request, [
            'name_en'  => 'max:150',
            'name_ru'  => 'required|max:150',
            'district_id' => 'numeric',
            'region_id' => 'numeric',
        ]);
        
        $place = Place::create($request->only('district_id','region_id','name_en','name_ru'));
        
        foreach ($request->other_names as $lang => $other_name) {
            if ($other_name) {
                $name= PlaceName::create(['place_id'=>$place->id, 
                                          'lang_id'=>$lang,
                                          'name'=>$other_name]);
            }
        }
        
        return Redirect::to('/corpus/place/?search_id='.$place->id)
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
        $lang_values = Lang::getList([Lang::getIDByCode('en'), 
                                      Lang::getIDByCode('ru')]);
        
        $other_names =[];
        foreach($place->other_names as $other_name) {
            $other_names[$other_name->lang_id] = $other_name->name;
        }
        
        return view('corpus.place.edit')
                  ->with(['region_values' => $region_values,
                          'district_values' => $district_values,
                          'lang_values' => $lang_values,
                          'other_names' => $other_names,
                          'place' => $place]);
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
            'district_id' => 'numeric',
            'region_id' => 'numeric',
        ]);
        
        $place = Place::find($id);
        $place->fill($request->only('district_id','region_id','name_en','name_ru'))->save();
        
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
        
        return Redirect::to('/corpus/place/?search_id='.$place->id)
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
                    $place->delete();
                    $result['message'] = \Lang::get('corpus.place_removed', ['name'=>$place_name]);
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
                return Redirect::to('/corpus/place/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/place/')
                  ->withSuccess($result['message']);
        }
    }
    
    /*    
    public function tempInsertVepsianPlace()
    {
        $veps_distr_places = DB::connection('vepsian')
                            ->table('place')
                            ->orderBy('id')
                            //->take(1)
                            ->get();
 
        DB::connection('mysql')->table('place_names')->delete();
        DB::connection('mysql')->statement('ALTER TABLE place_names AUTO_INCREMENT = 1');
        DB::connection('mysql')->table('places')->delete();
       
        foreach ($veps_distr_places as $veps_distr_place):
            if ($veps_distr_place->village_id != NULL) {
                $village = DB::connection('vepsian')
                             ->table('place_village')
                             ->where('id',$veps_distr_place->village_id)
                             ->first();
                $name_nu = $village->ru;
                $name_vep = $village->vep;
            } else {
                $name_nu = $name_vep = NULL;
            }

            $place = new Place;
            $place->id = $veps_distr_place->id;
            
            if ($veps_distr_place ->region_id == 2) {
                $place->region_id = 2;
            } else {
                $place->district_id = $veps_distr_place ->region_id;
                $district = District::find($veps_distr_place ->region_id);
                $place->region_id = $district -> region_id;
            }
            
            $place->name_ru = $name_nu;
            $place->save();
            
            if ($name_vep) {
                $place_name = new PlaceName;
                $place_name->place_id = $veps_distr_place->id;
                $place_name->lang_id = 1;
                $place_name->name = $name_vep;
                $place_name->save();
            }
            
        endforeach;
     }
 * 
 */

}
