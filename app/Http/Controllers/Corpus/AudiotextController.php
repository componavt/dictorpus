<?php

namespace App\Http\Controllers\Corpus;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use Redirect;
use Response;
use LaravelLocalization;

use App\Library\Map;

use App\Models\Corpus\Audiotext;
use App\Models\Corpus\Place;

use App\Models\Dict\Lang;

class AudiotextController extends Controller
{
    public function __construct(Request $request)
    {
        // permission= corpus.edit, redirect failed users to /corpus/text/, authorized actions list:
        $this->middleware('auth:corpus.edit,/corpus/text/', 
                         ['except' => ['onMap'/*, 'upload'*/]]);
    }
    
    public function chooseFiles(int $text_id)
    {
        $audio_values = Audiotext::getAllFiles($text_id,true);
        return view('corpus.audiotext._choose_files',
                compact('audio_values'));
    }
    
    public function addFiles(int $text_id, Request $request) {
        $filenames = $request->input('filenames');
        foreach ($filenames as $filename) {
            Audiotext::create(['filename'=>$filename, 'text_id'=>$text_id]);            
        }
        return Redirect::to('/corpus/audiotext/show_files/'.$text_id);
    }
    
    public function showFiles(int $text_id, Request $request) {        
        $audiotexts = Audiotext::whereTextId($text_id)->get();
        $action = 'edit';
        return view('corpus.audiotext._show_files',
                compact('action', 'audiotexts'));
    }
    
    public function removeFile(int $text_id, int $audiotext_id) {
        $audiotext = Audiotext::find($audiotext_id);
        $audiotext->delete();
        return Redirect::to('/corpus/audiotext/show_files/'.$text_id);
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
    
    public function onMap(Request $request) {
        $legend = Lang::legendForMap();
        $places = Audiotext::onMap();

        return view('corpus.audiotext.map', 
                compact('legend', 'places')); 
    }
    
    public function upload(Request $request)
    {
        return ['success'=>true,'message'=>'Successfully uploaded'];
    	$validator = Validator::make($request->all(), [
                		'file' => 'required|mimes:application/octet-stream,audio/mpeg,mpga,mp3,wav',
            		]);

        if ($validator->fails()) {
            return response()
                ->json([
                    'success' => false,
                    'error' =>  $validation->errors()->first()
                ]);
        }
//        return ['success'=>true,'message'=>'Successfully uploaded'];

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $fileName = $file->getClientOriginalName();
            $request->file('file')->move(public_path(Audiotext::DIR), $fileName);
        }

        return ['success'=>true,'message'=>'Successfully uploaded'/*, 'filename'=>$filename*/];
    }
    
}
