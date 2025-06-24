<?php

namespace App\Http\Controllers\Library;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Venturecraft\Revisionable\Revision;

//use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Charts\DistributionChart;
use App\Charts\LemmaNumByLang;

use App\Models\User;
use App\Models\Corpus\Corpus;
use App\Models\Corpus\Genre;
use App\Models\Corpus\Informant;
use App\Models\Corpus\Place;
use App\Models\Corpus\Recorder;
use App\Models\Corpus\Text;
use App\Models\Corpus\Word;
use App\Models\Dict\Lang;
use App\Models\Dict\Lemma;
use App\Models\Dict\Meaning;
use App\Models\Dict\LemmaWordform;

class StatsController extends Controller
{
     /**
     * Instantiate a new new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        // permission= corpus.edit, redirect failed users to /corpus/text/, authorized actions list:
        $this->middleware('auth:corpus.edit,/', 
                         ['only' => ['byEditors', 'byCorpMarkup']]);
    }
    
    public function index()
    {
        return view('stats.index');
    }
    
    public function byUser()
    {
        $total_users = number_format(User::count(), 0, ',', ' '); 
        $total_editors = number_format(User::whereIn('id', function ($q) {
                            $q->select('user_id')->from('role_users')
                              ->whereIn('role_id', [1,4]);
                        })->count(), 0, ',', ' '); 
        $total_active_editors = number_format(User::countActiveEditors(), 0, ',', ' '); 
        return view('stats.by_user', 
                compact('total_active_editors', 'total_editors', 'total_users'));
    }
    
    public function byEditors()
    {
/*        $users = User::whereIn('id', function ($q) {
                    $q->select('user_id')->from('revisions');
                })
//                ->limit(1)
                ->get();
        $editors = collect();
        foreach ($users as $user) {
            $history = DB::table('revisions')
                         ->where('user_id',$user->id)
                         ->orderBy('updated_at','desc');
            $user->count = number_format($history->count(), 0, ',', ' ');
            $user->last_time = $history->first()->updated_at;
            $editors->push($user);
        }
        $editors->sortByDesc('last_time');*/
        
/*        // Получаем агрегированные данные по revisions: группируем по user_id
        $revisionStats = collect(DB::table('revisions')
            ->select('user_id', DB::raw('COUNT(*) as revisions_count'), DB::raw('MAX(updated_at) as last_time'))
            ->groupBy('user_id')
            ->get())
            ->keyBy('user_id'); // для быстрого доступа по user_id
//dd(get_class($revisionStats));
        // Получаем пользователей, участвующих в revisions
        $userIds = $revisionStats->keys();
        $users = User::whereIn('id', $userIds)->get();

        // Собираем коллекцию редакторов
        $editors = $users->map(function ($user) use ($revisionStats) {
            $stats = $revisionStats[$user->id];
            $user->count = number_format($stats->revisions_count, 0, ',', ' ');
            $user->last_time = $stats->last_time;
            return $user;
        })->sortByDesc('last_time');    */
        
        // Получаем агрегированные данные по ревизиям
        $revisionStats = Revision::query()
            ->select('user_id', DB::raw('COUNT(*) as revisions_count'), DB::raw('MAX(updated_at) as last_time'))
            ->whereNotNull('user_id') // на всякий случай, если есть null
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        // Получаем всех пользователей, которые что-то редактировали
        $users = User::whereIn('id', $revisionStats->keys())->get();

