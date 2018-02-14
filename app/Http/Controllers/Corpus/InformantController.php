<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use DB;
use LaravelLocalization;

use App\Models\Corpus\Informant;
use App\Models\Corpus\Place;

class InformantController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:corpus.edit,/corpus/informant/', ['only' => ['create','store','edit','update','destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $informant_name = $request->input('informant_name');
        $limit_num = (int)$request->input('limit_num');
        $birth_place_id = (int)$request->input('birth_place_id');
        $birth = (int)$request->input('birth');
        $search_id = (int)$request->input('search_id');
        $page = (int)$request->input('page');

        if (!$page) {
            $page = 1;
        }
        
        if (!$birth) {
            $birth = NULL;
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
        $informants = Informant::orderBy('name_'.$locale);

        if ($informant_name) {
            $informants = $informants->where(function($q) use ($informant_name){
                            $q->where('name_en','like', $informant_name)
                              ->orWhere('name_ru','like', $informant_name);
                    });
        } 

        if ($birth_place_id) {
            $informants = $informants->where('birth_place_id',$birth_place_id);
        } 

        if ($birth) {
            $informants = $informants->where('birth_date',$birth);
        } 

        if ($search_id) {
            $informants = $informants->where('id',$search_id);
        } 

        $numAll = $informants->count();

        $informants = $informants->paginate($limit_num);
        
        $place_values = Place::getListWithQuantity('informants');
        
        return view('corpus.informant.index')
                    ->with(['informants' => $informants,
                            'limit_num' => $limit_num,
                            'informant_name' => $informant_name,
                            'birth_place_id'=>$birth_place_id,
                            'birth'=>$birth,
                            'search_id'=>$search_id,
                            'page'=>$page,
                            'place_values' => $place_values,
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
