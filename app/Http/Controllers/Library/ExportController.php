<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Storage;
use Carbon\Carbon;

use App\Models\Corpus\Text;
use App\Models\Dict\Dialect;
use App\Models\Dict\Gram;
use App\Models\Dict\GramCategory;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

class ExportController extends Controller
{
    public function __construct(Request $request)
    {
        // permission= dict.edit, redirect failed users to /dict/lemma/, authorized actions list:
        $this->middleware('auth:admin,/');
    }
    /*
     * annotation for CONLL
     */
    public function exportAnnotationConll() {
        $filename = 'export/conll/annotation.txt';
        
        Storage::disk('public')->put($filename, "# Parts of speech");
        $parts_of_speech = PartOfSpeech::all()->sortBy('name_en');
        foreach ($parts_of_speech as $pos) {
            Storage::disk('public')->append($filename, $pos->name_en. "\t". $pos->code);            
        }
        
        Storage::disk('public')->append($filename, "\n# Lemma features");
        $lemma_feature = new LemmaFeature;
        foreach ($lemma_feature->feas_conll_codes as $name=>$info) {
            $named_keys = [];
            if (preg_match("/^(.+)_id$/", $name, $regs) && is_array(trans('dict.'.$regs[1].'s'))) {
                $named_keys = trans('dict.'.$regs[1].'s');
//dd($named_keys);                
            }
            foreach ($info as $key=>$code) {
                Storage::disk('public')->append($filename, "$name=".(isset($named_keys[$key]) ? $named_keys[$key] : $key)."\t$code");
            }
        }
        
        Storage::disk('public')->append($filename, "\n# Grammatical attributes");
        $gram_categories = GramCategory::all()->sortBy('sequence_number');
        foreach ($gram_categories as $gram_category) {
            $grams = Gram::where('gram_category_id',$gram_category->id)->orderBy('sequence_number')->get();
            foreach ($grams as $gram) {
                Storage::disk('public')->append($filename, $gram_category->name_en. '='. $gram->name_en. "\t". $gram->conll);            
            }
        }
        
        print  '<p><a href="'.Storage::url($filename).'">annotation</a>';            
    }

    /*
     * vepkar-20190129-vep
     */
    public function exportLemmasToUniMorph() {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        $date = Carbon::now();
        $date_now = $date->toDateString();
        
//        foreach ([4, 5, 6, 1] as $lang_id) {
            $lang_id = 6;
            $lang = Lang::find($lang_id);
            $dialects = Dialect::where('lang_id',$lang_id)->get();
            foreach ($dialects as $dialect) {
                $filename = 'export/unimorph/vepkar-'.$date_now.'-'.$dialect->code.'.txt';
                $lemmas = Lemma::where('lang_id',$lang_id)
    //                    ->where('id',1416)
    //                    ->take(100)
                        ->orderBy('lemma')
                        ->get();
                $count = 0;
                foreach ($lemmas as $lemma) {
                    $line = $lemma->toUniMorph($dialect->id);
                    if ($line) {
                        $count++;
                        if ($count==1) {
                            Storage::disk('public')->put($filename, "# ".$lang->name_en.': '.$dialect->name_en);                            
                        }
                        Storage::disk('public')->append($filename, $line);
                    }
                }
                print  '<p><a href="'.Storage::url($filename).'">'.$dialect->name_en.'</a>';            
            }
//        }      
    }

    /*
     * vepkar-20190129-vep
     */
    public function exportLemmasWithPOS() {
        $date = Carbon::now();
        $date_now = $date->toDateString();
        
        $lang_id = 1;
        $lang = Lang::find($lang_id);
        $filename = 'export/vepkar-'.$date_now.'-lemma-pos-'.$lang->code.'.txt';
        $lemmas = Lemma::where('lang_id',$lang_id)
//                    ->where('id',1416)
//                    ->take(100)
                ->orderBy('lemma')
                ->get();
        $count=0;
        foreach ($lemmas as $lemma) {
            $line = $lemma->lemma.'|'.($lemma->pos ? $lemma->pos->code : 'Unknoun POS');
            if ($count==0) {
                Storage::disk('public')->put($filename, $line);                            
            } else {
                Storage::disk('public')->append($filename, $line);
            }
            $count++;
        }
        print  '<p><a href="'.Storage::url($filename).'">Lemmas with POS</a>';            
    }

    /*
     * vepkar-20190129-vep
     */
    public function exportTextsToCONLL() {//Request $request
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
//dd(ini_get('memory_limit'));
        $date = Carbon::now();
        $date_now = $date->toDateString();
        foreach ([4, 5, 6, 1] as $lang_id) {
//            $lang_id = 6;
            $lang = Lang::find($lang_id);
            $filename = 'export/conll/vepkar-'.$date_now.'-'.$lang->code.'.txt';
            Storage::disk('public')->put($filename, "# ".$lang->name);
            $texts = Text::where('lang_id',$lang_id)
//                    ->where('id',1416)
                    ->get();
            foreach ($texts as $text) {
                Storage::disk('public')->append($filename, $text->toCONLL());
            }
            print  '<p><a href="'.Storage::url($filename).'">'.$lang->name.'</a>';            
        }
    }

    /*
     * export for word2vec 
     * each sentences in new line
     * sentences - words separated by | 
     * vepkar-20190129-vep
     * 
     */
    public function exportSentencesToLines() {//Request $request
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
//dd(ini_get('memory_limit'));
        $date = Carbon::now();
        $date_now = $date->toDateString();
        $lang_id = 1;
        $lang = Lang::find($lang_id);
        $filename = 'export/vepkar-'.$date_now.'-'.$lang->code.'.txt';
        Storage::disk('public')->put($filename, "# ".$lang->name);
        $texts = Text::where('lang_id',$lang_id)
//                    ->where('id',1416)
                ->get();
        foreach ($texts as $text) {
            Storage::disk('public')->append($filename, $text->sentencesToLines());
        }
        print  '<p><a href="'.Storage::url($filename).'">'.$lang->name.'</a>';            
    }

}