<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Response;

use App\Models\Corpus\Genre;
use App\Models\Corpus\Motype;

class MotypeController extends Controller
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
        $this->middleware('auth:corpus.edit,/corpus/motype/', ['only' => ['create','store','edit','update','destroy']]);
        $this->url_args = Motype::urlArgs($request);  
        
        $this->args_by_get = search_values_by_URL($this->url_args);
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
        
        $motypes = Motype::search($url_args);
        $numAll = $motypes->count();

        $motypes = $motypes->get();
        $genre_values = [NULL => ''] + Genre::getList();
//dd($motype_by_corpus);        
        return view('corpus.motype.index', 
                    compact('genre_values', 'motypes', 'numAll', 
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
        $default_genre = 60;
        return view('corpus.motype.create', 
                compact('default_genre', 'genre_values', 'args_by_get', 'url_args'));
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
        
        $motype = Motype::create($request->all());
        
        return Redirect::to('/corpus/motype/'.($this->args_by_get))
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
        return Redirect::to('/corpus/motype/'.($this->args_by_get));
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
        
        $motype = Motype::find($id); 
        $corpus_values = Corpus::getList();
        $motype_values = [NULL => ''] + Motype::getNumeredList();        
        
        return view('corpus.motype.edit', 
                compact('corpus_values', 'motype', 'motype_values', 
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
        
        $motype = Motype::find($id);
        $motype->fill($request->all())->save();
        
//        return Redirect::to('/corpus/motype/?search_id='.$motype->id)
        return Redirect::to('/corpus/motype/'.($this->args_by_get))
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
                $motype = Motype::find($id);
                if($motype){
                    $motype_name = $motype->name;
                    if ($motype->texts()->count()>0) {
                        $error = true;
                        $result['error_message'] = \Lang::get('corpus.motype_has_text', ['name'=>$motype_name]);                        
                    } else {
                        $motype->delete();
                        $result['message'] = \Lang::get('corpus.motype_removed', ['name'=>$motype_name]);
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
                return Redirect::to('/corpus/motype/'.($this->args_by_get))
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/motype/'.($this->args_by_get))
                  ->withSuccess($result['message']);
        }
    }
    
    /**
     * Gets list of places for drop down list in JSON format
     * Test url: /corpus/motype/list?lang_id[]=1
     * 
     * @return JSON response
     */
    public function motypeList(Request $request)
    {
        $motype_name = '%'.$request->input('q').'%';
        $corpus_ids = (array)$request->input('corpus_id');

        $list = [];
        $motypes = Motype::where(function($q) use ($motype_name){
                            $q->where('name_en','like', $motype_name)
                              ->orWhere('name_ru','like', $motype_name);
                         });
        if (sizeof($corpus_ids)) {                 
            $motypes = $motypes -> whereIn('corpus_id',$corpus_ids);
        }
        
        $motypes = $motypes->orderBy('sequence_number')->get();
                         
        foreach ($motypes as $motype) {
            $list[]=['id'  => $motype->id, 
                     'text'=> $motype->numberInList(). '. '. $motype->name];
        }  
//dd($list);        
//dd(sizeof($places));
        return Response::json($list);

    }
}
