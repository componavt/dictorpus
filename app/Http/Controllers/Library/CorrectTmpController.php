<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use LaravelLocalization;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Caxy\HtmlDiff\HtmlDiff;
use Caxy\HtmlDiff\HtmlDiffConfig;
use DB;

use App\Library\Grammatic;
use App\Library\Grammatic\VepsName;
use App\Library\Service;
use App\Library\Str;

use App\Models\Corpus\Sentence;
use App\Models\Corpus\Text;
use App\Models\Corpus\TextWordform;
use App\Models\Corpus\Transtext;
use App\Models\Corpus\Word;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
//use App\Models\Dict\LemmaWordform;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Wordform;

class CorrectTmpController extends Controller
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
        
    /**
     * select count(*) from lemma_wordform where wordform_for_search='';
     */
    public function tmpFillWordformForSearch() {
        $is_all_checked = false;
        while (!$is_all_checked) {
//            $lemma = Lemma::orderBy('id')->first();
            $lemma_wordform = Wordform::join('lemma_wordform', 'lemma_wordform.wordform_id', '=', 'wordforms.id')
                                ->join('lemmas', 'lemma_wordform.lemma_id', '=', 'lemmas.id')
                                ->where('lemma_wordform.wordform_for_search','')
                                ->take(1)->first();
            if ($lemma_wordform) {
            DB::statement("UPDATE lemma_wordform SET wordform_for_search='".
                          Grammatic::changeLetters($lemma_wordform->wordform, $lemma_wordform->lang_id).
                          "' WHERE wordform_id=".$lemma_wordform->wordform_id. " and lemma_id=".$lemma_wordform->lemma_id);
            } else {
                $is_all_checked = true;
            }
//exit(0);            
        }
    }
    
    public function tmpFillGenres() {
        $lang_id=1;
        $corpus_id=6;
        $genre_id=11;
        $texts = Text::whereLangId($lang_id)->whereCorpusId($corpus_id)
                     ->whereNotIn('id', function ($query) {
                         $query->select('text_id')->from('genre_text');
                     })->get();
//dd($texts);    
        foreach ($texts as $text) {
            $text->genres()->attach($genre_id);
        }            
        print 'done.';
    }
    
    /**
     * update texts set checked=0;
     */
    public function tmpSplitTextsIntoSentences() {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        
        $is_all_checked = false;
        while (!$is_all_checked) {
            $text = Text::orderBy('id')->whereChecked(0)->first();
            if ($text) {
                $text->splitXMLToSentencesAndWrite();
//exit(0);               
                $text->checked=1;
                $text->save();
            } else {
                $is_all_checked = true;
            }
        }
print 'done';        
    }

    /**
     * update sentences set checked=0;
     */
    public function tmpWordNumbersForWords() {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        
        $is_all_checked = false;
        while (!$is_all_checked) {
            $word = Word::whereWordNumber(0)->first();
            if (!$word) {
                $is_all_checked = true;
                continue;
            }
            $sentence = Sentence::whereTextId($word->text_id)->whereSId($word->s_id)->first();
            if (!$sentence) {
                dd("Нет предложения ".$word->text_id. '-'. $word->s_id);
            }
            $sentence->numerateWords();
            $sentence->checked=1;
            $sentence->save();
    //dd($sentence->id);
        }
print 'done';        
    }

    /**
     * move beginning tags <br> from sentences to text structure
     * 
     * update sentences set checked=0;
     */
    public function tmpMoveBrFromSentences() {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        
        $is_all_checked = false;
        while (!$is_all_checked) {
            $sentence = Sentence::orderBy('id')/*->whereChecked(0)*/
                        ->where('text_xml', 'like', '<s id="_"><br/>%')
                        ->orWhere('text_xml', 'like', '<s id="__"><br/>%')
                        ->orWhere('text_xml', 'like', '<s id="___"><br/>%')->first();
            if ($sentence) {
                    $sentence->moveBrFromSentences();
/*                $sentence->checked=1;
                $sentence->save();*/
            } else {
                $is_all_checked = true;
            }
//                    dd($sentence->id);
        }
print 'done';        
    }

    /**
     * select count(*) from words where sentence_id=0;
     */
    public function tmpFillSentenceIdInWords() {
        $is_all_checked = false;
        while (!$is_all_checked) {
            $word = Word::whereSentenceId(0)->first();
            if ($word) {
                $sentence = Sentence::whereTextId($word->text_id)
                                    ->whereSId($word->s_id)->first();
                if (!$sentence) {
                    dd("Нет предложения ". $word->text_id. '-'.$word->s_id);
                }
                DB::statement("UPDATE words SET sentence_id='".$sentence->id.
                              "' WHERE text_id=".$word->text_id. ' and s_id='.$word->s_id);
//exit(1);                
            } else {
                $is_all_checked = true;
            }
//exit(0);            
        }
    }
    
    /**
     * select count(*) from text_wordform where sentence_id=0;
     */
    public function tmpFillSentenceIdInTextWordform() {
        $is_all_checked = false;
        while (!$is_all_checked) {
//            $text_wordform = TextWordform::whereIsNull('sentence_id')->first();
            $text_wordform = TextWordform::whereSentenceId(0)->first();
            if ($text_wordform) {
                $text_id = $text_wordform->text_id;
                $w_id = $text_wordform->w_id;
                $sentence = Sentence::whereIn('id', function($q) use ($text_id, $w_id) {
                    $q -> select('sentence_id')->from('words')
                       -> whereTextId($text_id)
                       ->whereWId($w_id);
                })->first();
                if (!$sentence) {
                    dd("Нет предложения ". $text_id. '-'.$w_id);
                }
                DB::statement("UPDATE text_wordform SET sentence_id='".$sentence->id.
                              "' WHERE text_id=".$text_id. ' and w_id='.$w_id);
//exit(1);                
            } else {
                $is_all_checked = true;
            }
//exit(0);            
        }
    }
    
    /**
     * select count(*) from text_wordform where word_id=0;
     */
    public function tmpFillWordIdInTextWordform() {
        $is_all_checked = false;
        while (!$is_all_checked) {
            $text_wordform = TextWordform::whereWordId(0)->first();
            if ($text_wordform) {
                $text_id = $text_wordform->text_id;
                $w_id = $text_wordform->w_id;
                $word = Word::whereTextId($text_id)
                       ->whereWId($w_id)->first();
                if (!$word) {
                    dd("Нет слова ". $text_id. '-'.$w_id);
                }
                DB::statement("UPDATE text_wordform SET word_id='".$word->id.
                              "' WHERE text_id=".$text_id. ' and w_id='.$w_id);
//exit(1);                
            } else {
                $is_all_checked = true;
            }
//exit(0);            
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
                            ['s_id'=>$word->s_id,
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
    
/*    
    public function tempInsertVepsianSource()
    {
        $veps_sources = DB::connection('vepsian')
                            ->table('source')
                            ->orderBy('id')
                            //->take(1)
                            ->get();
 
        DB::connection('mysql')->table('sources')->delete();
       
        foreach ($veps_sources as $veps_source):
            $source = new Source;
            $source->id = $veps_source->id;
            $source->title = $veps_source->title;
            $source->author = $veps_source->author;
            $source->year = $veps_source->year;
            $source->ieeh_archive_number1 = $veps_source->ieeh_archive_number1;
            $source->ieeh_archive_number2 = $veps_source->ieeh_archive_number2;
            $source->comment = $veps_source->comment;

            if ($veps_source->page_from) {
                $source->pages = $veps_source->page_from;
                if ($veps_source->page_to) {
                    $source->pages .= '-';
                }
            }
            if ($veps_source->page_to) {
                $source->pages .= $veps_source->page_to;
            }
                
            $source->updated_at = $veps_source->modified;
            $source->created_at = $veps_source->modified;
            $source->save();
            
        endforeach;
     }
 * 
 */
    
    /*    
    public function tempInsertVepsianPlace()
    {
        $veps_distr_places = DB::connection('vepsian')
                            ->table('place')
                            ->orderBy('id')
                            //->take(1)
                            ->get();
 
        DB::connection('mysql')->table('place_names')->delete();
        DB::connection('mysql')->statement('ALTER TABLE place_names AUTO_INCREMENT = 1');
        DB::connection('mysql')->table('places')->delete();
       
        foreach ($veps_distr_places as $veps_distr_place):
            if ($veps_distr_place->village_id != NULL) {
                $village = DB::connection('vepsian')
                             ->table('place_village')
                             ->where('id',$veps_distr_place->village_id)
                             ->first();
                $name_nu = $village->ru;
                $name_vep = $village->vep;
            } else {
                $name_nu = $name_vep = NULL;
            }

            $place = new Place;
            $place->id = $veps_distr_place->id;
            
            if ($veps_distr_place ->region_id == 2) {
                $place->region_id = 2;
            } else {
                $place->district_id = $veps_distr_place ->region_id;
                $district = District::find($veps_distr_place ->region_id);
                $place->region_id = $district -> region_id;
            }
            
            $place->name_ru = $name_nu;
            $place->save();
            
            if ($name_vep) {
                $place_name = new PlaceName;
                $place_name->place_id = $veps_distr_place->id;
                $place_name->lang_id = 1;
                $place_name->name = $name_vep;
                $place_name->save();
            }
            
        endforeach;
     }
 * 
 */

/*    
    public function tempInsertVepsianInformant()
    {
        $veps_informants = DB::connection('vepsian')
                            ->table('informant')
                            ->orderBy('id')
                            //->take(1)
                            ->get();
 
        DB::connection('mysql')->table('informants')->delete();
       
        foreach ($veps_informants as $veps_informant):
            $informant = new Informant;
            $informant->id = $veps_informant->id;
            $informant->birth_place_id = $veps_informant->birth_place_id;
            $informant->birth_date = $veps_informant->birth_date;
            $informant->name_ru = $veps_informant->name;
            $informant->save();            
        endforeach;
     }
 * 
 */
    
/*    
    public function tempInsertVepsianRecorder()
    {
        $veps_recorders = DB::connection('vepsian')
                            ->table('recorder')
                            ->orderBy('id')
                            //->take(1)
                            ->get();
 
        DB::connection('mysql')->table('recorders')->delete();
       
        foreach ($veps_recorders as $veps_recorder):
            $recorder = new Recorder;
            $recorder->id = $veps_recorder->id;
            $recorder->name_ru = $veps_recorder->name;
            $recorder->save();            
        endforeach;
     }
 * 
 */
}
