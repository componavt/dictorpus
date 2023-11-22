<?php

namespace App\Http\Controllers\Library\Experiments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

//use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Charts\DistributionChart;

use App\Library\Experiments\DialectDmarker;
use App\Library\Experiments\Dmarker;
use App\Library\Experiments\Mvariant;

use App\Models\Corpus\Text;

use App\Models\Dict\Dialect;

class DialectDmarkerController extends Controller
{
    public function __construct(Request $request)
    {
        // permission= corpus.edit, redirect failed users to /corpus/text/, authorized actions list:
        $this->middleware('auth:corpus.edit,/', 
                         ['only' => ['calculate', 'calculateСoalitions', 'calculateSSindex']]);
    }
    /**
     * /experiments/dialect_dmarker/
     * 
     */
    public function index() {
        $output = 'frequency';
        list($dialects, $dmarkers, $gr_dialects) = DialectDmarker::init($output);
        
        return view('experiments.dialect_dmarker.index', 
                compact('dialects', 'dmarkers', 'gr_dialects', 'output'));
    }
    
    public function fractions() {
        $output = 'fraction';
        list($dialects, $dmarkers, $gr_dialects) = DialectDmarker::init($output);
        
        return view('experiments.dialect_dmarker.index', 
                compact('dialects', 'dmarkers', 'gr_dialects', 'output'));
    }
    
    public function words() {
        $output = 'words';
        list($dialects, $dmarkers, $gr_dialects) = DialectDmarker::init($output);
        
        return view('experiments.dialect_dmarker.index', 
                compact('dialects', 'dmarkers', 'gr_dialects', 'output'));
    }
    
    public function calculate() {
        DB::statement("DELETE FROM dialect_dmarker");
        $mvariants = Mvariant::orderBy('id')
//                     ->whereIn('id', [29,35,37,49])
//                     ->where('dmarker_id', '>', 5)
//                     ->where('dmarker_id', '<', 6)
/*                     ->whereIn('dmarker_id', function ($q) {
                         $q->select('id')->from('dmarkers')
                           ->where('absence', 0);
                     })*/
                     ->get();
        
        $dialects = Dialect::whereIn('lang_id', [4,5,6])
                    ->whereIn('id', function ($query) {
                        $query->select('dialect_id')
                        ->from('dialect_text');
                    })->get();
                    
        foreach ($dialects as $dialect) {
            $total_texts = $dialect->totalTexts();
            $total_words = $dialect->totalWords();
print "<h3>". $dialect->name. ", $total_texts / $total_words</h3>";          
            foreach ($mvariants as $mvariant) {
print "<p>". $mvariant->dmarker_id. ". ". $mvariant->name. "</p>";          
                $mvariant->calculateFrequencyAndFraction($dialect->id, $total_texts, $total_words);
//exit(0);                
            }
        }
print 'done.';        
    }
    
    public function calculateСoalitions() {
        ini_set('max_execution_time', 100000);
//        ini_set('memory_limit', '512M');
                
        $win_coef = 0.75;
        $players_num = 20;
        $dialects = Dialect::whereIn('lang_id', [4,5,6])
                    ->where('id', '>', 46)
                    ->whereIn('id', function ($query) {
                        $query->select('dialect_id')
                        ->from('dialect_text');
                    })->orderBy('id')
//                    ->take(1)
                    ->get();
        foreach ($dialects as $dialect) {
            DialectDmarker::createCoalitions($dialect->id, $win_coef, $players_num);        
        }
    }
    
    public function calculateSSindex() {
        $coalitions_num = 10;
        $players_num = 20;
        $dialects = DB::table('coalition_dialect')->groupBy('dialect_id')
                      ->orderBy('dialect_id')
                      ->where('dialect_id', '>', 30)
//                      ->where('dialect_id', '<', 32)
                      ->get();//pluck('dialect_id')->toArray();
        foreach ($dialects as $rec) {
            DialectDmarker::calculateSSindex($rec->dialect_id, $coalitions_num, $players_num);                
        }
print 'done';        
    }
    
