<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Caxy\HtmlDiff\HtmlDiff;
use Caxy\HtmlDiff\HtmlDiffConfig;

use App\Library\Grammatic\VepsName;
use App\Library\Service;

use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Wordform;

class ServiceController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // permission= dict.edit, redirect failed users to /dict/lemma/, authorized actions list:
        $this->middleware('auth:admin,/');
    }
    
    public function index() {
        $langs = [];
        foreach (Lang::projectLangIDs() as $l_id) {
            $langs[$l_id]['name']=Lang::getNameById($l_id);
            $langs[$l_id]['affix_count'] = number_format(Wordform::countWithoutAffixes($l_id), 0, ',', ' ');
        }
        
        return view('page.service')
                ->with(['langs' => $langs]);
        
    }
    public function addCompTypeToPhrases() {
        $lemmas = Lemma::where('pos_id', PartOfSpeech::getPhraseID())->orderBy('lemma')->get();
        foreach ($lemmas as $lemma) {
            if ($lemma->features && $lemma->features->comptype_id) {
                continue;
            }
            $comp_lemmas = [];
            foreach ($lemma->phraseLemmas as $clemma) {
                $comp_lemmas[] = '<a href="/dict/lemma/'.$clemma->id.'">'.$clemma->lemma.'</a> = '.$clemma->pos->code;
            }    
            
print '<p><a href="/dict/lemma/'.$lemma->id.'">'.$lemma->lemma. '</a> ('. join('; ',$comp_lemmas). ')';
            
        }
    }
    
    public function illativeTable() {
        $lang_id = 1;
        $dialect_id = 43;
        $parts_of_speech = PartOfSpeech::getNameIDs();
//dd($parts_of_speech);        
        $gramset_gen_sg = 3;
        $gramset_ill_sg = 10;
        $gramset_term_sg = 16;
        $gramset_add_sg = 19;
        $lemmas = [];
        foreach ($parts_of_speech as $pos_id) {
            $pos = PartOfSpeech::find($pos_id);
            $lemma_coll = Lemma::where('lang_id', $lang_id)
                           ->where('pos_id', $pos_id)
                           ->join('lemma_wordform', 'lemmas.id', '=', 'lemma_wordform.lemma_id')
                           ->where('gramset_id', $gramset_gen_sg)
                           ->where('dialect_id',$dialect_id)
                           ->orderBy('lemma')->get();
            foreach ($lemma_coll as $lemma): 
                $gen_wordform = Wordform::find($lemma->wordform_id);
                if (!$gen_wordform) {
                    continue;
                }
                $lemma_wordforms=[
                    'lemma' => $lemma->lemma,
                    'gen_sg' => $gen_wordform->wordform,
                    'ill_sg' => ['old'=>$lemma->wordform($gramset_ill_sg, $dialect_id),'new'=>''],
                    'term_sg' => ['old'=>$lemma->wordform($gramset_term_sg, $dialect_id),'new'=>''],
                    'add_sg' => ['old'=>$lemma->wordform($gramset_add_sg, $dialect_id),'new'=>''],
                    ];
                if (!preg_match("/^(.+)n$/", $lemma_wordforms['gen_sg'], $regs)) {
                    continue;
                }
                $stems = [1=>$regs[1], 2=>VepsName::illSgBase($regs[1])];
                $lemma_wordforms['ill_sg']['new'] = VepsName::wordformByStems($stems, $gramset_ill_sg, $dialect_id);
                $lemma_wordforms['term_sg']['new'] = VepsName::wordformByStems($stems, $gramset_term_sg, $dialect_id);
                $lemma_wordforms['add_sg']['new'] = VepsName::wordformByStems($stems, $gramset_add_sg, $dialect_id);
                
                if ($lemma_wordforms['ill_sg']['new'] != $lemma_wordforms['ill_sg']['old']) {
                    $lemma->deleteWordforms($gramset_ill_sg, $dialect_id);
                    $lemma->addWordforms($lemma_wordforms['ill_sg']['new'], $gramset_ill_sg, $dialect_id);
                    $lemma_wordforms['ill_sg']['old'] = $lemma->wordform($gramset_ill_sg, $dialect_id);
                }
                if ($lemma_wordforms['term_sg']['new'] != $lemma_wordforms['term_sg']['old']) {
                    $lemma->deleteWordforms($gramset_term_sg, $dialect_id);
                    $lemma->addWordforms($lemma_wordforms['term_sg']['new'], $gramset_term_sg, $dialect_id);
                    $lemma_wordforms['term_sg']['old'] = $lemma->wordform($gramset_term_sg, $dialect_id);
                }
                if ($lemma_wordforms['add_sg']['new'] != $lemma_wordforms['add_sg']['old']) {
                    $lemma->deleteWordforms($gramset_add_sg, $dialect_id);
                    $lemma->addWordforms($lemma_wordforms['add_sg']['new'], $gramset_add_sg, $dialect_id);
                    $lemma_wordforms['add_sg']['old'] = $lemma->wordform($gramset_add_sg, $dialect_id);
                }

                if ($lemma_wordforms['ill_sg']['new'] != $lemma_wordforms['ill_sg']['old'] ||
                    $lemma_wordforms['term_sg']['new'] != $lemma_wordforms['term_sg']['old'] || 
                    $lemma_wordforms['add_sg']['new'] != $lemma_wordforms['add_sg']['old']) {
                        $lemmas[$pos->name][$lemma->id] = $lemma_wordforms;
                }
            endforeach; 
        }
//dd($lemmas);        
        return view('dict.lemma.illative_table',compact('lemmas'));
    }
    
    /**
     * Compare wordform from DB with the generated by rules
     * 
     * @param Request $request
     * @return type
     */
    public function checkWordforms(Request $request) {
        $diffConfig = new HtmlDiffConfig();
        $lang_id = 1;
//        $parts_of_speech = array_merge(PartOfSpeech::getNameIDs(),[PartOfSpeech::getVerbID()]);
        $lemmas = [];
  //      foreach ($parts_of_speech as $pos_id) {
        $pos_id=11;
            $pos = PartOfSpeech::find($pos_id);
            $gramset_list = Gramset::getList($pos_id, $lang_id, true);
            $lemma_coll = Lemma::whereLangId($lang_id)->wherePosId($pos_id)
                    ->where('lemma', 'like', 'a%')->orderBy('lemma')->take(1)->get();
//dd ($lemma_coll);           
            foreach ($lemma_coll as $lemma) {
    //print "<p>".$lemma->lemma." : ".$lemma->pos->code."</p>";            
    /*            if (!$lemma->pos->isChangeable()) {
                    continue;
                }
    */            
                $dialects = $lemma->dialects()->whereNotNull('dialect_id')->get();
                if (!$dialects) {
                    continue;
                }
                $lemma_dialect = [];
                foreach ($dialects as $dialect) {
                    $gramset_wordforms = $lemma->generateWordforms($dialect->id);
//dd($gramset_wordforms);                    
                    if (!$gramset_wordforms) {
                        continue(2);
                    }
                    foreach ($gramset_wordforms as $gramset_id=>$new_wordform) {
                        $old_wordform = $lemma->wordform($gramset_id,$dialect->id);
                        
                        if (trim($old_wordform) != trim($new_wordform)) {
                            $htmlDiff = HtmlDiff::create($old_wordform, $new_wordform,$diffConfig);
//dd($htmlDiff);
                            $lemma_dialect[$dialect->name][$gramset_list[$gramset_id]] = [0=>$old_wordform, 1=>$new_wordform, 2=>$htmlDiff->build()];
                        }
                    }
                }
//dd($lemma_dialect);                
                if (sizeof($lemma_dialect)) {
                    $lemmas[$pos->name][$lemma->id]=['lemma'=>$lemma->lemma, 'dialects'=>$lemma_dialect];
                }
            }
        //}
//dd($lemmas);        
        return view('dict.lemma.check_wordforms',compact('lemmas'));
        
    }
    
    function addWordformAffixes(Request $request) {
        $lang_id = (int)$request->input('search_lang');
        
        if ($lang_id) {
            Service::addWordformAffixesForLang($lang_id);
            return;
        }

        foreach (Lang::projectLangIDs() as $lang_id) {
            Service::addWordformAffixesForLang($lang_id);
        }      
    }
