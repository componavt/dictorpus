<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Models\Dict\Example;

class ExampleController extends Controller
{
    public function __construct(Request $request)
    {
        // permission= dict.edit, redirect failed users to /dict/lemma/, authorized actions list:
        $this->middleware('auth:dict.edit,/dict/lemma/'/*, 
                          ['only' => ['create'//,'store','edit','update','destroy',
                              ]]*/);
    }
    
    public function create(int $meaning_id) {
        return view('dict.example._create', compact('meaning_id'));        
    }
    
    public function store(int $meaning_id, Request $request) {
        $example_obj = Example::store($meaning_id, $request->all());
        if ($example_obj) {
            return view('dict.example.view', compact('example_obj'));     
        }
    }
    
    public function edit(int $example_id) {
        $example = Example::find($example_id);
        if (!$example) {
            return;
        }
        return view('dict.example._edit', compact('example'));
    }
    
    public function update(int $example_id, Request $request) {
        $example_obj = Example::find($example_id);
        if (!$example_obj) {
            return ' ';
        }
        $example = $request->input('example');
        $example_ru = $request->input('example_ru');

        if ($example) {
            $example_obj->example = $example;
            $example_obj->example_ru = $example_ru;
            $example_obj->save();
            return view('dict.example._view', compact('example_obj'));     
        } else {
            $example_obj->delete();
            return ' ';
        }
    }
    
}
