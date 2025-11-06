<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;
use App\Http\Requests\MonumentRequest;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use LaravelLocalization;
use Response;

use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;
use App\Models\Corpus\Monument;

class MonumentController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth:corpus.edit,/corpus/monument/', 
                ['except' => ['index']]);
        
        $this->url_args = Monument::urlArgs($request);          
        $this->args_by_get = search_values_by_URL($this->url_args);
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
        
        $monuments = Monument::search($url_args);

        $numAll = $monuments->count();

        $monuments = $monuments->paginate($url_args['limit_num']);
        
        $lang_values = [NULL=>'']+Lang::getListWithQuantity('monuments', !user_dict_edit());
        $dialect_values = Dialect::getList($url_args['search_lang']);
        
        return view('corpus.monument.index',
                    compact('dialect_values', 'lang_values', 'monuments', 'numAll',
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
        
        $lang_values = Lang::getList();
        $dialect_values = [NULL=>'']+Dialect::getList($url_args['search_lang']);
        
        $action = 'create';
        
        return view('corpus.monument.modify',
                  compact('action', 'dialect_values', 'lang_values', 
                          'args_by_get', 'url_args'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MonumentRequest $request)
    {
//dd($request->all());        
        $monument = Monument::create($request->all());        
        return Redirect::to('/corpus/monument/'.$monument->id)
            ->withSuccess(\Lang::get('messages.created_success'));        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Monument $monument)
    {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        return view('corpus.monument.show',
                  compact('monument', 'args_by_get', 'url_args'));
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
        
        $monument = Monument::find($id); 
        $lang_values = Lang::getList();
        
        $dialect_values = [NULL=>'']+Dialect::getList($url_args['search_lang']);
        
        $action = 'edit';
        
        return view('corpus.monument.modify',
                  compact('action', 'dialect_values', 'lang_values', 'monument',
                          'args_by_get', 'url_args'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MonumentRequest $request, $id)
    {
        $monument = Monument::find($id);
        $monument->fill($request->all())->save();
        
        return Redirect::to('/corpus/monument/'.$monument->id)
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
                $monument = Monument::find($id);
                if($monument){
                    $monument_name = $monument->name;
                    $monument->delete();
                    $result['message'] = \Lang::get('corpus.monument_removed', ['name'=>$monument_name]);
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
                return Redirect::to('/corpus/monument/')
                               ->withErrors($result['error_message']);
        } else {
            return Redirect::to('/corpus/monument/')
                  ->withSuccess($result['message']);
        }
    }
    
}
