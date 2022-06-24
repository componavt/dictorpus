<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use LaravelLocalization;

//use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

use App\Library\Str;

use App\Models\Dict\Audio;
use App\Models\Dict\Lemma;
use App\Models\Dict\PartOfSpeech;

class DictController extends Controller
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
        $this->middleware('auth:dict.edit,/');
        
        $this->url_args = Str::urlArgs($request) + 
            ['search_pos'      => (int)$request->input('search_pos'),
             'search_status'   => (int)$request->input('search_status'),
//                'search_lemma'    => $request->input('search_lemma'),
            ];
        
        $this->args_by_get = Str::searchValuesByURL($this->url_args);
    }
    
    // отбираем из словаря ливвиковские леммы, у которых нет ещё метки "для мильтимедийного словаря"
    // и слова, для которых есть примеры в корпусе новописьменных текстов
    // 
    // проверка на "чужие леммы", вдруг не то попалось
    // select count(*) from label_lemma where lemma_id in (select id from lemmas where lang_id not in (5)) and label_id=3;
    public function multiSelect() {
        $lang_id=5; // livvic
        $dialect_id=44; // New written Livvic
        $label_id = 3; // for multimedia dictionary
        
/*        $lemmas = Lemma::whereLangId($lang_id)
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
        // update label_lemma set status=1  where lemma_id in (select id from lemmas where status=1);
    }
    
    public function multiView(Request $request) {
        ini_set('max_execution_time', 7200);
        ini_set('memory_limit', '512M');
        $lang_id=5; // livvic
        $label_id = 3; // for multimedia dictionary
        $locale = LaravelLocalization::getCurrentLocale();
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $lemmas = Lemma::whereLangId($lang_id)
                ->whereIn('lemmas.id', function ($q) use ($label_id, $url_args) {
                    $q->select('lemma_id')->from('label_lemma')
                      ->whereLabelId($label_id);
                    if (in_array($url_args['search_status'], [1,2])) {
                        $q->whereStatus($url_args['search_status']==1 ? 1 : 0);
                    } 
                });
        // леммы с метками, отсортированные по частоте употребления в корпусе
/*        $lemmas = Lemma::selectFromMeaningText()
            ->join('parts_of_speech','parts_of_speech.id','=','lemmas.pos_id')
            ->whereLangId($lang_id)
            ->whereIn('lemmas.id', function ($q) use ($label_id, $url_args) {
                $q->select('lemma_id')->from('label_lemma')
                  ->whereLabelId($label_id);
                if ($url_args['search_status']) {
                    $q->whereStatus($url_args['search_status']==1 ? 1 : 0);
                } 
            })
            ->groupBy('lemma_id', 'word_id')
            ->latest(DB::raw('count(*)'));
*/
        if ($url_args['search_pos']) {
            $lemmas->wherePosId($url_args['search_pos']);
        } 
               
        if ($url_args['search_status'] == 3) {
            $lemmas->whereIn('id', function ($q) {
                $q->select('lemma_id')->from('audio_lemma');
            });            
        }
        
        $lemma_coll = $lemmas->get();//['lemma', 'lemma_id', 'parts_of_speech.name_'.$locale.' as pos_name', 'status']);
        $lemmas = [];
        foreach ($lemma_coll as $lemma) {
//dd($lemma);            
            $lemmas[$lemma->id] = [
                'lemma'=>$lemma->lemma, 
                'pos_name'=>$lemma->pos->name, 
                'frequency'=>$lemma->getFrequencyInCorpus(), 
                'audios'=>Audio::getUrlsByLemmaId($lemma->id), 
                'status'=>$lemma->labelStatus($label_id)];
        }
//        $lemmas=$lemmas->sortByDesc('frequency');
//dd($lemmas[10603]);        
        $pos_values = [NULL=>'']+PartOfSpeech::getList();
        
        return view('service.dict.multi.index',
                compact('label_id', 'lemmas', 'pos_values', 
                        'args_by_get', 'url_args'));
    }
    
    public function schoolSelect() {
        /*
        $lang_id=5; // livvic
        $dialect_id=44; // New written Livvic
        $label_id = 4; // for school dictionary
        
        $lemmas = Lemma::whereLangId($lang_id)
                       ->whereNotIn('id', function ($q) use ($label_id) {
                           $q->select('lemma_id')->from('label_lemma')
                             ->whereLabelId($label_id);
                       })->orderBy('id')
                               //->take(10)
                       ->get();
        foreach ($lemmas as $lemma) {
//print "<p>".$lemma->id."</p>";            
            $lemma->labels()->attach([$label_id]);
            foreach ($lemma->meanings as $meaning) {
                $meaning->labels()->attach([$label_id]);
            }
        } */                      
    }
    
    public function schoolView(Request $request) {
//        ini_set('max_execution_time', 7200);
//        ini_set('memory_limit', '512M');
        $lang_id=5; // livvic
        $label_id = 4; // for school dictionary
        $args_by_get = $this->args_by_get;
        $url_args = $this->url_args;

        $lemmas = Lemma::whereLangId($lang_id)
            ->whereIn('id', function ($q) use ($label_id, $url_args) {
                $q->select('lemma_id')->from('label_lemma')
                  ->whereLabelId($label_id);
                if (in_array($url_args['search_status'], [1,2])) {
                    $q->whereStatus($url_args['search_status']==1 ? 1 : 0);
                } 
            })
//            ->orderBy('lemma');
            ->orderByRaw('lower(lemma)');

        if ($url_args['search_pos']) {
            $lemmas->wherePosId($url_args['search_pos']);
        } 
        
        if ($url_args['search_status'] == 3) {
            $lemmas->whereIn('id', function ($q) {
                $q->select('lemma_id')->from('audio_lemma');
            });            
        }
        
        $numAll = $lemmas->count();
        $lemmas = $lemmas->paginate($url_args['limit_num']);
        $pos_values = [NULL=>'']+PartOfSpeech::getList();
        
        return view('service.dict.school.index',
                compact('label_id', 'lemmas', 'numAll', 'pos_values', 
                        'args_by_get', 'url_args'));
    }
}
