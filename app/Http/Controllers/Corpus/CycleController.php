<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Redirect;
use Response;

use App\Library\Str;

use App\Models\Corpus\Corpus;
use App\Models\Corpus\Cycle;
use App\Models\Corpus\Genre;

class CycleController extends Controller
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
        // permission= corpus.edit, redirect failed users to /corpus/text/, authorized actions list:
        $this->middleware('auth:corpus.edit,/corpus/cycle/', ['except' => ['index','cycleList']]);
        $this->url_args = Cycle::urlArgs($request);  
        
        $this->args_by_get = Str::searchValuesByURL($this->url_args);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $cycles = Cycle::search($url_args);
        $numAll = $cycles->count();

        $cycles = $cycles->paginate($this->url_args['limit_num']);
        $corpus_values = Corpus::getList();
        $genre_values = Genre::getList();

        return view('corpus.cycle.index', 
                    compact('corpus_values', 'genre_values', 'cycles', 'numAll', 
                            'args_by_get', 'url_args'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $genre_id = Genre::LEGEND_ID;
        $genre_values = Genre::getNumeredList();
        return view('corpus.cycle.create', 
                compact('genre_id', 'genre_values', 'args_by_get', 'url_args'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $this->validate($request, [
            'name_en'  => 'max:150',
            'name_ru'  => 'required|max:150',
        ]);
        
        $cycle = Cycle::create($request->all());
        
        return Redirect::to('/corpus/cycle/'.($this->args_by_get))
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
        return Redirect::to('/corpus/cycle/'.($this->args_by_get));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $cycle = Cycle::find($id); 
        $genre_id = Genre::LEGEND_ID;
        $genre_values = Genre::getNumeredList();
        
        return view('corpus.cycle.edit', 
                compact('default_genre', 'genre_values', 'cycle',
                        'args_by_get', 'url_args'));
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
        
        $cycle = Cycle::find($id);
        $cycle->fill($request->all())->save();
        
//        return Redirect::to('/corpus/cycle/?search_id='.$cycle->id)
        return Redirect::to('/corpus/cycle/'.($this->args_by_get))
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
                $cycle = Cycle::find($id);
                if($cycle){
                    $cycle_name = $cycle->name;
                    if ($cycle->texts()->count()>0) {
                        $error = true;
                        $result['error_message'] = \Lang::get('corpus.cycle_has_text', ['name'=>$cycle_name]);                        
                    } else {
                        $cycle->delete();
                        $result['message'] = \Lang::get('corpus.cycle_removed', ['name'=>$cycle_name]);
                    }
                }
                else{
                    $error = true;
                    $result['error_message'] = \Lang::get('messages.record_not_exists');
                }
          } catch(\Exception $ex){
                    $error = true;
                    $status_code = $ex->getCode();
                    $result['error_code'] = $ex->getCode();
                    $result['error_message'] = $ex->getMessage();
                }
        } else{
            $error =true;
            $status_code = 400;
            $result['message']='Request data is empty';
        }
        
        if ($error) {
                return Redirect::to('/corpus/cycle/'.($this->args_by_get))
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/cycle/'.($this->args_by_get))
                  ->withSuccess($result['message']);
        }
    }
    
    /**
     * Gets list of places for drop down list in JSON format
     * Test url: /corpus/cycle/list?lang_id[]=1
     * 
     * @return JSON response
     */
    public function cycleList(Request $request)
    {
        $cycle_name = '%'.$request->input('q').'%';
        
        $genre_ids = (array)$request->input('genre_id');
        foreach (Genre::whereIn('parent_id', $genre_ids)->get() as $g) {
            $genre_ids[] = $g->id;
        }

        $list = [];
        $cycles = Cycle::where(function($q) use ($cycle_name){
                            $q->where('name_en','like', $cycle_name)
                              ->orWhere('name_ru','like', $cycle_name);
                         });
        if (sizeof($genre_ids)) {                 
            $cycles = $cycles -> whereIn('genre_id',$genre_ids);
        }
        
        $cycles = $cycles->orderBy('sequence_number')->get();
                         
        foreach ($cycles as $cycle) {
            $list[]=['id'  => $cycle->id, 
                     'text'=> $cycle->name];
        }  
//dd($list);        
//dd(sizeof($places));
        return Response::json($list);

    }
}
