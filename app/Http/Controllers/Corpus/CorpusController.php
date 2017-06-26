<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use DB;
use LaravelLocalization;

use App\Models\Corpus\Corpus;

class CorpusController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:ref.edit,/corpus/corpus/', ['only' => ['create','store','edit','update','destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $corpus_name = $request->input('corpus_name');
        $search_id = (int)$request->input('search_id');

        if (!$search_id) {
            $search_id = NULL;
        }
        
        $locale = LaravelLocalization::getCurrentLocale();
        $corpuses = Corpus::orderBy('name_'.$locale);

        if ($corpus_name) {
            $corpuses = $corpuses->where(function($q) use ($corpus_name){
                            $q->where('name_en','like', $corpus_name)
                              ->orWhere('name_ru','like', $corpus_name);
                    });
        } 

        if ($search_id) {
            $corpuses = $corpuses->where('id',$search_id);
        } 

        $numAll = $corpuses->count();

        $corpuses = $corpuses->get();
        
        return view('corpus.corpus.index')
                    ->with(['corpuses' => $corpuses,
                            'corpus_name' => $corpus_name,
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
        return view('corpus.corpus.create');
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
            'name_en'  => 'max:255',
            'name_ru'  => 'required|max:255',
        ]);
        
        $corpus = Corpus::create($request->all());
        
        return Redirect::to('/corpus/corpus/')
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
        return Redirect::to('/corpus/corpus/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $corpus = Corpus::find($id); 
        
        return view('corpus.corpus.edit')
                  ->with(['corpus' => $corpus]);
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
            'name_en'  => 'max:255',
            'name_ru'  => 'required|max:255',
        ]);
        
        $corpus = Corpus::find($id);
        $corpus->fill($request->all())->save();
        
        return Redirect::to('/corpus/corpus/')
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
                $corpus = Corpus::find($id);
                if($corpus){
                    $corpus_name = $corpus->name;
                    if ($corpus->texts()->count()>0) {
                        $error = true;
                        $result['error_message'] = \Lang::get('corpus.corpus_has_text', ['name'=>$corpus_name]);                        
                    } else {
                        $corpus->delete();
                        $result['message'] = \Lang::get('corpus.corpus_removed', ['name'=>$corpus_name]);
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
                return Redirect::to('/corpus/corpus/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/corpus/')
                  ->withSuccess($result['message']);
        }
    }
    
    /**
     * Gets list of corpuses for drop down list in JSON format
     * Test url: /corpus/corpus/list?lang_id[]=1&lang[]=5
     * 
     * @return JSON response
     */
    public function corpusList(Request $request)
    {
        $locale = LaravelLocalization::getCurrentLocale();

        $corpus_name = '%'.$request->input('q').'%';
        $lang_ids = (array)$request->input('lang_id');

        $list = [];
        /*
        $dialects = Dialect::whereIn('lang_id',$lang_ids)
                       ->where(function($q) use ($dialect_name){
                            $q->where('name_en','like', $dialect_name)
                              ->orWhere('name_ru','like', $dialect_name);
                         })->orderBy('name_'.$locale)->get();
                         
        foreach ($dialects as $dialect) {
            $list[]=['id'  => $dialect->id, 
                     'text'=> $dialect->name];
        }  
//dd(sizeof($dialects));
         */
        return Response::json($list);
    }

    
}
