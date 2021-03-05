<?php

namespace App\Http\Controllers\Library;

//use Illuminate\Http\Request;

//use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Charts\DistributionChart;

use App\Models\User;
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
    public function index()
    {
        $total_users = User::count(); 
        $total_active_editors = User::countActiveEditors(); 
        return view('stats.index')
                ->with(['total_users' => number_format($total_users, 0, ',', ' '),
                        'total_active_editors' => number_format($total_active_editors, 0, ',', ' ')]);
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
                       ]);
    }    
    
    public function byCorp()
    {
        $total_texts = Text::count();
        $total_informants = Informant::count();
        $total_places = Place::count();
        $total_recorders = Recorder::count();
        
        $total_words = Word::count(); 
        $total_marked_words = Word::countMarked();
        $marked_words_to_all = 100*$total_marked_words/$total_words;
        $lang_marked = Lang::countMarked();
        
        $total_checked_examples = Text::countCheckedExamples();
        $total_checked_words = Text::countCheckedWords(); 
        $checked_words_to_marked = 100*$total_checked_words/$total_marked_words;
        
        $total_examples = Text::countExamples();
        $checked_examples_to_all = 100*$total_checked_examples/$total_examples;
                
        return view('stats.by_corp')
                ->with([
                        'checked_examples_to_all' => number_format($checked_examples_to_all, 2,',', ' '),
                        'checked_words_to_marked' => number_format($checked_words_to_marked, 2,',', ' '),
                        'lang_marked' => $lang_marked,
                        'marked_words_to_all' => number_format($marked_words_to_all, 1,',', ' '),
                        'total_checked_examples' => number_format($total_checked_examples, 0, ',', ' '),
                        'total_checked_words' => number_format($total_checked_words, 0, ',', ' '),
                        'total_examples' => number_format($total_examples, 0, ',', ' '),
                        'total_informants' => number_format($total_informants, 0, ',', ' '),
                        'total_places' => number_format($total_places, 0, ',', ' '),
                        'total_recorders' => number_format($total_recorders, 0, ',', ' '),
                        'total_texts' => number_format($total_texts, 0, ',', ' '),
                        'total_words' => number_format($total_words, 0, ',', ' '),
                        'total_marked_words' => number_format($total_marked_words, 0, ',', ' '),
                       ]);
    }    
    
    public function byGenre() {
        $lang_genres = Genre::countTextsByIDGroupByLang();     
//dd($lang_genres);        
        $chart = new DistributionChart;
        $colors = $chart->colors();
        $count = 0;
        $genre_langs=[];
        foreach ($lang_genres as $lang_name=>$genre_num) {
//var_dump($lang_name,$corpuses);            
//print "<br>";
            if ($count==0) {
                $chart->labels(array_keys($genre_num));                
            }
//            $chart->dataset($lang_name, 'horizontalBar', array_values(array_map(function($v){return preg_replace('/\s/','',$v)/1000;},$corpuse_num)))
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
}
