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
use App\Models\Corpus\Recorder;

class RecorderController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth:corpus.edit,/corpus/recorder/', ['only' => ['create','store','edit','update','destroy']]);
        
        $this->url_args = Recorder::urlArgs($request);                   
        $this->args_by_get = Str::searchValuesByURL($this->url_args);
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
        
        $locale = LaravelLocalization::getCurrentLocale();
        $recorders = Recorder::search($url_args);

        $numAll = $recorders->count();
        $recorders = $recorders->paginate($url_args['limit_num']);
        
        return view('corpus.recorder.index',
                    compact('recorders', 'numAll','args_by_get', 'url_args'));
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

    public function validateRequest(Request $request) {
        $this->validate($request, [
            'name_en'  => 'max:150',
            'name_ru'  => 'required|max:150',
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
        $recorder = Recorder::create($request->all());
        
        return Redirect::to('/corpus/recorder/?search_id='.$recorder->id)
            ->withSuccess(\Lang::get('messages.created_success'));        
    }

    public function simpleStore(Request $request)
    {
        $this->validateRequest($request);        
        $recorder = Recorder::create($request->all());
        return $recorder->id;        
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
        
        return view('corpus.recorder.edit',compact('recorder'));
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
}
