<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use DB;
use LaravelLocalization;

use App\Models\Corpus\Recorder;

class RecorderController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:corpus.edit,/corpus/recorder/', ['only' => ['create','store','edit','update','destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $recorder_name = $request->input('recorder_name');
        $search_id = (int)$request->input('search_id');

        if (!$search_id) {
            $search_id = NULL;
        }
        
        $locale = LaravelLocalization::getCurrentLocale();
        $recorders = Recorder::orderBy('name_'.$locale);

        if ($recorder_name) {
            $recorders = $recorders->where(function($q) use ($recorder_name){
                            $q->where('name_en','like', $recorder_name)
                              ->orWhere('name_ru','like', $recorder_name);
                    });
        } 

        if ($search_id) {
            $recorders = $recorders->where('id',$search_id);
        } 

        $numAll = $recorders->count();

        $recorders = $recorders->get();
        
        return view('corpus.recorder.index')
                    ->with(['recorders' => $recorders,
                            'recorder_name' => $recorder_name,
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
        return view('corpus.recorder.create');
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
        
        $recorder = Recorder::create($request->all());
        
        return Redirect::to('/corpus/recorder/?search_id='.$recorder->id)
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
        return Redirect::to('/corpus/recorder/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $recorder = Recorder::find($id); 
        
        return view('corpus.recorder.edit')
                  ->with(['recorder' => $recorder]);
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
        
        $recorder = Recorder::find($id);
        $recorder->fill($request->all())->save();
        
        return Redirect::to('/corpus/recorder/?search_id='.$recorder->id)
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
                $recorder = Recorder::find($id);
                if($recorder){
                    $recorder_name = $recorder->name;
                    $recorder->delete();
                    $result['message'] = \Lang::get('corpus.recorder_removed', ['name'=>$recorder_name]);
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
                return Redirect::to('/corpus/recorder/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/recorder/')
                  ->withSuccess($result['message']);
        }
    }

/*    
    public function tempInsertVepsianRecorder()
    {
        $veps_recorders = DB::connection('vepsian')
                            ->table('recorder')
                            ->orderBy('id')
                            //->take(1)
                            ->get();
 
        DB::connection('mysql')->table('recorders')->delete();
       
        foreach ($veps_recorders as $veps_recorder):
            $recorder = new Recorder;
            $recorder->id = $veps_recorder->id;
            $recorder->name_ru = $veps_recorder->name;
            $recorder->save();            
        endforeach;
     }
 * 
 */
}
