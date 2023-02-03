<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Response;

use App\Library\Str;

use App\Models\Corpus\Corpus;
use App\Models\Corpus\Genre;
use App\Models\Corpus\Plot;

class PlotController extends Controller
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
        $this->middleware('auth:corpus.edit,/corpus/plot/', ['except' => ['index','plotList']]);
        $this->url_args = Plot::urlArgs($request);  
        
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
        
        $plots = Plot::search($url_args)->get()->sortBy('number_in_genres');        
        $numAll = $plots->count();

        $corpus_values = Corpus::getList();
        $genre_values = Genre::getList();

        return view('corpus.plot.index', 
                    compact('corpus_values', 'genre_values', 'plots', 'numAll', 
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
        
        $genre_values = Genre::getNumeredList();
        return view('corpus.plot.create', 
                compact('genre_values', 'args_by_get', 'url_args'));
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
        
        $plot = Plot::create($request->all());
        
        return Redirect::to('/corpus/plot/'.($this->args_by_get))
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
        return Redirect::to('/corpus/plot/'.($this->args_by_get));
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
        
        $plot = Plot::find($id); 
        $genre_values = Genre::getNumeredList();
        
        return view('corpus.plot.edit', 
                compact('genre_values', 'plot',
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
        
        $plot = Plot::find($id);
        $plot->fill($request->all())->save();
        
//        return Redirect::to('/corpus/plot/?search_id='.$plot->id)
        return Redirect::to('/corpus/plot/'.($this->args_by_get))
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
                $plot = Plot::find($id);
                if($plot){
                    $plot_name = $plot->name;
                    if ($plot->texts()->count()>0) {
                        $error = true;
                        $result['error_message'] = \Lang::get('corpus.plot_has_text', ['name'=>$plot_name]);                        
                    } else {
                        $plot->delete();
                        $result['message'] = \Lang::get('corpus.plot_removed', ['name'=>$plot_name]);
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
                return Redirect::to('/corpus/plot/'.($this->args_by_get))
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/plot/'.($this->args_by_get))
                  ->withSuccess($result['message']);
        }
    }
    
    /**
     * Gets list of places for drop down list in JSON format
     * Test url: /corpus/plot/list?lang_id[]=1
     * 
     * @return JSON response
     */
    public function plotList(Request $request)
    {
        $plot_name = '%'.$request->input('q').'%';
        
        $genre_ids = (array)$request->input('genre_id');
        foreach (Genre::whereIn('parent_id', $genre_ids)->get() as $g) {
            $genre_ids[] = $g->id;
        }

        $list = [];
        $plots = Plot::where(function($q) use ($plot_name){
                            $q->where('name_en','like', $plot_name)
                              ->orWhere('name_ru','like', $plot_name);
                         });
        if (sizeof($genre_ids)) {                 
            $plots = $plots -> whereIn('genre_id',$genre_ids);
        }
        
        $plots = $plots->orderBy('sequence_number')->get();
                         
        foreach ($plots as $plot) {
            $list[]=['id'  => $plot->id, 
                     'text'=> $plot->name];
        }  
//dd($list);        
//dd(sizeof($places));
        return Response::json($list);

    }
}
