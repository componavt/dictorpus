<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use LaravelLocalization;
use Response;

use App\Models\Dict\Lang;
use App\Models\Corpus\District;
use App\Models\Corpus\Informant;
use App\Models\Corpus\Place;
use App\Models\Corpus\Region;

class InformantController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth:corpus.edit,/corpus/informant/', ['only' => ['create','store','edit','update','destroy', 'audio']]);
        
        $this->url_args = Informant::urlArgs($request);          
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
        
        $informants = Informant::search($url_args);

        $numAll = $informants->count();

        $informants = $informants->paginate($url_args['limit_num']);
        
        $region_values = [NULL => ''] + Region::getList();
        $district_values = District::getList();
        $place_values = Place::getList(false);
        
        return view('corpus.informant.index',
                    compact('district_values', 'informants','numAll',
                            'place_values', 'region_values',
                            'args_by_get', 'url_args'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $place_values = [NULL => ''] + Place::getList();
        $region_values = Region::getList();
        $district_values = District::getList();
        
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        return view('corpus.informant.create',
                  compact('place_values', 'region_values', 'district_values', 
                          'args_by_get', 'url_args'));
    }

    public function validateRequest(Request $request) {
        $this->validate($request, [
            'name_en'  => 'max:150',
            'name_ru'  => 'required|max:150',
            'birth_place_id' => 'numeric',
//            'birth_date' => 'numeric',
        ]);
        
        if (!$request->birth_date) {
            $request->birth_date = NULL;
        }

        if (!$request['birth_place_id']) {
            $request['birth_place_id'] = NULL;
        }
        return $request->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data=$this->validateRequest($request);       
        $informant = Informant::create($data);        
        return Redirect::to('/corpus/informant/?search_id='.$informant->id)
            ->withSuccess(\Lang::get('messages.created_success'));        
    }

    public function simpleStore(Request $request)
    {
        $data=$this->validateRequest($request);       
        $informant = Informant::create($data);        
        $lang_id=Lang::getIDByCode(LaravelLocalization::getCurrentLocale());
        return Response::json([$informant->id, $informant->informantString($lang_id)]);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Redirect::to('/corpus/informant/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $informant = Informant::find($id); 
        $place_values = [NULL => ''] + Place::getList();
        $region_values = Region::getList();
        $district_values = District::getList();
        
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        return view('corpus.informant.edit',
                  compact('informant', 'place_values', 'region_values', 
                          'district_values', 'args_by_get', 'url_args'));
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
        $data=$this->validateRequest($request);       
        $informant = Informant::find($id);
        $informant->fill($data)->save();
        
        return Redirect::to('/corpus/informant/?search_id='.$informant->id)
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
                $informant = Informant::find($id);
                if($informant){
                    $informant_name = $informant->name;
                    $informant->delete();
                    $result['message'] = \Lang::get('corpus.informant_removed', ['name'=>$informant_name]);
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
                return Redirect::to('/corpus/informant/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/informant/')
                  ->withSuccess($result['message']);
        }
    }
    
    public function audio($id)
    {
        $informant = Informant::find($id); 
        if (!$informant || !sizeof($informant->dialects)) {
            return Redirect::to('/corpus/informant/');            
        }
        
        $dialect_values = [NULL=>''] + $informant->dialects->pluck('name','id')->toArray();        
        $url_args = $this->url_args;

        return view('corpus.informant.audio',
                compact('dialect_values', 'informant', 'url_args'));        
    }
    
    public function getLang($id)
    {
        $informant = Informant::find($id); 
        return $informant->birth_place && isset($informant->birth_place->dialects[0]) 
                ? $informant->birth_place->dialects[0]->lang_id : null;
    }
}
