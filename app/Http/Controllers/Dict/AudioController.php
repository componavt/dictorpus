<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;

use App\Models\User;

use App\Models\Dict\Audio;
use App\Models\Dict\Lemma;

class AudioController extends Controller
{
    public function recordGroup() {
        $user = User::currentUser();
        $informant_id = $user ? $user->informant_id : NULL;
        $lang_id=5; // livvic
        $label_id = 4; // for school dictionary
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
}
