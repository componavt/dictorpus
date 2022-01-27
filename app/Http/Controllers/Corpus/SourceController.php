<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

use App\Models\Corpus\Source;

class SourceController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:corpus.edit,/corpus/genre/', ['only' => ['create','store','edit','update','destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sources = Source::orderBy('title')->get();
        print "<table border=\"1\"><tr><th>id</th><th>text_id</th><th>author</th><th>title</th><th>pages</th><th>ieeh_archive_number1</th><th>ieeh_archive_number2</th><th>comment</th></tr>";    
        foreach ($sources as $source) {
//dd($source->id, $source->texts);            
            print "<tr><td>".$source->id."</td><td>".$source->texts->implode('id', ', ')."</td><td>".$source->author."</td><td>".$source->title."</td><td>".$source->pages."</td><td>".$source->ieeh_archive_number1."</td><td>".$source->ieeh_archive_number2."</td><td>".$source->comment."</td></tr>";
        }
        print "</table>";        
    }
    
}
