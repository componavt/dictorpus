<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Response;
use LaravelLocalization;

use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;

class DialectController extends Controller
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
        $this->middleware('auth:ref.edit,/dict/dialect/', 
                ['only' => ['create','store','edit','update','destroy']]);
        $this->url_args = Dialect::urlArgs($request);          
        $this->args_by_get = search_values_by_URL($this->url_args);
    }

    /**
     * Show the list of dialects.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $dialects = Dialect::search($url_args);       
        $numAll = $dialects->count();        
        $dialects = $dialects->paginate($url_args['limit_num']);

        $lang_values = Lang::getListWithQuantity('dialects', true);

        return view('dict.dialect.index',
            compact('dialects', 'lang_values', 'numAll', 'args_by_get', 'url_args'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $lang_values = Lang::getList();
        $url_args = $this->url_args;

        return view('dict.dialect.create',
                  compact('lang_values', 'url_args'));
    }

    public function validateRequest(Request $request, $code_rule='') {
        $this->validate($request, [
            'name_en'  => 'required|max:255',
            'name_ru'  => 'required|max:255',
            'code' => 'required|max:20|unique:dialects'.$code_rule
            ]);
        
        $data = $request->all();
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
        Dialect::create($this->validateRequest($request));
        
        return Redirect::to('/dict/dialect/')
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
        return Redirect::to('/dict/dialect/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $dialect = Dialect::find($id); 
        if (!$dialect) {
            return Redirect::to('/dict/dialect/')
                           ->withErrors('messages.record_not_exists');
        }        
        $lang_values = Lang::getList();        
        $url_args = $this->url_args;

        return view('dict.dialect.edit',
                  compact('dialect', 'lang_values', 'url_args'));
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
        $dialect = Dialect::find($id);

        $dialect->fill($this->validateRequest($request, ',code,'.$dialect->id))->save();
        
        return Redirect::to('/dict/dialect/'.($this->args_by_get))
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
                $dialect = Dialect::find($id);
                if($dialect){
                    $dialect_name = $dialect->name;
                    // check if wordforms and gramsets exists with this dialect
                    if ($dialect->wordforms()->count() || $dialect->texts()->count()) {
                        $result['error_message'] = \Lang::get('dialect_can_not_be_removed');
                    } else {
                        $dialect->delete();
                        $result['message'] = \Lang::get('dict.dialect_removed', ['name'=>$dialect_name]);
                    }
                }
                else{
                    $error = true;
                    $result['error_message'] = \Lang::get('record_not_exists');
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
            return Redirect::to('/dict/dialect/')
                           ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/dict/dialect/')
                  ->withSuccess($result['message']);
        }
    }
    
    /**
     * Gets list of dialects for drop down list in JSON format
     * Test url: /dict/dialect/list?lang_id[]=1
     * 
     * @return JSON response
     */
    public function dialectList(Request $request)
    {

        $dialect_name = '%'.$request->input('q').'%';
        $lang_ids = (array)$request->input('lang_id');
//        $lemma_id = (int)$request->input('lemma_id');

        $list = [];
        $dialects = Dialect::where(function($q) use ($dialect_name){
                            $q->where('name_en','like', $dialect_name)
                              ->orWhere('name_ru','like', $dialect_name);
                         });
        if (sizeof($lang_ids)) {                 
            $dialects = $dialects ->whereIn('lang_id',$lang_ids);
        }
        
        $dialects = $dialects->orderBy('sequence_number')->get();
                         
        foreach ($dialects as $dialect) {
            $list[]=['id'  => $dialect->id, 
                     'text'=> $dialect->name];
        }  
//dd($list);        
//dd(sizeof($dialects));
        return Response::json($list);

/*        $lang_id = (int)$request->input('lang_id');

        $all_dialects = Dialect::getList($lang_id);

        return Response::json($all_dialects);*/
    }
    
    /*
     * test: /ru/dict/dialect/47/text_count
     */
    public function textCount($id, Request $request) {
        $without_link = $request->without_link;
        $dialect = Dialect::find($id);     
        $count = $dialect->texts()->count();
        $count = number_format($count, 0, ',', ' ');
        if (!$count || $without_link) {
            return $count;
        }
        return '<a href="'.LaravelLocalization::localizeURL('/corpus/text?search_dialect='.$dialect->id).'">'.$count.'</a>';
    }
    
    public function wordformCount($id, Request $request) {
        $without_link = $request->without_link;
        $dialect = Dialect::find($id);     
        $count = $dialect->wordforms()->count();
        $count = number_format($count, 0, ',', ' ');
        if (!$count || $without_link) {
            return $count;
        }
        return '<a href="'.LaravelLocalization::localizeURL('/dict/wordform?search_dialect='.$dialect->id).'">'.$count.'</a>';    
    }
}
