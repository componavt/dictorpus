<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Redirect;
use Response;

use App\Models\Corpus\Genre;
use App\Models\Corpus\Motive;
use App\Models\Corpus\Motype;

class MotiveController extends Controller
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
        $this->middleware('auth:corpus.edit,/corpus/motive/', ['only' => ['create','store','edit','update','destroy']]);
        $this->url_args = Motive::urlArgs($request);  
        
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
        
        $motives = Motive::search($url_args);
        $numAll = $motives->count();

        $motives = $motives->get();
        $motives=$motives->sortBy('full_code');
        $genre_values = [NULL => ''] + Genre::getList();
        $motype_values = Motype::getList();        
//dd($motive_by_corpus);        
        return view('corpus.motive.index', 
                    compact('genre_values', 'motives', 'motype_values', 'numAll', 
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
        
        $motype_values = Motype::getList();        
        $parent_values = [NULL=>''] + Motive::getList(1,null);        
        return view('corpus.motive.create', 
                compact('motype_values', 'parent_values', 'args_by_get', 'url_args'));
    }

    public function validateRequest(Request $request) {
        $this->validate($request, [
            'name_en'  => 'max:150',
            'name_ru'  => 'required|max:150',
        ]);
        $data = $request->all();
        if ($data['parent_id']==0) {
            $data['parent_id'] = null;
        }
        return $data;
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
        
        $motive = Motive::create(self::validateRequest($request));
        
        return Redirect::to('/corpus/motive/'.($this->args_by_get))
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
        return Redirect::to('/corpus/motive/'.($this->args_by_get));
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
        
        $motive = Motive::find($id); 
        $motype_values = Motype::getList();        
        $parent_values = [NULL=>''] + Motive::getList($motive->motype_id,null);        
        
        return view('corpus.motive.edit', 
                compact('motype_values', 'motive', 'parent_values', 'args_by_get', 'url_args'));
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
        $motive = Motive::find($id);
        $motive->fill(self::validateRequest($request))->save();
        
//        return Redirect::to('/corpus/motive/?search_id='.$motive->id)
        return Redirect::to('/corpus/motive/'.($this->args_by_get))
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
                $motive = Motive::find($id);
                if($motive){
                    $motive_name = $motive->name;
                    if ($motive->texts()->count()>0) {
                        $error = true;
                        $result['error_message'] = \Lang::get('corpus.motive_has_text', ['name'=>$motive_name]);                        
                    } else {
                        $motive->delete();
                        $result['message'] = \Lang::get('corpus.motive_removed', ['name'=>$motive_name]);
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
                return Redirect::to('/corpus/motive/'.($this->args_by_get))
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/motive/'.($this->args_by_get))
                  ->withSuccess($result['message']);
        }
    }
    
    /**
     * Gets list of places for drop down list in JSON format
     * Test url: /corpus/motive/list?lang_id[]=1
     * 
     * @return JSON response
     */
    public function motiveList(Request $request)
    {
        $motive_name = '%'.$request->input('q').'%';
        $genre_ids = (array)$request->input('genre_id');
        $motype_ids = (array)$request->input('motype_id');
        $parent_id = $request->input('parent_id');

        $list = [];
        $motives = Motive::where(function($q) use ($motive_name){
                            $q->where('name_en','like', $motive_name)
                              ->orWhere('name_ru','like', $motive_name);
                         });
        if (sizeof($genre_ids)) {                 
            $motives -> whereIn('motype_id', function ($q) use ($genre_ids) {
                $q->select('id')->from('motypes')
                  ->whereIn('genre_id', $genre_ids);
            });
        }
        
        if (sizeof($motype_ids)) {                 
            $motives -> whereIn('motype_id',$motype_ids);
        }
        
        if ($parent_id == 'NULL') {
            $motives -> whereNull('parent_id');
        } elseif ((int)$parent_id>0) {
            $motives -> whereParentId($parent_id);
        }
//dd(to_sql($motives));        
        $motives = $motives->orderBy('code')->get()->sortBy('full_code');
                         
        foreach ($motives as $motive) {
            $list[]=['id'  => $motive->id, 
                     'text'=> $motive->full_code. '. '. $motive->full_name];
        }  
//dd($list);        
//dd(sizeof($places));
        return Response::json($list);
    }
}