/*    
    public function tmpUpdateStemAffix() {
//print "<pre>";        
        $lemmas = Lemma::orderBy('id')->get(); //where('id','>',1)->take(10)
        foreach ($lemmas as $lemma) {
            if (!$lemma->isChangeable()) {
                $lemma->reverseLemma->stem = $lemma->lemma;
                $lemma->reverseLemma->affix = null;
                $lemma->save();
//var_dump($lemma->lemma, $lemma->reverseLemma);                
                continue;
            }
            $dialects = $lemma->getDialectIds();
            $max_stem=$lemma->lemma; 
            $stems = [];
            foreach ($dialects as $dialect_id) {
                $stems_for_max = $stems = $lemma->getBases($dialect_id);
                $lemma->updateBases($stems, $dialect_id);
                
                if ($lemma->lang_id==1 && $lemma->pos_id == PartOfSpeech::getVerbID()) {
                    $stems_for_max = array_slice($stems, 0, 5);
                }
                list($max_stem) = Grammatic::maxStem(array_merge([$max_stem], $stems_for_max), $lemma->lang_id, $lemma->pos_id);
            }
            if (preg_match("/^".$max_stem."(.*)/u", $lemma->lemma, $regs)) {
                $affix = $regs[1];
            } else {
                $affix = false;
            }
            if ($max_stem!=$lemma->reverseLemma->stem || $affix!=$lemma->reverseLemma->affix || !sizeof($stems)) {
print sprintf("<p><b>id:</b> %s, <b>lang:</b> %s, <b>lemma:</b> <a href=\"/dict/lemma/%s\">%s</a>, <b>dialects:</b> [%s], <b>stems:</b> [%s], <b>max_stem:</b> %s, <b>affix:</b> %s",
        $lemma->id, $lemma->lang_id, $lemma->id, $lemma->lemma, join(", ",$dialects), join(", ",$stems), $max_stem, $affix);   
            }
            if ($max_stem!=$lemma->reverseLemma->stem || $affix!=$lemma->reverseLemma->affix) {
print sprintf(", <span style='color:red'><b>reverse_stem:</b> %s, <b>reverse_affix:</b> %s</span>",
        $lemma->reverseLemma->stem, $lemma->reverseLemma->affix);   
            $lemma->reverseLemma->stem = $max_stem;
            $lemma->reverseLemma->affix = $affix;
            $lemma->reverseLemma->save();
}
            if ($affix === false) {
                dd('ERROR');
            }
print "</p>";
        }
    }


    /*
     * split wordforms such as pieksäh/pieksähes on two wordforms
     * and link meanings of lemma with sentences
     * 
     * select * from meaning_text where meaning_id in (select id from meanings where lemma_id in (select lemma_id from lemma_wordform where wordform_id=8755))
     */
    /*
    public function tmpSplitWordforms() {
        $wordforms = Wordform::where('wordform','like','%/%')->take(100)->get();
        foreach($wordforms as $wordform) {
print "<br><br>".$wordform->id. "=".$wordform->wordform;  
//exit(0);
            $lemmas = $wordform->lemmas()->withPivot('gramset_id')
                      ->withPivot('dialect_id')->get();
            foreach ($lemmas as $lemma) {
                $gramset_id = $lemma->pivot->gramset_id;
                $dialect_id = $lemma->pivot->dialect_id;
                foreach (preg_split("/\//",$wordform->wordform) as $word) {
                    $wordform_obj = Wordform::findOrCreate(trim($word));
                    $exist_wordforms = $lemma->wordforms()
                                             ->wherePivot('gramset_id',$gramset_id)
                                             ->wherePivot('dialect_id',$dialect_id)
                                             ->wherePivot('wordform_id',$wordform_obj->id);
                    if (!$exist_wordforms->count()) {
                        $lemma->wordforms()->attach($wordform_obj->id, 
                               ['gramset_id'=>$gramset_id, 'dialect_id'=>$dialect_id]);
                    }
print "<br>".$wordform_obj->id.'='.$wordform_obj->wordform."; lemma: ".$lemma->id."=".$lemma->lemma;                    
                    $wordform_obj->updateTextLinks($lemma);
                }
                $wordform->lemmas()->wherePivot('gramset_id',$gramset_id)
                         ->wherePivot('dialect_id',$dialect_id)
                         ->detach();
            }
            $wordform->delete();
        }
    }
    
    public function tmpMoveReflexive() {
        $lemmas=Lemma::where('reflexive',1)->get();
       
        foreach ($lemmas as $lemma) {
            LemmaFeature::create(['id'=>$lemma->id,
                'reflexive'=>1]);
        }
        print 'done.';
    }
    
    
    /** Copy vepsian.{lemma and translation_lemma} to vepkar.lemmas
     * + temp column vepkar.lemmas.temp_translation_lemma_id
     */
/*    
    public function tempInsertVepsianLemmas()
    {
        $lemmas = DB::connection('vepsian')->table('lemma')->orderBy('id')->get();
 
     
        DB::connection('mysql')->table('meaning_texts')->delete();
        DB::connection('mysql')->statement('ALTER TABLE meaning_texts AUTO_INCREMENT = 1');
        
        DB::connection('mysql')->table('meanings')->delete();
        DB::connection('mysql')->statement('ALTER TABLE meanings AUTO_INCREMENT = 1');

        DB::connection('mysql')->table('lemmas')->delete();
        DB::connection('mysql')->statement('ALTER TABLE lemmas AUTO_INCREMENT = 1');
        
        foreach ($lemmas as $lemma) {
            DB::connection('mysql')->table('lemmas')->insert([
                    'id' => $lemma->id,
                    'lemma' => $lemma->lemma,
                    'lang_id' => 1,
                    'pos_id' => $lemma->pos_id,
                    'created_at' => $lemma -> modified,
                    'updated_at' => $lemma -> modified
                ]
            );
        }
         
    }
 */    
}
