<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Library\Correct;

use App\Models\Corpus\Text;
use App\Models\Corpus\Transtext;
use App\Models\Corpus\Word;

use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;
use App\Models\Dict\Wordform;

class CorrectController extends Controller
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
            $langs[$l_id]=Lang::getNameById($l_id);
        }        
        $count_lemmas_without_wordform_total = Lemma::whereNull('wordform_total')->count();
        
        return view('service.correct', compact('langs', 'count_lemmas_without_wordform_total'));        
    }
    
// select lemma_id, gramset_id, dialect_id, count(*) as count from lemma_wordform where gramset_id=3 group by  lemma_id, gramset_id, dialect_id having count>1;   
    public function addAccusatives() {
        $nom_sg_pos = 1;
        $acc_sg_pos = 56;
        $gen_sg_pos = 3;
        $nom_pl_pos = 2;
        $acc_pl_pos = 57;
        $lang_id=4;
        $lemmas = Lemma::whereLangId($lang_id)->whereIn('pos_id', PartOfSpeech::getNameIDs())->orderBy('lemma')->get();
        foreach ($lemmas as $lemma) {            
            foreach ($lemma->wordforms()->wherePivot('gramset_id',$nom_sg_pos)->get() as $nom_sg) {
                $dialect_id = $nom_sg->pivot->dialect_id;
                if (!$lemma->wordforms()->wherePivot('wordform_id',$nom_sg->id)->wherePivot('gramset_id',$acc_sg_pos)->wherePivot('dialect_id', $dialect_id)->count()) {
                    $lemma->wordforms()->attach($nom_sg->id, ['gramset_id'=>$acc_sg_pos, 'dialect_id'=>$dialect_id]);
print '<p><a href="/dict/lemma/'.$lemma->id.'">'.$lemma->lemma."</a></p>";                    
                }
                $gen_sg = $lemma->wordforms()->wherePivot('gramset_id',$gen_sg_pos)->wherePivot('dialect_id', $dialect_id)->first();
                if ($gen_sg && !$lemma->wordforms()->wherePivot('wordform_id',$gen_sg->id)->wherePivot('gramset_id',$acc_sg_pos)->wherePivot('dialect_id', $dialect_id)->count()) {
                    $lemma->wordforms()->attach($gen_sg->id, ['gramset_id'=>$acc_sg_pos, 'dialect_id'=>$dialect_id]);
                } 
            }
            foreach ($lemma->wordforms()->wherePivot('gramset_id',$nom_pl_pos)->get() as $nom_pl) {
                $dialect_id = $nom_sg->pivot->dialect_id;
                if (!$lemma->wordforms()->wherePivot('wordform_id',$nom_pl->id)->wherePivot('gramset_id',$acc_pl_pos)->wherePivot('dialect_id', $dialect_id)->count()) {
                    $lemma->wordforms()->attach($nom_pl->id, ['gramset_id'=>$acc_pl_pos, 'dialect_id'=>$dialect_id]);
                }
            }
        }
    }

    function addWordformAffixes(Request $request) {
        $lang_id = (int)$request->input('search_lang');
//dd($lang_id);        
        if ($lang_id) {
            Correct::addWordformAffixesForLang($lang_id);
            return;
        }
/*
        foreach (Lang::projectLangIDs() as $lang_id) {
            Correct::addWordformAffixesForLang($lang_id);
        }      */
        
        $langs = [];
        foreach (Lang::projectLangIDs() as $l_id) {
            $langs[$l_id]['name']=Lang::getNameById($l_id);
            $langs[$l_id]['affix_count'] = number_format(Wordform::countWithoutAffixes($l_id), 0, ',', ' ');
            $langs[$l_id]['wrong_affix_count'] = number_format(Wordform::countWrongAffixes($l_id), 0, ',', ' ');
        }
        
        return view('service.add_wordform_affixes', compact('langs'));
    }
    
    /**
     * Search words of texts, 
     * Ищем неразмеченные слова в текстах, добавляем связи и устававливаем  words.checked=1
     * 
     * update words set checked=0;
     * select count(*) from words where checked=0 and text_id in (select id from texts where lang_id=1) and id not in (select word_id from meaning_text);
     * 
     * @param Request $request
     */
    public function addMeaningTextLinks(Request $request) {
        $lang_id = (int)$request->input('search_lang');
        
        if (!$lang_id) {
            $langs = [];
            foreach (Lang::projectLangIDs() as $l_id) {
                $langs[$l_id]['name']=Lang::getNameById($l_id);
                $langs[$l_id]['unmarked_words_count'] = number_format(Word::countUnmarked($l_id), 0, ',', ' ');
            }
            return view('service.add_unmarked_links', compact('langs'));
        }

        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        
        $word_groups = Word::whereChecked(0)
                        ->whereIn('text_id', function ($q) use ($lang_id) {
                            $q->select('id')->from('texts')->where('lang_id',$lang_id);
                       })->whereNotIn('id', function ($query) {
                            $query->select('word_id')->from('meaning_text');
                })->groupBy('word')
                //->take(10)
                ->get(['word']);  
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
    print "<p>text=".$word->text_id.", s_id=".$word->s_id.", w_id=".$word->w_id.", word=".$word->word. ', meaning links = <span style="color: red">'. $num_links. '</span>';            
                }
                $word->checked=1;
                $word->save();
            }
        }
    }

    public function addTextWordformLinks(Request $request) {
//exit(0);
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '2048M');
        $text_id = (int)$request->text_id;
        if ($text_id) {
            $text = Text::find($text_id);
            if (!$text) { return; }
            
            $text->updateWordformLinks();
print '<p>'.$text->id.'</p>';  
            return;          
        } 
