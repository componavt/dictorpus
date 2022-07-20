<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use Redirect;
use File;

use App\Library\Str;

use App\Models\User;

use App\Models\Dict\Audio;
use App\Models\Dict\Lemma;

class AudioController extends Controller
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
        // permission= dict.edit, redirect failed users to /dict/lemma/, authorized actions list:
        $this->middleware('auth:dict.edit,/audio', 
                          ['except'=>['index']]);
        
        $this->url_args = Str::urlArgs($request) + 
            [
//                'search_pos'      => (int)$request->input('search_pos'),
//             'search_status'   => (int)$request->input('search_status'),
//                'search_lemma'    => $request->input('search_lemma'),
            ];
        
        $this->args_by_get = Str::searchValuesByURL($this->url_args);
    }
    
    public function index() {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        $audios = Audio::orderBy('created_at', 'desc')->get();
        return view('dict.audio.index',
                compact('audios', 
                        'args_by_get', 'url_args'));
    }
    
    public function recordGroup() {
        $user = User::currentUser();
        $informant_id = $user ? $user->informant_id : NULL;
        $lang_id=5; // livvic
//        $label_id = 4; // for school dictionary
        $label_id = 3; // for multimedia dictionary
        $lemmas = Lemma::whereLangId($lang_id)
            ->whereIn('id', function ($q) use ($label_id) {
                $q->select('lemma_id')->from('label_lemma')
                  ->whereLabelId($label_id);
            })->whereNotIn('id', function ($q) {
                $q->select('lemma_id')->from('audio_lemma');
            })
            ->groupBy('lemma')
            ->orderByRaw('lower(lemma)')
            ->take(100)->get();
        return view('dict.audio.record_group',
                compact('lemmas', 'informant_id'));        
    }
    
    public function upload(Request $request) {
/*    	$this->validate($request, [
                    'audio' => 'required|mimes:application/octet-stream,audio/mpeg,mpga,mp3,wav',
               ]);

        if ($request->hasFile('audio')) {*/
            $lemma_id = (int)$request->input('id');
            $lemma = Lemma::find($lemma_id);
            if ($lemma) {
                $informant_id = $request->input('informant_id');// ? (int)$request->input('informant_id') : NULL;
                $fileName = $lemma_id.'_'.$informant_id.'.wav';
                $request->file('audio')->move(Storage::disk('audios')->getAdapter()->getPathPrefix(), $fileName);

                Audio::addAudioFileToLemmas($fileName, $lemma_id, $informant_id);
            }
//        }
        return view('dict.audio.view_audios',
                compact('lemma'));        
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
//        $status_code = 200;
        $result =[];
        if($id != "" && $id > 0) {
            try{
                $audio = Audio::find($id);
                if($audio){
                    $result['message'] = \Lang::get('dict.audio_removed', ['name'=>$audio->filename]);
                    $audio->lemmas()->detach();
                    Storage::disk('audios')->delete($audio->filename);
                    $audio->delete();
                } else{
                    $error = true;
                    $result['error_message'] = \Lang::get('messages.record_not_exists');
                }
          }catch(\Exception $ex){
                    $error = true;
//                    $status_code = $ex->getCode();
                    $result['error_code'] = $ex->getCode();
                    $result['error_message'] = $ex->getMessage();
                }
        } else{
            $error =true;
//            $status_code = 400;
            $result['message']='Request data is empty';
        }
        
        if ($error) {
            return Redirect::to('/dict/audio/'.($this->args_by_get))
                           ->withErrors($result['error_message']);
        }
        return Redirect::to('/dict/audio/'.($this->args_by_get))
              ->withSuccess($result['message']);
    }
    
}
