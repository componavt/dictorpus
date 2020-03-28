<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use DB;
use LaravelLocalization;

use App\Library\Str;

use App\Models\Dict\Lang;
use App\Models\Corpus\Informant;
use App\Models\Corpus\Place;

class InformantController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth:corpus.edit,/corpus/informant/', ['only' => ['create','store','edit','update','destroy']]);
        $this->url_args = [
                    'limit_num'       => (int)$request->input('limit_num'),
                    'page'            => (int)$request->input('page'),
                    'search_birth'   => (int)$request->input('search_birth'),
                    'search_birth_place'  => $request->input('search_birth_place'),
                    'search_id'     => (int)$request->input('search_id'),
                    'search_name'    => $request->input('search_name'),
                ];
        
        $this->url_args['page'] = $this->url_args['page'] ? $this->url_args['page'] : 1;
        
        if ($this->url_args['limit_num']<=0) {
            $this->url_args['limit_num'] = 10;
        } elseif ($this->url_args['limit_num']>1000) {
            $this->url_args['limit_num'] = 1000;
        }   
       
        $this->url_args['search_birth'] = $this->url_args['search_birth'] ? $this->url_args['search_birth'] : NULL;
        
        $this->url_args['search_id'] = $this->url_args['search_id'] ? $this->url_args['search_id'] : NULL;
        
        $this->args_by_get = Str::searchValuesByURL($this->url_args);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $informants = Informant::orderBy('name_'.$locale);

        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $informant_name = $url_args['search_name'];
        if ($informant_name) {
            $informants = $informants->where(function($q) use ($informant_name){
                            $q->where('name_en','like', $informant_name)
                              ->orWhere('name_ru','like', $informant_name);
                    });
        } 

        if ($url_args['search_birth_place']) {
            $informants = $informants->where('birth_place_id',$url_args['search_birth_place']);
        } 

        if ($url_args['search_birth']) {
            $informants = $informants->where('birth_date',$url_args['search_birth']);
        } 

        if ($url_args['search_id']) {
            $informants = $informants->where('id',$url_args['search_id']);
        } 

        $numAll = $informants->count();

        $informants = $informants->paginate($url_args['limit_num']);
        
        $place_values = Place::getListWithQuantity('informants');
        
        return view('corpus.informant.index',
                    compact('informants','numAll','place_values',
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
        
        return view('corpus.informant.create')
                  ->with(['place_values' => $place_values]);
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
            'birth_place_id' => 'numeric',
            'birth_date' => 'numeric',
        ]);
        
        if (!$request->birth_date) {
            $request->birth_date = NULL;
        }

        if (!$request['birth_place_id']) {
            $request['birth_place_id'] = NULL;
        }

        $informant = Informant::create($request->all());
        
        return Redirect::to('/corpus/informant/?search_id='.$informant->id)
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
        
        return view('corpus.informant.edit')
                  ->with(['place_values' => $place_values,
                          'informant' => $informant]);
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
            'birth_place_id' => 'numeric',
            'birth_date' => 'numeric',
        ]);
//dd($request);        
        if (!$request['birth_date']) {
            $request['birth_date'] = NULL;
        }

        if (!$request['birth_place_id']) {
            $request['birth_place_id'] = NULL;
        }

        $informant = Informant::find($id);
        $informant->fill($request->all())->save();
        
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
/*    
    public function tempInsertVepsianInformant()
    {
        $veps_informants = DB::connection('vepsian')
                            ->table('informant')
                            ->orderBy('id')
                            //->take(1)
                            ->get();
 
        DB::connection('mysql')->table('informants')->delete();
       
        foreach ($veps_informants as $veps_informant):
            $informant = new Informant;
            $informant->id = $veps_informant->id;
            $informant->birth_place_id = $veps_informant->birth_place_id;
            $informant->birth_date = $veps_informant->birth_date;
            $informant->name_ru = $veps_informant->name;
            $informant->save();            
        endforeach;
     }
 * 
 */
}
