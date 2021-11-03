<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

use App\Models\Dict\PartOfSpeech;

class PartOfSpeechController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:ref.edit,/dict/part_of_speech/', ['only' => ['create','store','edit','update','destroy']]);
    }

    /**
     * Show the list of parts of speech.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $categories = PartOfSpeech::groupBy('category')->orderBy('category')->get(['category']);
        
        $pos_by_categories = [];
        
        foreach ($categories as $row) {
            $pos_by_categories[$row->category] = PartOfSpeech::getByCategory($row->category,'code');
        }

        return view('dict.pos.index', compact('pos_by_categories'));
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pos = PartOfSpeech::find($id);
        
        return view('dict.pos.show', compact('pos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pos = PartOfSpeech::find($id);
        $categories = PartOfSpeech::posCategories();
        
        return view('dict.pos.edit', compact('pos', 'categories'));
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
            'name_short_ru'  => 'max:15',
            'name_ru'  => 'required|max:255',
            'code' => 'required'
        ]);
        
        $pos = PartOfSpeech::find($id);
        $pos->fill($request->all())->save();
        
        return Redirect::to('/dict/pos/'.$id)
            ->withSuccess(\Lang::get('messages.updated_success'));        
    }

}
