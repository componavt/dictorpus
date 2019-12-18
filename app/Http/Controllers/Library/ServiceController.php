<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Caxy\HtmlDiff\HtmlDiff;
use Caxy\HtmlDiff\HtmlDiffConfig;

use App\Library\Grammatic\VepsName;
use App\Library\Service;

use App\Models\Corpus\Word;

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
            $langs[$l_id]['wrong_affix_count'] = number_format(Wordform::countWrongAffixes($l_id), 0, ',', ' ');
            $langs[$l_id]['unmarked_words_count'] = number_format(Word::countUnmarked($l_id), 0, ',', ' ');
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
    
    function reloadStemAffixes(Request $request) {
        $lang_id = (int)$request->input('search_lang');
        
        if (!$lang_id) {
            return;
        }

        Service::reloadStemAffixesForLang($lang_id);
    }
    
    public function tmpUpdateStemAffix() {
//print "<pre>";        
        $lemmas = Lemma::orderBy('id')->get(); //where('id','>',1)->take(10)
        foreach ($lemmas as $lemma) {
            list($max_stem, $affix) = $lemma->getStemAffixByStems();
            if ($max_stem!=$lemma->reverseLemma->stem || $affix!=$lemma->reverseLemma->affix || !sizeof($stems)) {
print sprintf("<p><b>id:</b> %s, <b>lang:</b> %s, <b>lemma:</b> <a href=\"/dict/lemma/%s\">%s</a>, <b>dialects:</b> [%s], <b>stems:</b> [%s], <b>max_stem:</b> %s, <b>affix:</b> %s",
        $lemma->id, $lemma->lang_id, $lemma->id, $lemma->lemma, join(", ",$dialects), join(", ",$stems), $max_stem, $affix);   
            }
            if ($max_stem!=$lemma->reverseLemma->stem || $affix!=$lemma->reverseLemma->affix) {
print sprintf(", <span style='color:red'><b>reverse_stem:</b> %s, <b>reverse_affix:</b> %s</span>", $lemma->reverseLemma->stem, $lemma->reverseLemma->affix);   
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
    
    /**
     * Search words of texts, 
     * 
     * update words set checked=0;
     * 
     * @param Request $request
     */
    public function addUnmarkedLinks(Request $request) {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        $lang_id = (int)$request->input('search_lang');
        
        if (!$lang_id) {
            return;
        }
        
        $word_groups = Word::select('word')->whereChecked(0)
                        ->whereIn('text_id', function ($q) use ($lang_id) {
                            $q->select('id')->from('texts')->where('lang_id',$lang_id);
                       })->whereNotIn('id', function ($query) {
                            $query->select('word_id')->from('meaning_text');
        })->groupBy('word')
        //->take(10)
        ->get();  
        foreach ($word_groups as $group) {
            $words = Word::where('word', 'like', $group->word)
                            ->whereIn('text_id', function ($q) use ($lang_id) {
                                $q->select('id')->from('texts')->where('lang_id',$lang_id);
                           })->whereNotIn('id', function ($query) {
                                $query->select('word_id')->from('meaning_text');
            })->get();  
            foreach ($words as $word) {
                $num_links = $word->setMeanings([], $lang_id);
                if ($num_links) {
    print "<p>text=".$word->text_id.", sentence_id=".$word->sentence_id.", w_id=".$word->w_id.", word=".$word->word. '<span style="color: red">'. $num_links. '</span>';            
                }
                $word->checked=1;
                $word->save();
            }
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
    /** 
     * (1) Copy vepsian.wordform to vepkar.wordforms (without dublicates)
     * (2) Copy vepsian.lemma_gram_wordform to vepkar.lemma_wordform
     */
/*    public function tempInsertVepsianWordform()
    {
        $lemma_wordfoms = DB::connection('vepsian')
                            ->table('lemma_gram_wordform')
                            ->orderBy('lemma_id','wordform_id')
                            //->take(1)
                            ->get();
 
        DB::connection('mysql')->table('lemma_wordform')->delete();

        DB::connection('mysql')->table('wordforms')->delete();
        DB::connection('mysql')->statement('ALTER TABLE wordforms AUTO_INCREMENT = 1');
        
        
        foreach ($lemma_wordfoms as $lemma_wordform):
            $veps_wordform = DB::connection('vepsian')
                            ->table('wordform')
                            ->find($lemma_wordform->wordform_id);
            $wordform = Wordform::firstOrNew(['wordform' => $veps_wordform->wordform]); 
            $wordform->updated_at = $veps_wordform->modified;
            $wordform->created_at = $veps_wordform->modified;
            $wordform->save();
            
            if ($lemma_wordform->gram_set_id === 0) {
                $lemma_wordform->gram_set_id = NULL;
            }
            
            DB::connection('mysql')->table('lemma_wordform')->insert([
                    'lemma_id' => $lemma_wordform->lemma_id,
                    'wordform_id' => $wordform->id,
                    'gramset_id' => $lemma_wordform->gram_set_id,
                    //'created_at' => $wordform->updated_at,
                    //'updated_at' => $wordform->created_at
                ]
            );
                
        endforeach;
     }
    
    public function tempCheckWordformsWithSpaces(Request $request) {
//print "<pre>";        
        $id = $request->id;
        $wordforms = Wordform::where('wordform','like','% %');
        if ($id) {
            $wordforms = $wordforms->where('id','>',$id);
        }
        $wordforms = $wordforms->orderBy('id')->get();//take(10)->
        $count = 1;
        foreach ($wordforms as $wordform) {
            print "<p>".$count++.') '.$wordform->id.', '.$wordform->wordform;
            if ($wordform->lemmas()->count()) { 
                print $wordform->trimWord() ? '<br>Wordform saved' : '';
                $wordform->checkWordformWithSpaces(1);
            } else {
                $wordform->delete();
                print "<br>Wordform deleted";
            }
            print "</p>";
        }
    }    
    
    public function tmpFixNegativeVepsVerbForms() {
        $lang_id = 1;
        $gramsets = [70, 71, 72, 73, 78, 79, 80, 81, 82, 83, 84, 85, 50, 74, 76, 77, 116, 117, 118, 119, 120, 121];
        $dialect_id=43;
        foreach ($gramsets as $gramset_id) {
            $negation = Grammatic::negativeForm($gramset_id, $lang_id);
            $lemmas = Lemma::where('lang_id', $lang_id)
                    ->whereIn('id', function($query) use ($dialect_id, $gramset_id) {
                        $query->select('lemma_id')->from('lemma_wordform')
                              ->where('gramset_id',$gramset_id)
                              ->where('dialect_id',$dialect_id);
                    })->where('id','<>',828)->where('id','<>',652)
                    ->orderBy('lemma')->get();
            $count = 1;
            foreach($lemmas as $lemma) {
                foreach ($lemma->wordforms()->wherePivot('gramset_id', $gramset_id)->get() as $wordform) {
                    if (preg_match("/^".$negation."/", $wordform->wordform)) { continue; }
                    $new_wordform = $negation.$wordform->wordform;
                    print "<p>".$count++.'. '.$lemma->id.'. '.$new_wordform;
                    $lemma->wordforms()
                          ->wherePivot('wordform_id',$wordform->id)
                          ->wherePivot('gramset_id',$gramset_id)
                          ->wherePivot('dialect_id',$dialect_id)
                          ->detach();                    
                    $lemma->addWordform($new_wordform, $gramset_id, $dialect_id); 
                }
            }
        }
    }
 *
 */  
    /**
     * When we add column lang_id in table gramset_pos,
     * we old records associated with vepsian lang (lang_id=1) and
     * dublicated existing records for 3 karelian langs (lang_id=4..6)
     *
     * @return null
     */
/*    
    public function tempInsertGramsetPosLang()
    {
        $langs = [4,5,6];
        $gramset_pos = DB::table('gramset_pos')->where('lang_id',1)->get();
        foreach ($gramset_pos as $rec) {
            foreach ($langs as $lang) {
            DB::table('gramset_pos')->insert([
                    'gramset_id' => $rec->gramset_id,
                    'pos_id' => $rec->pos_id,
                    'lang_id' => $lang
                ]);
            }
        }
    }
*/    
    /**
     * Reads some gramsets for non-reflexive verbs and 
     * inserts the same records for reflexive verbs.
     *
     * @return null
     */
/*    
    public function tempInsertGramsetsForReflexive()
    {
        $reflexive_sequence_number=143;
        $lang_id = 6; // lude lang
        $pos_id = 11; // verb
        $reflex_verb = 47; // id of grammatical attribure 'reflexive verb'
        $seq_nums = [0=>71, 82=>95, 106=>119]; // ranges of sequence numbers  
        $langs = [1, 4, 5, 6];
        
        foreach ($seq_nums as $min=>$max) {
            $gramsets = Gramset::orderBy('sequence_number')
                               ->join('gramset_pos', 'gramsets.id', '=', 'gramset_pos.gramset_id')
                               ->where('lang_id',$lang_id)
                               ->where('pos_id',$pos_id)
                               ->where('sequence_number','>',$min)
                               ->where('sequence_number','<',$max)
                               ->get();
            foreach ($gramsets as $gramset) {
                $ref_gramset = Gramset::create([
                    'gram_id_mood' => $gramset->gram_id_mood, 
                    'gram_id_tense' => $gramset->gram_id_tense, 
                    'gram_id_person' => $gramset->gram_id_person, 
                    'gram_id_number' => $gramset->gram_id_number, 
                    'gram_id_negation' => $gramset->gram_id_negation, 
                    'gram_id_reflexive' => $reflex_verb,
                    'sequence_number' => $reflexive_sequence_number++,
                ]);

                foreach ($langs as $l_id) {
                    $ref_gramset-> parts_of_speech()
                                -> attach($pos_id, ['lang_id'=>$l_id]);
                }
            }
        }
    }
 * 
 */
//select count(*) from words where (word like '%Ü%' COLLATE utf8_bin OR word like '%ü%' COLLATE utf8_bin OR word like '%w%') and text_id in (SELECT id from texts where lang_id=5);
/*
    public function tmpProcessOldLetters() {
        $lang_id=5;
        $words = Word::whereRaw("(word like '%Ü%' COLLATE utf8_bin OR word like '%ü%' COLLATE utf8_bin OR word like '%w%')"
                . " and text_id in (SELECT id from texts where lang_id=5)")  // only livvic texts
                     ->take(1000)->get();
//dd($words->toSql());        
        foreach ($words as $word) {
//dd($word->word);            
            $new_word = Grammatic::changeLetters($word->word);
            $new_word_l = strtolower($new_word);
            if ($new_word != $word->word) {
//dd($word->text_id);        
print "<p>".$word->word;        
                DB::statement("DELETE FROM meaning_text WHERE word_id=".$word->id);
                $wordform_q = "(SELECT id from wordforms where wordform like '$new_word' or wordform like '$new_word_l')";
                $lemma_q = "(SELECT lemma_id FROM lemma_wordform WHERE wordform_id in $wordform_q)";
                $meanings = Meaning::whereRaw("lemma_id in (SELECT id from lemmas where lang_id=$lang_id and (lemma like '$new_word' or lemma like '$new_word_l' or id in $lemma_q))")
                                   ->get();    
//dd($meanings);    
                foreach ($meanings as $meaning) {
                    $meaning->texts()->attach($word->text_id,
                            ['sentence_id'=>$word->sentence_id,
                             'word_id'=>$word->id,
                             'w_id'=>$word->w_id,
                             'relevance'=>1]);
                    
                }
                $word->word = $new_word;
                $word->save();
            }
//                        $word_for_DB = Word::changeLetters($word_for_DB);
        }
        
    }
*/
    /*    public function tempStripSlashes()
    {
        $texts = Text::all();
        foreach ($texts as $text) {
            $text->title = stripslashes($text->title);
            $text->text = stripslashes($text->text);
            $text->save();            
        }
        
    }
 * 
 */
    
    /*    
    public function tempInsertVepsianText()
    {
        DB::connection('mysql')->table('texts')->delete();
       
        DB::connection('mysql')->table('transtexts')->delete();

        $veps_texts = DB::connection('vepsian')
                            ->table('text')
                            ->where('lang_id',2)
                            ->orderBy('id')
                            //->take(1)
                            ->get();
        
        foreach ($veps_texts as $veps_text):
            $text = new Transtext;
            $text->id = $veps_text->id;
            $text->lang_id = $veps_text->lang_id;
            $text->title = $veps_text->title;
            $text->text = $veps_text->text;
            $text->updated_at = $veps_text->modified;
            $text->created_at = $veps_text->modified;
            $text->save();            
        endforeach;

        $veps_texts = DB::connection('vepsian')
                            ->table('text')
                            ->where('lang_id',1)
                            ->orderBy('id')
                            //->take(1)
                            ->get();
 
        foreach ($veps_texts as $veps_text):
            $text = new Text;
            $text->id = $veps_text->id;
            $text->corpus_id = $veps_text->corpus_id;
            $text->lang_id = $veps_text->lang_id;
            $text->title = $veps_text->title;
            $text->text = $veps_text->text;
            $text->source_id = $veps_text->source_id;
            $text->event_id = $veps_text->event_id;
            $text->updated_at = $veps_text->modified;
            $text->created_at = $veps_text->modified;

            $transtext = DB::connection('vepsian')
                            ->table('text_pair')
                            ->where('text1_id',$text->id)
                            ->first();
            if ($transtext) {
                $text->transtext_id = $transtext->text2_id;
            }
            $text->save();            
        endforeach;
     }
 */
/*
    public function tempInsertVepsianDialectText()
    {
        DB::connection('mysql')->table('dialect_text')->delete();
       
        $veps_texts = DB::connection('vepsian')
                            ->table('text_label')
                            ->join('text','text.id','=','text_label.text_id')
                            ->where('label_id','<',6)
                            ->where('lang_id',1)
                            ->orderBy('text_id')
                            //->take(1)
                            ->get();
        
        foreach ($veps_texts as $veps_text):
            DB::connection('mysql')->table('dialect_text')
                                   ->insert(['dialect_id'=>$veps_text->label_id,
                                             'text_id'=>$veps_text->text_id]);
        endforeach;
     }
/*    
    public function tempInsertVepsianGenreText()
    {
        DB::connection('mysql')->table('dialect_text')->delete();
       
        $veps_texts = DB::connection('vepsian')
                            ->table('text_label')
                            ->join('text','text.id','=','text_label.text_id')
                            ->where('label_id','>',5)
                            ->where('lang_id',1)
                            ->orderBy('text_id')
                            //->take(1)
                            ->get();
        
        foreach ($veps_texts as $veps_text):
            DB::connection('mysql')->table('genre_text')
                                   ->insert(['genre_id'=>$veps_text->label_id,
                                             'text_id'=>$veps_text->text_id]);
        endforeach;
     }
 * 
 */
     // select text1_id,text2_id,t1.event_id,t2.event_id  from text_pair, text as t1, text as t2 where t2.lang_id=2 and t2.event_id is not null and text_pair.text1_id=t1.id and text_pair.text2_id=t2.id;
     // select text1_id,text2_id,text.event_id from text_pair,text where text.lang_id=2 and text.event_id is not null and text_pair.text2_id=text.id;
    
}
