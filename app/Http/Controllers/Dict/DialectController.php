<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Corpus\Text;
use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;

class DialectController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:ref.edit,/dict/dialect/', ['only' => ['create','store','edit','update','destroy']]);
    }

    /**
     * Show the list of dialects.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $dialects = Dialect::orderBy('lang_id')->orderBy('id')->get();

        return view('dict.dialect.index')->with(array('dialects' => $dialects));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lang_values = Lang::getList();

        return view('dict.dialect.create')
                  ->with(['lang_values' => $lang_values]);
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
            'name_en'  => 'required|max:255',
            'name_ru'  => 'required|max:255',
            'code' => 'required|max:20'
        ]);
        
        $dialect = Dialect::create($request->all());
        
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
    public function edit($id)
    {
        $dialect = Dialect::find($id); 
        $lang_values = Lang::getList();
        
        return view('dict.dialect.edit')
                  ->with(['dialect' => $dialect,
                          'lang_values' => $lang_values,
                         ]);
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
            'name_en'  => 'required|max:255',
            'name_ru'  => 'required|max:255',
            'code' => 'required|max:20'
        ]);
        
        $dialect = Dialect::find($id);
        $dialect->fill($request->all())->save();
        
        return Redirect::to('/dict/dialect/')
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
}
