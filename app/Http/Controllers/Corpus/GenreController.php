<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use DB;
use LaravelLocalization;

use App\Models\Corpus\Genre;

class GenreController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:corpus.edit,/corpus/genre/', ['only' => ['create','store','edit','update','destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $genre_name = $request->input('genre_name');
        $search_id = (int)$request->input('search_id');

        if (!$search_id) {
            $search_id = NULL;
        }
        
        $locale = LaravelLocalization::getCurrentLocale();
        $genres = Genre::orderBy('name_'.$locale);

        if ($genre_name) {
            $genres = $genres->where(function($q) use ($genre_name){
                            $q->where('name_en','like', $genre_name)
                              ->orWhere('name_ru','like', $genre_name);
                    });
        } 

        if ($search_id) {
            $genres = $genres->where('id',$search_id);
        } 

        $numAll = $genres->count();

        $genres = $genres->get();
        
        return view('corpus.genre.index')
                    ->with(['genres' => $genres,
                            'genre_name' => $genre_name,
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
        return view('corpus.genre.create');
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
        
        $genre = Genre::create($request->all());
        
        return Redirect::to('/corpus/genre/?search_id='.$genre->id)
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
        return Redirect::to('/corpus/genre/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $genre = Genre::find($id); 
        
        return view('corpus.genre.edit')
                  ->with(['genre' => $genre]);
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
        return Redirect::to('/corpus/genre/')
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
                return Redirect::to('/corpus/genre/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/genre/')
                  ->withSuccess($result['message']);
        }
    }
}