exit(0);        
        $texts=Text::
              where('id', '>', 2480)
              ->where('id', '<', 2665)
              ->whereNotIn('id',[1714, 2000, 2037, 2038, 2041, 2048, 2060, 2061, 2079, 2080, 2081, 2082, 2083, 2084, 2092, 2216, 2243, 2363, 2540, 2541, 2573, 2584, 2587, 2617, 2941, 2944, 2950])
//              where('id', 2980)
              ->orderBy('id')
              //->take(50)
              ->get();
        foreach ($texts as $text) { 
            $text->updateWordformLinks();
print '<p>'.$text->id.'</p>';            
        }
    }
    
    public function calculateLemmaWordforms() {
        Correct::calculateLemmaWordforms();        
    }

    public function checkParallelTexts() {
        $texts = Text::whereNotNull('transtext_id')->orderBy('id')->get();
//dd($texts);       
        foreach ($texts as $text) {
//dd($text->id);            
            list($sxe,$error_message) = Text::toXML($text->text_xml, $text->id);
//dd($text->text_xml);  
//dd($sxe);            
            if ($error_message) { return 'Text XML parsing for '.$text->id. ': '.$error_message; } 
            
            $trans_text = Transtext::find($text->transtext_id);
            if (!$trans_text) {return 'Transtext finding error.';}
            
            list($sxe_trans, $error_message) = Text::toXML($trans_text->text_xml, $trans_text->id);
            if ($error_message) { return 'Transtext XML parsing for '.$text->id. ': '.$error_message; } 
            
            $last_sent = $sxe->xpath('/*/s[last()]');
//dd($last_sent);            
            $last_id = (int)$last_sent[0]->attributes()->id;
//dd($text_sent);            
            
            $last_sent_trans = $sxe_trans->xpath('/*/s[last()]');
            $last_id_trans = (int)$last_sent_trans[0]->attributes()->id;
            
            if ($last_id != $last_id_trans) {
                print "<p><a href=\"/ru/corpus/text/".$text->id."\">".$text->title."</a> ($last_id, $last_id_trans)</p>";
            }
        }
    }
    
    /**
     * 
     * @param Request $request
     */
    public function generateWordforms(Request $request) {
        $lang_id = (int)$request->input('search_lang');

        if (!$lang_id) {
            $pos_list = ['NOUN'=>'существительные', 'ADJ'=>'прилагательные'];
            $counts = Correct::countLemmaWordforms(5);
            return view('service.generate_wordforms', compact('pos_list', 'counts'));
        }
        
        Correct::generateWordforms($lang_id, 
                $request->input('search_pos'), (int)$request->input('w_count'));
    }

    /**
     * Создать минимальный набор словоформ. 
     * Если у изменяемой леммы нет основ, создаем основу 0 и генерируем леммы.
     * 
     * количество изменяемых лемм, у которых нет нулевой основы
     * select count(*) from lemmas where pos_id in (select pos_id from gramset_pos) and id not in (select lemma_id from lemma_bases where base_n=0);
     * количество глаголов наших языков, у которых нет словоформы-инфинитива
     * select count(*) from `lemmas` where `lang_id` in (4,5,1,6) and (`id` not in (select `id` from `lemma_features`) or `id` in (select `id` from `lemma_features` where (`without_gram` is null or `without_gram` <> 1) and `number` <> 1)) and `pos_id` in (11) and `id` not in (select `lemma_id` from `lemma_wordform` where `gramset_id` = 170);
     * количество глаголов наших языков (изменяемых), у которых нет нулевой основы
     * select count(*) from lemmas where pos_id=11 and id not in (select lemma_id from lemma_wordform where gramset_id=170) and (id not in (select id from lemma_features) or id in (select id from lemma_features where without_gram  is null or without_gram <> 1)) and lang_id in (1,4,5,6);
     * количество именных изменяемых лемм наших языков (не plural singular), у которых нет номинатива ед.ч.
     * select count(*) from `lemmas` where `lang_id` in (4,5,1,6) and `id` not in (261, 827, 866) and (`id` not in (select `id` from `lemma_features`) or `id` in (select `id` from `lemma_features` where (`without_gram` is null or `without_gram` <> 1) and `number` <> 1)) and `pos_id` in (1,5,6,10,13,14,20) and `id` not in (select `lemma_id` from `lemma_wordform` where `gramset_id` = 2);
     * 
     * select lemma_id from meanings where id in (select meaning_id from dialect_meaning) and lemma_id in (select lemma_id from lemma_wordform where dialect_id=43 and wordform_id=170) and lemma_id not in (select lemma_id from lemma_wordform where dialect_id=43 and wordform_id<>170);
     * select lemma_id, count(*) from lemma_wordform where dialect_id=43 and lemma_id in (select lemma_id from meanings where id in (select meaning_id from dialect_meaning)) and lemma_id in (select id from lemmas where pos_id=11) group by lemma_id having count(*)<99;

     */
    public function createInitialWordforms() {
/*        $dialect_id=44;//46,43
//        $pos_id=11;
        $pos_ids= PartOfSpeech::getNameIDs();
        $count=2;
        $lemmas = LemmaWordform::selectRaw("lemma_id, count(*)")
                               ->where('dialect_id', $dialect_id)
                               ->whereIn('lemma_id', function ($q) {
                                   $q->select('lemma_id')->from('meanings')
                                     ->whereIn('id',function($q1) {
                                        $q1->select('meaning_id')->from('dialect_meaning');                                         
                                     });
                               })
                               ->whereIn('lemma_id', function ($q) use ($pos_ids) {
                                   $q->select('id')->from('lemmas')
                                     ->whereIn('pos_id',$pos_ids);                                         
                               })
                               ->groupBy(DB::raw('lemma_id having count(*)='.$count))
                               ->take(1)
                               ->get();
        foreach ($lemmas as $lemma) {
//                print '<p><a href="/ru/dict/lemma/'.$lemma->lemma_id.'">'.$lemma->lemma_id.'</a></p>';
            DB::statement("DELETE FROM lemma_wordform where lemma_id=". $lemma->lemma_id. " and dialect_id=".$dialect_id);
            $lemma_obj=Lemma::find($lemma->lemma_id);
            $lemma_obj->createInitialWordforms();
            print '<p><a href="/ru/dict/lemma/'.$lemma_obj->id.'">'.$lemma_obj->id.'</a></p>';
        }
        
*/
        $is_all_checked = false;
        $langs = [4,5,1,6];
//        $pos=[1,5,6,10,13,14,20];
        $pos=[11];
//        $gramset_id=1;
//        $gramset_id=2;
        $gramset_id=170;
        while (!$is_all_checked) {
            // verbs and not plural numerals
            $lemmas = Lemma::whereIn('lang_id', $langs)
                           ->where(function($query) {
                               $query->whereNotIn('id', function($q) {
                                  $q->select('id')->from('lemma_features');                                    
                               })->orWhereIn('id', function($q) {
                                  $q->select('id')->from('lemma_features')
                                    ->where(function ($q1) {
                                        $q1->whereNull('without_gram')
                                        ->orWhere('without_gram', '<>', 1);                                                                            
                                    });
                                    //->where('number', '<>', 1);
                               });
                           })
                           ->whereIn('pos_id', $pos)
                           ->whereNotIn('id', function($query) use ($gramset_id) {
                                $query->select("lemma_id")->from("lemma_wordform")
                                      ->whereGramsetId($gramset_id);
                            })->take(10);
//dd($lemmas->count());                            
            // plural numerals
//select count(*) from `lemmas` where `lang_id` in (4,5,1,6) and `id` in (select `id` from `lemma_features` where `number` = 1) and `pos_id` in (1,5,6,10,13,14,20) and `id` not in (select `lemma_id` from `lemma_wordform` where `gramset_id` = 2);            
/*            $lemmas = Lemma::whereIn('lang_id', $langs)
//                           ->whereNotIn('id',[261, 827, 866]) 
                           ->whereIn('id', function($q) {
                                $q->select('id')->from('lemma_features')
                                  ->where('number', 1);
                           })
                           ->whereIn('pos_id', $pos)
                            ->whereNotIn('id', function($query) use ($gramset_id) {
                                $query->select("lemma_id")->from("lemma_wordform")
                                      ->whereGramsetId($gramset_id);
                            })->take(1);*/
//dd($lemmas->toSql());                            
            if (!$lemmas->count()) {
                $is_all_checked = true;
            }
            foreach ($lemmas->get() as $lemma) {
    //            print '<p><a href="/ru/dict/lemma/'.$lemma->id.'">'.$lemma->lemma.'</a></p>';
                $lemma->createInitialWordforms();
                print '<p><a href="/ru/dict/lemma/'.$lemma->id.'">'.$lemma->lemma.'</a></p>';
            }
//exit(0);            
        }        
    }  
    
    public function moveCharOutWord() {
        $chars = ['…'];
        foreach ($chars as $char) {
            Correct::moveCharOutWord($char);
        }
    }
}
