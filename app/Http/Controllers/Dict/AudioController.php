<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use Redirect;

use App\Models\User;

use App\Models\Corpus\Informant;

use App\Models\Dict\Audio;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

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
        $this->middleware('auth:dict.edit,/dict/audio', 
                          ['except'=>['index']]);
        
        $this->url_args = Audio::urlArgs($request);        
        $this->args_by_get = search_values_by_URL($this->url_args);
    }
    
    public function index() {
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        $audios = Audio::search($url_args);
        $numAll = $audios->count();
        $audios = $audios->paginate($url_args['limit_num']);         
        
        $informant_values = [NULL => ''] + Audio::getSpeakerList();
        $lang_values = Lang::getList();        
        
        return view('dict.audio.index',
                compact('audios', 'informant_values', 'lang_values', 'numAll', 
                        'args_by_get', 'url_args'));
    }
    
    public function show() {
        return Redirect::to('/dict/audio/'.($this->args_by_get));
    }
    
    public function store() {
        return Redirect::to('/dict/audio/'.($this->args_by_get));
    }
    
    public function edit($id)
    {
        $audio = Audio::find($id);
        
        $informant_values = [NULL => ''] + Informant::getList();
        $lang_values = [NULL => ''] + Lang::getList();
        $lemma_values = $audio->lemmas->pluck('lemma', 'id')->toArray();
        $lang_id = $audio->informant && $audio->informant->birth_place 
                && isset($audio->informant->birth_place->dialects[0]) 
                ? $audio->informant->birth_place->dialects[0]->lang_id : null;
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;
        
        return view('dict.audio.edit',
                compact('audio', 'informant_values', 'lang_id', 'lang_values', 
                        'lemma_values', 'args_by_get', 'url_args'));
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
        $audio = Audio::find($id);
        $this->validate($request, [
            'informant_id'  => 'required|integer',
            'filename'  => 'max:100',
        ]);
        
        $audio->informant_id = (int)$request->informant_id;
        if ($audio->filename != $request->filename) {
            Storage::disk('audios')->move($audio->filename, $request->filename);
            $audio->filename = $request->filename;
        }
        $audio->save();
        $audio->lemmas()->sync((array)$request->lemmas);
        
        return Redirect::to('/dict/audio/'.($this->args_by_get))
                       ->withSuccess(\Lang::get('messages.updated_success'));
    }
    
    public function recordGroup(string $list) {
        $user = User::currentUser();
        $informant_id = $user ? $user->informant_id : NULL;
        $informant_values = Informant::getList();
        
        if (in_array($list, ['multidict-check', 'multidict-phrase', 'multidict-all', 'schooldict'])) {
            $lang_id=5; // livvic
            $label_id = $list == 'schooldict' ? 4 : 3; 
            if ($list == 'multidict-phrase') {
                $lemmas = Lemma::wherePosId(PartOfSpeech::getPhraseID())
                            ->whereIn('id', function ($q1) use ($label_id) {
                                $q1->select('phrase_id')->from('lemma_phrase')
                                   ->whereIn('lemma_id', function ($q) use ($label_id) {
                                        $q->select('lemma_id')->from('label_lemma')
                                          ->whereLabelId($label_id);
                                   });
                            });
            } else {
            $lemmas = Lemma::whereIn('id', function ($q) use ($label_id, $list) {
                                $q->select('lemma_id')->from('label_lemma')
                                  ->whereLabelId($label_id);
                                if ($list == 'multidict-check') {
                                    $q->whereStatus(1);
                                }
                           });
            }
        } elseif ($list == 'lud-mikh') {
            $lang_id=6; // ludian
            $place_id=248; // Михайловское
            $lemmas = Lemma::whereIn('id', function ($q) use ($place_id) {
                                $q->select('lemma_id')->from('lemma_place')
                                  ->wherePlaceId($place_id);
                           });
        } else {
            return;
        }
        $lemmas = $lemmas->whereLangId($lang_id)
                         ->whereNotIn('id', function ($q) {
                            $q->select('lemma_id')->from('audio_lemma');
                         })
                         ->groupBy('lemma')
                         ->orderByRaw('lower(lemma)')
//                         ->take(100)
                         ->get();
//dd($lemmas);                         
        $list_title = Audio::recordGroups[$list];
        return view('dict.audio.record_group',
                compact('lemmas', 'list_title', 'informant_id', 'informant_values'));        
    }
    
    public function upload(Request $request) {
/*    	$this->validate($request, [
                    'audio' => 'required|mimes:application/octet-stream,audio/mpeg,mpga,mp3,wav',
               ]);

        if ($request->hasFile('audio')) {*/
        $all_audios = $request->input('all_audios');
            $lemma_id = (int)$request->input('id');
            
            $lemma = Lemma::find($lemma_id);
            if ($lemma) {
                $informant_id = $request->input('informant_id');// ? (int)$request->input('informant_id') : NULL;
                $fileName = $lemma_id.'_'.$informant_id.'_'.date('Y-m-d-H-i-s').'.wav';
                $request->file('audio')->move(Storage::disk('audios')->getAdapter()->getPathPrefix(), $fileName);

                Audio::addAudioFileToLemmas($fileName, $lemma_id, $informant_id);
                if (!$all_audios) {
                    $audio = $lemma->audios()->whereInformantId($informant_id)->first();
                    return view('widgets.audio_simple', ['route' => $audio->url(), 'autoplay'=>true]).
                           '<input type="hidden" id="update-'.$lemma_id.'" value="'.$audio->updated_at.'">';            
                }
                return view('dict.audio.view_audios', compact('informant_id', 'lemma'));        
            }
//        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $informant_id=null)
    {
//        $url = Route::current()->getName() == "informant.audio.destroy" && 
//        $url = url()->previous();
        $error = false;
//        $status_code = 200;
        $result =[];
        if($id != "" && $id > 0) {
            try{
//dd($id);                
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