        // Собираем итоговую коллекцию
        $editors = $users->map(function ($user) use ($revisionStats) {
            $stats = $revisionStats[$user->id];
            $user->count = number_format($stats->revisions_count, 0, ',', ' ');
            $user->last_time = $stats->last_time;
            return $user;
        })->sortByDesc('last_time');
        //dd($editors);        
        return view('stats.by_editors', 
                compact('editors'));
    }
    
    public function byEditor(User $user, Request $request)
    {
        $models = [
            'App\Models\Dict\Lemma'  => 'лемм',
            'App\Models\Dict\Wordform' => 'словоформ',
            'App\Models\Dict\Audio'  => 'аудиозаписей лемм',
            'App\Models\Corpus\Text' => 'текстов',
            'App\Models\Corpus\AudioText' => 'аудиозаписей текстов',
        ];
        // Проверка дат
        $min_date = $request->input('min_date');
        $max_date = $request->input('max_date');

        if (empty($min_date) || empty($max_date)) {
            $rec = Revision::where('user_id',$user->id)
                     ->selectRaw('min(created_at) as min, max(created_at) as max')
                     ->first();
            
            if (!$rec) { return;}
            
            $min_date = Carbon::parse($rec->min)->toDateString();
            $max_date = Carbon::parse($rec->max)->toDateString();
        }

        $minDate = Carbon::parse($min_date)->startOfDay();
        $maxDate = Carbon::parse($max_date)->endOfDay();
        
        $history_created = Revision::where('user_id',$user->id)
                     ->whereBetween('updated_at', [$minDate, $maxDate])
                     ->where('key', 'created_at')
                     ->whereIn('revisionable_type', array_keys($models))
                     ->groupBy('revisionable_type')
//                     ->orderBy('revisionable_type')
                     ->select('revisionable_type', DB::raw('count(*) as count'))
                     ->get()
                     ->keyBy('revisionable_type');

        $history_updated = [];

        foreach ($models as $modelClass => $label) {
            $count = Revision::where('user_id', $user->id)
                ->whereBetween('updated_at', [$minDate, $maxDate])
                ->where('key', '<>', 'created_at')
                ->where('revisionable_type', $modelClass)
                ->distinct('revisionable_id')
                ->count('revisionable_id'); // только уникальные ID

            $history_updated[$label] = $count;
        }   
        
        $quarter_query = '?min_date='.Carbon::now()->startOfQuarter()->format('Y-m-d')
                       . '&max_date='. Carbon::now()->format('Y-m-d');
        $year_query = '?min_date='.Carbon::now()->startOfYear()->format('Y-m-d')
                       . '&max_date='. Carbon::now()->format('Y-m-d');
        
        
        return view('stats.by_editor', 
                compact('history_created', 'history_updated', 'max_date', 
                        'min_date', 'models', 'quarter_query', 'year_query', 'user'));
    }
    
    public function byDict()
    {
        $total_lemmas = Lemma::count();
        $lang_lemmas = Lang::countLemmas();
        
        $total_wordforms = LemmaWordform::count();
        $lang_wordforms = Lang::countWordforms();
        
        $total_meanings = Meaning::count();
        $total_relations = Meaning::countRelations();
        $total_translations = Meaning::countTranslations();
        
        $total_examples = Text::countExamples();
        $total_checked_examples = Text::countCheckedExamples();
        $checked_examples_to_all = 100*$total_checked_examples/$total_examples;
                
        $chart = new LemmaNumByLang;
        $chart->labels(array_keys($lang_lemmas))
//                ->yAxisTitle('ggggggg')
//                ->title('9999999999')
//                ->options(['scales' =>     LemmaNumByLang::chartSetAxes('Date format(DD-MM)','Hours in (24) time format')])
                ;
        $chart->dataset(\Lang::trans('stats.chart_LemmaNumByLang'), 'horizontalBar', array_map(function($v){return preg_replace('/\s/','',$v)/1000;}, array_values($lang_lemmas)))
              ->color('#ff0000')
/*              ->options(['scales' => [
                    "xAxes" => [
                        "scaleLabel" => [
                            "display" => true,
                            "labelString" => "Time in Seconds",
                            "fontColor" => "red"
                        ]
                    ]]])*/
//              ->fill(false) для графика 'line' убрать заливку
              ->backgroundColor('#ff0000');
        $chart->dataset(\Lang::trans('stats.chart_WordformNumByLang'), 'horizontalBar', array_map(function($v){return preg_replace('/\s/','',$v)/1000;},array_values($lang_wordforms)))
              ->color('#00ff00')
              ->backgroundColor('#00ff00');
//dd($chart);
        return view('stats.by_dict')
                ->with(['chart' => $chart, 
                        'lang_lemmas' => $lang_lemmas,
                        'lang_wordforms' => $lang_wordforms,
                        'total_lemmas' => number_format($total_lemmas, 0, ',', ' '),
                        'total_meanings' => number_format($total_meanings, 0, ',', ' '),
                        'total_relations' => number_format($total_relations, 0, ',', ' '),
                        'total_translations' => number_format($total_translations, 0, ',', ' '),
                        'total_wordforms' => number_format($total_wordforms, 0, ',', ' '),
                        'total_examples' => number_format($total_examples, 0, ',', ' '),
                        'total_checked_examples' => number_format($total_checked_examples, 0, ',', ' '),
                        'checked_examples_to_all' => number_format($checked_examples_to_all, 2,',', ' '),
                       ]);
    }    
    
    public function byCorp()
    {
        $total_texts = Text::count();
        $total_informants = Informant::count();
        $total_places = Place::count();
        $total_recorders = Recorder::count();
        
        return view('stats.by_corp')
                ->with([
                        'total_informants' => number_format($total_informants, 0, ',', ' '),
                        'total_places' => number_format($total_places, 0, ',', ' '),
                        'total_recorders' => number_format($total_recorders, 0, ',', ' '),
                        'total_texts' => number_format($total_texts, 0, ',', ' '),
                       ]);
    }    
    
    public function byCorpMarkup()
    {
        $total_words = Word::count(); 
        $total_marked_words = Word::countMarked();
        $marked_words_to_all = 100*$total_marked_words/$total_words;
        $lang_marked = Lang::countWords();
        
        $total_checked_words = Text::countCheckedWords(); 
        $checked_words_to_marked = 100*$total_checked_words/$total_marked_words;
        
        return view('stats.by_corp_markup')
                ->with([
                        'checked_words_to_marked' => number_format($checked_words_to_marked, 2,',', ' '),
                        'lang_marked' => $lang_marked,
                        'marked_words_to_all' => number_format($marked_words_to_all, 1,',', ' '),
                        'total_checked_words' => number_format($total_checked_words, 0, ',', ' '),
                        'total_words' => number_format($total_words, 0, ',', ' '),
                        'total_marked_words' => number_format($total_marked_words, 0, ',', ' '),
                       ]);
    }    
    
    public function byGenre() {
        $lang_genres = Genre::countTextsByIDGroupByLang();     

        $chart = new DistributionChart;
        $colors = $chart->colors();
        $count = 0;
        $genre_langs=[];
//dd($lang_genres);        
        foreach ($lang_genres as $lang_name=>$genre_num) {
            if ($count==0) {
                $chart->labels(array_keys($genre_num));                
            }
            $chart->dataset($lang_name, 'horizontalBar', array_values($genre_num))
                  ->color('#'.$colors[$count])
                  ->backgroundColor('#'.$colors[$count++]);
            foreach ($genre_num as $genre_name=>$num) {
                $genre_langs[$genre_name][$lang_name] = $num;
            }
        }
//dd($genre_langs);
        return view('stats.by_genre',
                    compact('chart', 'lang_genres', 'genre_langs'));
    }    
    
    public function byCorpus() {
        $lang_corpuses = Corpus::countTextsByIDGroupByLang();     
        
        $chart = new DistributionChart;
        $colors = $chart->colors();
        $count = 0;
        $corpus_langs=[];
        foreach ($lang_corpuses as $lang_name=>$corpus_num) {
            if ($count==0) {
                $chart->labels(array_keys($corpus_num));                
            }
//            $chart->dataset($lang_name, 'horizontalBar', array_values(array_map(function($v){return preg_replace('/\s/','',$v)/1000;},$corpuse_num)))
            $chart->dataset($lang_name, 'horizontalBar', array_values($corpus_num))
                  ->color('#'.$colors[$count])
                  ->backgroundColor('#'.$colors[$count++]);
            foreach ($corpus_num as $corpus_name=>$num) {
                $corpus_langs[$corpus_name][$lang_name] = $num;
            }
        }
//dd($genre_langs);
        return view('stats.by_corpus',
                    compact('chart', 'lang_corpuses', 'corpus_langs'));
    }    
    
    public function byYear() {
        $label_years = Text::countTextsByYears();
        $chart = new DistributionChart;
        $chart->options(['scales'=> ['xAxes' => ['ticks' => ['max' => 2030]]]]);
        $colors = $chart->colors();
        $count = 0;
        $text_years=[];
        foreach ($label_years as $year_label=>$year_num) {
            if ($count==0) {
                $chart->labels(array_keys($year_num));                
            }
            $chart->dataset($year_label, 'bar', array_values($year_num))
                  ->color('#'.$colors[$count])
                  ->backgroundColor('#'.$colors[$count++]);
            foreach ($year_num as $year=>$num) {
                $text_years[$year][$year_label] = $num;
            }
        }
//dd($genre_langs);
        return view('stats.by_year',
                    compact('chart', 'text_years', 'label_years'));
    }    
}
