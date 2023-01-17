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
use App\Models\Corpus\Topic;

class TopicController extends Controller
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
        $this->middleware('auth:corpus.edit,/corpus/topic/', ['only' => ['create','store','edit','update','destroy']]);
        $this->url_args = Topic::urlArgs($request);  
        
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
        
        $topics = Topic::search($url_args);
        $numAll = $topics->count();

        $topics = $topics->paginate($this->url_args['limit_num']);
        $corpus_values = Corpus::getList();
        $genre_values = Genre::getList();
        $plot_values = Plot::getList();

        return view('corpus.topic.index', 
                    compact('corpus_values', 'genre_values', 'numAll', 
                            'plot_values', 'topics', 'args_by_get', 'url_args'));
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
        $plot_values = [NULL => ''] + Plot::getList();  
        $genre_id = 66; // руны
        
        return view('corpus.topic.create', 
                compact('genre_id', 'genre_values', 'plot_values',  
                        'args_by_get', 'url_args'));
    }

    public function validateRequest(Request $request) {
        $this->validate($request, [
            'name_en'  => 'max:150',
            'name_ru'  => 'required|max:150',
            'plot_id' => 'array' //required|
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
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $this->validateRequest($request);
        
        $topic = Topic::create($request->all());
        $topic->saveAddition($request->plot_id);
        
        return Redirect::to('/corpus/topic/'.($this->args_by_get))
            ->withSuccess(\Lang::get('messages.created_success'));        
    }

    public function simpleStore(Request $request)
    {
        $this->validateRequest($request);        
        $topic = Topic::create($request->all());
        $topic->saveAddition($request->plot_id);
        return $topic->id;        
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Redirect::to('/corpus/topic/'.($this->args_by_get));
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
        
        $topic = Topic::find($id); 
        $genre_values = Genre::getNumeredList();
        $genre_id = $topic->genre_id;
        $plot_values = [NULL => ''] + Plot::getList();  
        $plot_value = $topic->plotValue();
//dd($plot_value);        
        return view('corpus.topic.edit', 
                compact('genre_id', 'genre_values', 'plot_value', 
                        'plot_values', 'topic', 'args_by_get', 'url_args'));
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
//dd($request->all());        
        $topic = Topic::find($id);
        $topic->fill($request->all())->save();
        $topic->saveAddition($request->plot_id);
        
//        return Redirect::to('/corpus/topic/?search_id='.$topic->id)
        return Redirect::to('/corpus/topic/'.($this->args_by_get))
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
                $topic = Topic::find($id);
                if($topic){
                    $topic_name = $topic->name;
                    if ($topic->texts()->count()>0) {
                        $error = true;
                        $result['error_message'] = \Lang::get('corpus.topic_has_text', ['name'=>$topic_name]);                        
                    } else {
                        $topic->plots()->detach();
                        $topic->delete();
                        $result['message'] = \Lang::get('corpus.topic_removed', ['name'=>$topic_name]);
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
                return Redirect::to('/corpus/topic/'.($this->args_by_get))
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/topic/'.($this->args_by_get))
                  ->withSuccess($result['message']);
        }
    }
    
    /**
     * Gets list of places for drop down list in JSON format
     * Test url: /corpus/topic/list?lang_id[]=1
     * 
     * @return JSON response
     */
    public function topicList(Request $request)
    {
        $topic_name = '%'.$request->input('q').'%';
        $plot_ids = (array)$request->input('plot_id');

        $list = [];
        $topics = Topic::where(function($q) use ($topic_name){
                            $q->where('name_en','like', $topic_name)
                              ->orWhere('name_ru','like', $topic_name);
                         });
        if (sizeof($plot_ids)) {                 
            $topics = $topics->whereIn('id', function ($q) use ($plot_ids) {
                $q->select('topic_id')->from('plot_topic')
                  ->whereIn('plot_id', $plot_ids);
            });
        }
        
        $topics = $topics->orderBy('sequence_number')->get();
                         
        foreach ($topics as $topic) {
            $list[]=['id'  => $topic->id, 
                     'text'=> $topic->name];
        }  
//dd($list);        
//dd(sizeof($places));
        return Response::json($list);

    }
}
