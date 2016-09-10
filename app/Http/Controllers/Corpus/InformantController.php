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
        $this->middleware('auth:corpus.edit,/corpus/informant/', ['only' => 'create','store','edit','update','destroy']);
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
        $page = (int)$request->input('page');

        if (!$page) {
            $page = 1;
        }
        
        if (!$birth) {
            $birth = NULL;
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

        $numAll = $informants->count();

        $informants = $informants->paginate($limit_num);
        
        $place_values = Place::getListWithQuantity('informants');
        
        return view('corpus.informant.index')
                    ->with(['informants' => $informants,
                            'limit_num' => $limit_num,
                            'informant_name' => $informant_name,
                            'birth_place_id'=>$birth_place_id,
                            'birth'=>$birth,
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
