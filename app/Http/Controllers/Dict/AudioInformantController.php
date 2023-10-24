<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use Redirect;

use App\Models\Corpus\Informant;
use App\Models\Dict\Audio;
use App\Models\Dict\Lemma;

class AudioInformantController extends Controller
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
        $this->middleware('auth:dict.edit,/dict/audio');
        
//        $this->url_args = Audio::urlArgs($request);        
//        $this->args_by_get = search_values_by_URL($this->url_args);
    }
    
    public function index($informant_id) {
        $informant = Informant::find($informant_id);
        if (!$informant || !sizeof($informant->dialects)) {
            return Redirect::to('/corpus/informant/');            
        }
        return view('dict.audio.list.index',
                compact('informant'));        
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($informant_id, $id) {
        $informant = Informant::find($informant_id);
        $error = false;
        $result =[];
        if($id != "" && $id > 0) {
            try{
                $audio = Audio::find($id);
                if($audio){
                    $result['message'] = \Lang::get('dict.audio_removed', ['name'=>$audio->filename]);
                    foreach ($audio->lemmas as $lemma) {
                        $informant->lemmas()->detach($lemma->id);                        
                    }
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
            return Redirect::to(route('informant.audio.voiced', $informant_id))
                           ->withErrors($result['error_message']);
        }
        return Redirect::to(route('informant.audio.voiced', $informant_id))
              ->withSuccess($result['message']);
    }
    
    public function addLemmasToList($informant_id, Request $request) {
        $informant = Informant::find($informant_id);
        if (!$informant || !sizeof($informant->dialects)) {
            return Redirect::to('/corpus/informant/');            
        }
        
        $lemmas = $request->input('checked_lemmas');
        $informant->lemmas()->attach($lemmas);

        return Redirect::to('/dict/audio/list/'.$informant->id.'/choose?search_dialect='.$this->url_args['search_dialect']);
    }
    
    public function chooseLemmasForList($informant_id) {
        $informant = Informant::find($informant_id);
        if (!$informant || !sizeof($informant->dialects)) {
            return Redirect::to('/corpus/informant/');            
        }
        
        $dialect_values = [];
        $lemmas = $informant->unvoicedLemmas();
        foreach ($informant->dialects as $dialect) {
            $count = Lemma::searchByDialects($lemmas, [$dialect->id])->count();
//dd(to_sql($count));            
            $dialect_values[$dialect->id] = $dialect->name . ($count ? " ($count)" : '');
        }

        $url_args = $this->url_args;
        
        $lemmas = $informant->unvoicedLemmas();                
        $lemmas = Lemma::searchByLemma($lemmas, $url_args['search_lemma']); 

        if ($url_args['search_dialect']) {
            $lemmas=Lemma::searchByDialects($lemmas, [$url_args['search_dialect']]);
        }
                
        $lemmas = $lemmas->groupBy('lemma')->orderBy('lemma')
                         ->take(250)
                         ->get();

        return view('dict.audio.list.choose',
                compact('dialect_values', 'informant', 'lemmas', 'url_args'));                
    }
    
    public function deleteLemmaFromList($informant_id, $lemma_id) {
        $informant = Informant::find($informant_id);
        $lemma = Lemma::find($lemma_id);
        if (!$informant || !$lemma) {
            return;            
        }
        $informant->lemmas()->detach($lemma->id);
    }
    
    public function recordList($informant_id) {
        $informant = Informant::find($informant_id);
        if (!$informant || !sizeof($informant->dialects)) {
            return Redirect::to('/corpus/informant/');            
        }

        return view('dict.audio.list.record',
                compact('informant'));        
    }
    
    public function removeFromList($informant_id, Request $request) {
        $informant = Informant::find($informant_id);
        if (!$informant || !sizeof($informant->dialects)) {
            return Redirect::to('/corpus/informant/');            
        }
        $lemmas = $request->input('checked_lemmas');
        $informant->lemmas()->detach($lemmas);
        return Redirect::to('/dict/audio/list/'.$informant->id);
    }
    
    public function voicedList($informant_id) {
        $informant = Informant::find($informant_id);
        if (!$informant || !sizeof($informant->dialects)) {
            return Redirect::to('/corpus/informant/');            
        }
        $audios = Audio::whereInformantId($informant_id)
//                ->take(1)
                ->get();
        return view('dict.audio.list.voiced',
                compact('audios', 'informant'));                
    }
    
}
