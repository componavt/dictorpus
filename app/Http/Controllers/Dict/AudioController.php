<?php

namespace App\Http\Controllers\Dict;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

use App\Models\Dict\Audio;
use App\Models\Dict\Lemma;

class AudioController extends Controller
{
    public function recordGroup() {
        $lang_id=5; // livvic
        $label_id = 3; // for multimedia dictionary
        $lemmas = Lemma::whereLangId($lang_id)
            ->whereIn('id', function ($q) use ($label_id) {
                $q->select('lemma_id')->from('label_lemma')
                  ->whereLabelId($label_id);
            })->whereNotIn('id', function ($q) {
                $q->select('lemma_id')->from('audio_lemma');
            })
            ->orderByRaw('lower(lemma)')
            ->take(100)->get();
        return view('dict.audio.record_group',
                compact('lemmas'));        
    }
    
    public function upload(Request $request) {
/*    	$validator = Validator::make($request->all(), [
                		'audio' => 'required|mimes:application/octet-stream,audio/mpeg,mpga,mp3,wav',
            		]);

        if (!$validator->fails() 
                && $request->hasFile('audio')) {*/
            $lemma_id = (int)$request->input('id');

            $fileName = $lemma_id.'_1.wav';
            $request->file('audio')->move(public_path(Audio::DIR), $fileName);
            
            Audio::addAudioFileToLemmas($fileName, $lemma_id);
//        }
    }
}
