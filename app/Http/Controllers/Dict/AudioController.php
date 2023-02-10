<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use Redirect;
use File;

use App\Library\Str;

use App\Models\User;

use App\Models\Corpus\Informant;

use App\Models\Dict\Audio;
use App\Models\Dict\Label;
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
    
    public function update() {
        return Redirect::to('/dict/audio/'.($this->args_by_get));
    }
    
    public function store() {
        return Redirect::to('/dict/audio/'.($this->args_by_get));
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
    
    public function editRecordList($informant_id) {
        $informant = Informant::find($informant_id);
        if (!$informant || !sizeof($informant->dialects)) {
            return Redirect::to('/corpus/informant/');            
        }
        $dialect_values = [NULL=>''] + $informant->dialects->pluck('name','id')->toArray();
        $url_args = $this->url_args;
        $audios = Audio::whereInformantId($informant_id)->take(1)->get();

        return view('dict.audio.list.index',
                compact('audios', 'dialect_values', 'informant', 'url_args'));        
        
    }
    
    public function createRecordList() {
        $informant = Informant::find($this->url_args['search_informant']);
        $dialect = $this->url_args['search_dialect'];
        $lang_id = isset($this->url_args['search_lang'][0]) ? $this->url_args['search_lang'][0] : null;
        if (!$lang_id) {
            return null;
        }
        $lemmas = Lemma::whereLangId($lang_id);
        
        if ($dialect) {
            $lemmas->whereIn('id', function ($q) use ($dialect) {
                $q->select('lemma_id')->from('meanings')
                  ->whereIn('id', function ($q2) use ($dialect) {
                      $q2->select('meaning_id')->from('dialect_meaning')
                         ->whereIn('dialect_id', (array)$dialect);
                  });
            });
        }
        
        $lemmas = $lemmas->orderBy('lemma')->get();
//dd($lemmas);        
    }
}
