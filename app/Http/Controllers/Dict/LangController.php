<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Models\Dict\Lang;

class LangController extends Controller
{
    /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:ref.edit,/dict/lang/', ['only' => ['create','store','edit','update','destroy']]);
    }
    
   /**
     * Show the list of languages.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $langs = Lang::orderBy('name_en')->orderBy('sequence_number')->get();
        $total_count = Lang::count();

        return view('dict.lang.index')->with(array('languages' => $langs,
                                                   'total_count' => $total_count));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dict.lang.create');
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
            'name_en'  => 'required|max:64',
            'name_ru'  => 'required|max:64',
            'code'  => 'required|max:20',
        ]);
        
        $lang = Lang::create($request->all());
        
        return Redirect::to('/dict/lang/')
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
        return Redirect::to('/dict/lang/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $lang = Lang::find($id); 
        
        return view('dict.lang.edit')
                  ->with(['lang' => $lang]);
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
            'name_en'  => 'required|max:64',
            'name_ru'  => 'required|max:64',
            'code'  => 'required|max:20',
        ]);
        
        $lang = Lang::find($id);
//dd($request);       
        $lang->fill($request->all())->save();
        
        return Redirect::to('/dict/lang/')
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
        return Redirect::to('/dict/lang/');
    }
}
