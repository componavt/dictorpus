<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use Response;

use App\Models\Corpus\Audiotext;

class AudiotextController extends Controller
{
    public function __construct(Request $request)
    {
        // permission= corpus.edit, redirect failed users to /corpus/text/, authorized actions list:
        $this->middleware('auth:corpus.edit,/corpus/text/'/*, 
                         ['only' => ['chooseFiles']]*/);
    }
    
    public function chooseFiles(int $text_id)
    {
        $audio_values = Audiotext::getAllFiles($text_id);
        return view('corpus.audiotext._choose_files',
                compact('audio_values'));
    }
    
    public function addFiles(int $text_id, Request $request) {
        $filenames = $request->input('filenames');
        foreach ($filenames as $filename) {
            Audiotext::create(['filename'=>$filename, 'text_id'=>$text_id]);            
        }
        
        $audiotexts = Audiotext::whereTextId($text_id)->get();        
        return view('corpus.audiotext._show_files',
                compact('audiotexts'));
    }
    
    public function show(int $id)
    {
        $audiotext = Audiotext::find($id);
        if (!$audiotext || !Storage::disk('audiotexts')->exists($audiotext->filename)) {
            abort(404);
        }
        $type = Storage::disk('audiotexts')->mimeType($audiotext->filename);

        $file = Storage::disk('audiotexts')->get($audiotext->filename);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }    
}
