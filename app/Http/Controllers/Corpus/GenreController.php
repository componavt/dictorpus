<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
//use DB;
//use LaravelLocalization;

use App\Library\Str;

use App\Models\Corpus\Corpus;
use App\Models\Corpus\Genre;

class GenreController extends Controller
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
        $this->middleware('auth:corpus.edit,/corpus/genre/', ['only' => ['create','store','edit','update','destroy']]);
        $this->url_args = Genre::urlArgs($request);  
        
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
        
        $genres = Genre::search($url_args);
        $numAll = $genres->count();

        $genres = $genres->get();
        $corpus_values = [NULL => ''] + Corpus::getListWithQuantity('genres');
        
        return view('corpus.genre.index', 
                    compact('corpus_values', 'genres', 'numAll', 
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
        
        $corpus_values = Corpus::getList();
        $genre_values = [NULL => ''] + Genre::getList();        
        return view('corpus.genre.create', 
                compact('corpus_values', 'genre_values', 'args_by_get', 'url_args'));
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
        
        $genre = Genre::create($request->all());
        
        return Redirect::to('/corpus/genre/'.($this->args_by_get))
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
        return Redirect::to('/corpus/genre/'.($this->args_by_get));
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
        
        $genre = Genre::find($id); 
        $corpus_values = Corpus::getList();
        $genre_values = [NULL => ''] + Genre::getList();        
        
        return view('corpus.genre.edit', 
                compact('corpus_values', 'genre', 'genre_values', 
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
        
        $genre = Genre::find($id);
        $genre->fill($request->all())->save();
        
//        return Redirect::to('/corpus/genre/?search_id='.$genre->id)
        return Redirect::to('/corpus/genre/'.($this->args_by_get))
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
                $genre = Genre::find($id);
                if($genre){
                    $genre_name = $genre->name;
                    if ($genre->texts()->count()>0) {
                        $error = true;
                        $result['error_message'] = \Lang::get('corpus.genre_has_text', ['name'=>$genre_name]);                        
                    } else {
                        $genre->delete();
                        $result['message'] = \Lang::get('corpus.genre_removed', ['name'=>$genre_name]);
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
                return Redirect::to('/corpus/genre/'.($this->args_by_get))
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/genre/'.($this->args_by_get))
                  ->withSuccess($result['message']);
        }
    }
}
