<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use LaravelLocalization;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Caxy\HtmlDiff\HtmlDiff;
use Caxy\HtmlDiff\HtmlDiffConfig;
use DB;

use App\Library\Grammatic\VepsName;
use App\Library\Service;
use App\Library\Str;

use App\Models\Corpus\Sentence;
use App\Models\Corpus\Text;

use App\Models\Dict\Dialect;
use App\Models\Dict\Gramset;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\MeaningText;
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
        $this->middleware('auth:dict.edit,/');
    }
    
    public function index() {
        $langs = [];
        foreach (Lang::projectLangIDs() as $l_id) {
            $langs[$l_id]=Lang::getNameById($l_id);
        }
                
        return view('service.index', compact('langs'));        
    }
    
    function addWordformAffixes(Request $request) {
        $lang_id = (int)$request->input('search_lang');
//dd($lang_id);        
        if ($lang_id) {
            Service::addWordformAffixesForLang($lang_id);
            return;
        }
/*
        foreach (Lang::projectLangIDs() as $lang_id) {
            Service::addWordformAffixesForLang($lang_id);
        }      */
        
        $langs = [];
        foreach (Lang::projectLangIDs() as $l_id) {
            $langs[$l_id]['name']=Lang::getNameById($l_id);
            $langs[$l_id]['affix_count'] = number_format(Wordform::countWithoutAffixes($l_id), 0, ',', ' ');
            $langs[$l_id]['wrong_affix_count'] = number_format(Wordform::countWrongAffixes($l_id), 0, ',', ' ');
        }
        
        return view('service.add_wordform_affixes', compact('langs'));
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
                $dialects = $lemma->wordformDialects()->whereNotNull('dialect_id')->get();
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
    
    function reloadStemAffixes(Request $request) {
        $lang_id = (int)$request->input('search_lang');
        
        if (!$lang_id) {
            return;
        }

        Service::reloadStemAffixesForLang($lang_id);
    }
    
    function checkWordformsByRules(Request $request) {
        $lang_id = (int)$request->input('search_lang');
        
        if ($lang_id) {
            Service::checkWordformsByRules($lang_id);
            return;
        }

        foreach (Lang::projectLangIDs() as $lang_id) {
            Service::checkWordformsByRules($lang_id);
        }      
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
     * output only wordform list with links 
     * by search_lang, search_pos, w_count
     * 
     * @param Request $request
     */
    public function wordformsByWordformTotal(Request $request) {
        $lang_id = (int)$request->input('search_lang');
        $pos_id = (int)$request->input('search_pos'); 
        $w_total = (int)$request->input('w_count');
        
        $lemmas = Lemma::whereLangId($lang_id)
                       ->wherePosId($pos_id)
                       ->whereWordformTotal($w_total)
                       ->orderBy('lemma')
                       ->get();
        foreach ($lemmas as $lemma) {
            print "<p><a href=\"/dict/lemma/".$lemma->id."\">".$lemma->lemma."</a></p>";
        }

    }

    /*
     * Select livvic lemmas with partitives plural more than 2
     * 
     *  select lemma_id,count(*) as count from lemma_wordform where gramset_id=22 and dialect_id=44 group by lemma_id having count>2;
     * 
     * /service/regenerate_wrong_names
     */
    public function reGenerateWrongNames() {
        $dialect_id = 44; 
        $partPl_id = 22;
        
        $lemmas = Lemma::join('lemma_wordform', 'lemma_wordform.lemma_id', '=', 'lemmas.id')
                        ->where('gramset_id', $partPl_id)
                        ->where('dialect_id', $dialect_id)
                        ->groupBy('lemma_id')
                        ->havingRaw('count(*)>2')->get();
//dd($lemmas);        
        foreach ($lemmas as $lemma) {
            print "<p><a href=\"/dict/lemma/".$lemma->id."\">".$lemma->lemma."</a></p>";
            $lemma->reloadWordforms($dialect_id);
            $lemma->updateWordformTotal();        
        }

    }
    
    /*
     * Select livvic lemmas with illative plural 
     * 
     * 
     * /service/regenerate_livvic_ill_pl
     */
    public function reGenerateLivvicIllPl() {
        $dialect_id = 44; 
        $illPl_id = 61;
        
        $lemmas = Lemma::join('lemma_wordform', 'lemma_wordform.lemma_id', '=', 'lemmas.id')
                       ->where('gramset_id', $illPl_id)
                       ->where('dialect_id', $dialect_id)
//                       ->take(50)
                       ->get();
//dd($lemmas);        
        foreach ($lemmas as $lemma) {
            $old_wordform = $lemma->wordform($illPl_id, $dialect_id);
            $new_wordform = $lemma->generateWordform($illPl_id, $dialect_id, false);
            print "<p><a href=\"/dict/lemma/".$lemma->lemma_id."\">".$lemma->lemma."</a>";
            if ($new_wordform && $old_wordform != $new_wordform) {
                print " (".$old_wordform . ' -> '. $new_wordform.")";
                $lemma->deleteWordforms($illPl_id, $dialect_id);
                $lemma->addWordforms($new_wordform, $illPl_id, $dialect_id);
            }
            print "</p>";
        }

    }
    
    /**
     * select count(*) from lemmas where lang_id=5 and lemma not in (select lemma from lemmas where lang_id=4);
     * 
     * @param Request $request
     */
    public function copyLemmas(Request $request) {
        $lang_to = (int)$request->lang_to;  
        
        $url_args = Str::urlArgs($request) + [
                    'lang_from'  => (int)$request->input('lang_from'),
                    'lang_to'  => $lang_to,
                    'wordform_dialect_id' => (int)$request->input('wordform_dialect_id'),
                    'search_lemma'    => $request->input('search_lemma'),
                    'search_pos'    => (int)$request->input('search_pos'),
                ];        
//        $args_by_get = Str::searchValuesByURL($url_args);
        $added_lemmas = [];
        $dialect_is_right = true; 
                
        if ($url_args['lang_from'] && $lang_to) {            
            if ($request->copy_lemmas && $request->lemmas && $url_args['wordform_dialect_id']) {
                $dialect = Dialect::find($url_args['wordform_dialect_id']);                
                if ($dialect->lang_id == $lang_to) {
                    $added_lemmas = Service::copyLemmas($lang_to, $request->lemmas, $request->new_lemmas, $url_args['wordform_dialect_id']);                 
                } else {
                    $dialect_is_right = false;
                }
            }
            
            $lemmas = Lemma::whereLangId($url_args['lang_from'])
                          ->whereNotIn('lemma', function ($query) use ($lang_to) {
                              $query->select('lemma')->from('lemmas')
                                    ->whereLangId($lang_to);
                          });
            if ($url_args['search_lemma']) {
                $lemmas = $lemmas->where('lemma', 'like', $url_args['search_lemma']);
            }             
            if ($url_args['search_pos']) {
                $lemmas = $lemmas->wherePosId($url_args['search_pos']);
            }             
            $lemmas=$lemmas->orderBy('lemma');
            $numAll = $lemmas->count();
            $lemmas = $lemmas->paginate($url_args['limit_num']);                                   
        } else {
            $lemmas = null;
            $numAll = 0;
        }
        
        $lang_values = Lang::getListWithQuantity('reverseLemmas');
        $pos_values = PartOfSpeech::getGroupedListWithQuantity('lemmas');
        $dialect_values = $lang_to ? Dialect::getList($lang_to) : Dialect::getList(); //['NULL'=>'']+
        
        return view('service.copy_lemmas', 
                compact('added_lemmas', 'dialect_is_right', 'dialect_values', 'lang_values', 'lemmas', 
                        'numAll', 'pos_values', 'url_args'));        // , 'args_by_get'
    }
    
    /**
     * select text_id, w_id from meaning_text where word_id in (select word_id from meaning_text where relevance=1) and word_id in (select word_id from meaning_text where relevance>1);
     */
    public function checkMeaningText() {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        
        $words = DB::table('meaning_text')->select('text_id', 'w_id')
                ->whereIn('word_id', function ($query){
                    $query->select('word_id')->from('meaning_text')
                          ->where('relevance', '>', 1);
                })->whereIn('word_id', function ($query){
                    $query->select('word_id')->from('meaning_text')
                          ->where('relevance', 1);
                })->groupBy('text_id', 'w_id')->get();
        foreach ($words as $word) {
            print '<p><a href="/corpus/text/'.$word->text_id.'?search_wid='.$word->w_id.'">'.$word->text_id.'_'.$word->w_id.'</a></p>';
            DB::table('meaning_text')->whereTextId($word->text_id)->whereWId($word->w_id)->where('relevance', '>', 1)->update(['relevance'=>1]);
        }
    }

    /**
     * select wordform from wordforms, lemma_wordform where lemma_wordform.wordform_id = wordforms.id and gramset_id=179 and dialect_id=47 and (wordform like '%ä%nun' or wordform like '%ö%nun' or wordform like '%y%nun'); 
     * select lemmas.id, lemma, wordform from wordforms, lemma_wordform, lemmas where lemma_wordform.wordform_id = wordforms.id and lemma_wordform.lemma_id = lemmas.id and gramset_id=179 and dialect_id=47 and (wordform like '%ä%nun' or wordform like '%ö%nun' or wordform like '%y%nun') and lemmas.id< 23260 order by lemma_id; 
     */
    public static function reGenerateTverPartic2active() {
        $gramset_id=179;
        $dialect_id=47;
        $lemmas = Lemma::whereIn('id', function ($query) use ($gramset_id, $dialect_id) {
                            $query->select('lemma_id')->from('lemma_wordform')
                                  ->whereDialectId($dialect_id)
                                  ->whereGramsetId($gramset_id)
                                  ->whereIn('wordform_id', function ($q) {
                                      $q->select('id')->from('wordforms')
                                        ->where(function($q2) {
                                            $q2->where('wordform', 'like', '%ä%nun')
                                               ->orWhere('wordform', 'like', '%ö%nun')
                                               ->orWhere('wordform', 'like', '%y%nun');
                                        });
                                  });
                        }) ->orderBy('id')->get();
/*        $lemmas = Lemma::join('lemma_wordform', 'lemmas.id', '=', 'lemma_wordform.lemma_id') 
                       ->join('wordforms', 'lemma_wordform.wordform_id', '=', 'wordforms.id')
                       ->whereDialectId($dialect_id)
                       ->whereGramsetId($gramset_id)
                       ->where(function($q2) {
                            $q2->where('wordform', 'like', '%ä%nun')
                               ->orWhere('wordform', 'like', '%ö%nun')
                               ->orWhere('wordform', 'like', '%y%nun');
                       })
//                       ->groupBy('lemma_id','lemma','wordform')
                       ->orderBy('lemma_id')->get();*/
print "<ol>";                        
        foreach ($lemmas as $lemma) {
            $new_wordform = $lemma->generateWordform($gramset_id, $dialect_id);
            print '<li><a href="/ru/dict/lemma/'.$lemma->id.'">'.$lemma->lemma.'</a> : '. $lemma->wordform($gramset_id, $dialect_id) .' > '.$new_wordform.'</li>';
            $lemma->deleteWordforms($gramset_id, $dialect_id);
            $lemma->addWordforms($new_wordform, $gramset_id, $dialect_id);
        }
print "</ol>";

    }
    
    public function checkAuthors() {
        $texts = Text::whereNotNull('source_id')
                     ->whereCorpusId(3)
                     ->whereNotIn('id', function ($query) {
                         $query->select('text_id')->from('author_text');
                     })
                     ->whereIn('source_id', function ($q) {
                         $q->select('id')->from('sources')
                           ->where('author', '<>', '')
                           ->orWhere('comment', '<>', '');
                     })->latest('id')->get();
        return view('service.authors', compact('texts'));        
    }    
    
    // проверка на "чужие леммы"
    // select count(*) from label_lemma where lemma_id in (select id from lemmas where lang_id not in (5)) and label_id=3;
    public function multidictSelect() {
/*        $lang_id=5; // livvic
        $dialect_id=44; // New written Livvic
        $label_id = 3; // for multimedia dictionary
        
        $lemmas = Lemma::whereLangId($lang_id)
                       ->whereNotIn('id', function ($q) use ($label_id) {
                           $q->select('lemma_id')->from('label_lemma')
                             ->whereLabelId($label_id);
                       })
                       ->whereIn('id', function ($q) use ($dialect_id) {
                           $q->select('lemma_id')->from('meanings')
                             ->whereIn('id', function ($q2) use ($dialect_id) {
                               $q2->select('meaning_id')->from('meaning_text')
                                  ->whereIn('text_id', function ($q3) use ($dialect_id) {
                                      $q3->select('text_id')->from('dialect_text')
                                         ->whereDialectId($dialect_id);
                                  });
                             });
                       })->get();
        foreach ($lemmas as $lemma) {
//print "<p>".$lemma->id."</p>";            
            $lemma->labels()->attach([$label_id]);
        }               */
        
/*        $examples = DB::table('meaning_text')->whereRelevance(10)->get();
//dd($examples);        
        foreach ($examples as $example) {
            $sentence = Sentence::whereTextId($example->text_id)
                                ->whereSId($example->s_id)->first();
            $fragment = $sentence->fragments()->first();
            if ($fragment) {
                $fragment->w_id = $example->w_id;
                $fragment->save();
            }
            $translation = $sentence->translations()->first();
            if ($translation) {
                $translation->w_id = $example->w_id;
                $translation->save();
            }
        }
        print "done";*/
    }
    
    public function multidictView(Request $request) {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        $lang_id=5; // livvic
        $label_id = 3; // for multimedia dictionary
        $locale = LaravelLocalization::getCurrentLocale();
        $url_args = //Str::urlArgs($request) + 
            ['search_pos'      => (int)$request->input('search_pos'),
             'search_status'   => (int)$request->input('search_status'),
//                'search_lemma'    => $request->input('search_lemma'),
            ];
        $args_by_get = Str::searchValuesByURL($url_args);

        $lemmas = Lemma::selectFromMeaningText()
            ->join('parts_of_speech','parts_of_speech.id','=','lemmas.pos_id')
            ->whereLangId($lang_id)
            ->whereIn('lemmas.id', function ($q) use ($label_id) {
                $q->select('lemma_id')->from('label_lemma')
                  ->whereLabelId($label_id);
            })
            ->groupBy('lemma_id', 'word_id')
            ->latest(DB::raw('count(*)'));

        if ($url_args['search_pos']) {
            $lemmas->wherePosId($url_args['search_pos']);
        } 
        
        if ($url_args['search_status']) {
            $lemmas->whereStatus($url_args['search_status']==1 ? 1 : 0);
        } 
        

//dd(to_sql($lemmas));
        $lemma_coll = $lemmas->get(['lemma', 'lemma_id', 'parts_of_speech.name_'.$locale.' as pos_name', 'status']);
        $lemmas = [];
        foreach ($lemma_coll as $lemma) {
            if (isset($lemmas[$lemma->lemma_id])) {
                $lemmas[$lemma->lemma_id]['frequency'] = 1+$lemmas[$lemma->lemma_id]['frequency'];
                continue;
            }
            $lemmas[$lemma->lemma_id] = [
                'lemma'=>$lemma->lemma, 
                'pos_name'=>$lemma->pos_name, 
                'frequency'=>1, 
                'status'=>$lemma->status];
        }
//        $lemmas=$lemmas->sortByDesc('frequency');
//dd($lemmas);        
        $pos_values = [NULL=>'']+PartOfSpeech::getList();
        
        return view('service.multidict.index',
                compact('lemmas', 'pos_values', 
                        'args_by_get', 'url_args'));
    }
}