    public function compareFreqSSindex() {
        $dialect_markers=$charts=[];
        $dialects = Dialect::whereIn('lang_id', [4,5,6])
                    ->whereIn('id', function ($query) {
                        $query->select('dialect_id')
                        ->from('dialect_dmarker')
                        ->whereNotNull('SSindex');
                    })->orderBy('id')
                    ->get();
        $dmarkers = Dmarker::orderBy('id')->get();
        foreach ($dialects as $dialect) {        
            $labels = $dataset = [];
            foreach ($dmarkers as $marker) {
                foreach ( $marker->mvariants as $variant ) {
                    $d = $variant->dialects()->where('dialect_id', $dialect->id)->first();
                    $dialect_markers[$dialect->name][$marker->id .'. '. $marker->name][$variant->id]
                           =['name'=>$variant->name,
                             'w_fraction'=>$d ? round($d->pivot->w_fraction, 4): '',
                             'SSindex'=>$d ? round($d->pivot->SSindex, 4): ''];
                    if ($d && $d->pivot->SSindex) {
                        $labels[] = $variant->id;
                        $dataset[0][] = round($d->pivot->w_fraction, 4);
                        $dataset[1][] = round($d->pivot->SSindex, 4);
                    }
                }
            }
            $chart = new DistributionChart;
            $colors = $chart->colors();
            $chart->labels($labels);
            $chart->dataset('Относит. частота', 'line', $dataset[0])->fill(false)
                  ->color('#'.$colors[0])->backgroundColor('#'.$colors[0]);
            $chart->dataset('Индекс Шепли-Шубика', 'line', $dataset[1])->fill(false)
                  ->color('#'.$colors[1])->backgroundColor('#'.$colors[1]);
            $charts[$dialect->name] = $chart;
        } 

        return view('experiments.dialect_dmarker.compare_freq_SSindex', 
                compact('charts', 'dialect_markers'));        
    }
    
    public function example() {
        $lang_id = 4;
        $limit = 20;
        
        $d_fractions = []; // частоты диалектов
        $right = $wrong = 0;
        
        $mvariants = Mvariant::orderBy('id')->get();

        $dialects = Dialect::whereIn('id', function ($q) {
            $q->select('dialect_id')->from('dialect_dmarker');
        })->orderBy('id')->get();

        foreach ($dialects as $dialect) {
            $d_fractions[$dialect->id] = DialectDmarker::whereDialectId($dialect->id)
                    ->orderBy('mvariant_id')->pluck('w_fraction', 'mvariant_id')
                    ->toArray();
        }
        
        $has_more_one_dialects = DB::table('dialect_text')->groupBy('text_id')
                ->having(DB::raw('count(*)'), '>', 1)
                ->pluck('text_id');

        $texts = Text::whereLangId($lang_id)
                    ->whereNotIn('id', $has_more_one_dialects)
                    ->whereIn('id', function ($q) {
                        $q->select('text_id')->from('dialect_text');
                    })->orderBy('id')->get();
dd(sizeof($texts));        
        foreach ($texts as $text) {
            $fractions = DialectDmarker::getWFractions($text, $mvariants);
/*        
        arsort($w_fractions); // сортируем по убыванию частот
        $w_fractions = array_slice($w_fractions, 0, $limit, true); // оставляем первые 20
        $w_fract = array_filter($w_fractions, function($element) { // удаляем пустые элементы
            return !empty($element);
        });
*/        
            $closeness = [];

            foreach ($dialects as $dialect) {
                $c = 0;
                foreach ($fractions as $mvariant_id => $f) {
                    $c += pow($f-$d_fractions[$dialect->id][$mvariant_id], 2);
                }
                $closeness[$dialect->id] = sqrt($c);
            }
            $guess = array_keys($closeness, min($closeness))[0];
            if ($guess == $text->dialects()->first()->id) {
                $right++;
            } else {
                $wrong++;
            }
        }
dd($right, $wrong); 
// 1165
// 687

    }
}
