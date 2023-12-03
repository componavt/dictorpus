<?php

namespace App\Http\Controllers\Library\Experiments;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//use App\Http\Requests;
use App\Http\Controllers\Controller;
use Storage;

use App\Charts\DistributionChart;

use App\Library\Experiments\DialectDmarker;
use App\Library\Experiments\Dmarker;
use App\Library\Experiments\Mvariant;

use App\Models\Corpus\Text;

use App\Models\Dict\Dialect;
use App\Models\Dict\Lang;

class DialectDmarkerController extends Controller
{
    public function __construct(Request $request)
    {
        // permission= corpus.edit, redirect failed users to /corpus/text/, authorized actions list:
        $this->middleware('auth:corpus.edit,/', 
                         ['only' => ['calculate', 'calculateСoalitions', 
                             'calculateSSindex', 'checkExperiment']]);
    }
    /**
     * /experiments/dialect_dmarker/
     * 
     */
    public function index() {
        return view('experiments.dialect_dmarker.index');
    }
    
    public function frequencies() {
        $output = 'frequency';
        list($dialects, $dmarkers, $gr_dialects) = DialectDmarker::init($output);
        
        return view('experiments.dialect_dmarker.dialect_check', 
                compact('dialects', 'dmarkers', 'gr_dialects', 'output'));
    }
    
    public function fractions() {
        $output = 'fraction';
        list($dialects, $dmarkers, $gr_dialects) = DialectDmarker::init($output);
        
        return view('experiments.dialect_dmarker.dialect_check', 
                compact('dialects', 'dmarkers', 'gr_dialects', 'output'));
    }
    
    public function words() {
        $output = 'words';
        list($dialects, $dmarkers, $gr_dialects) = DialectDmarker::init($output);
        
        return view('experiments.dialect_dmarker.dialect_check', 
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
    
    public function checkExperiment() {
        $mvariants = Mvariant::orderBy('id')->get();

        $d_fractions = DialectDmarker::dialectFractions();

        $stats = [];
        foreach ([4,5,6] as $lang_id) {
            $texts = Text::whereLangId($lang_id)
                        ->hasOneDialect()
                        ->whereIn('id', function ($q) {
                            $q->select('text_id')->from('dialect_text');
                        })->orderBy('id')->get();

            $stats[$lang_id]['total'] = sizeof($texts);
            
            foreach ($texts as $text) {
                $closeness = DialectDmarker::dialectCloseness($text, $mvariants, $d_fractions);                
                $guess = array_keys($closeness)[0];
                $guess_dialect = Dialect::find($guess);
                $text_dialect = $text->dialects()->first()->id;
                if ($guess == $text_dialect) {
                    $stats[$lang_id]['texts']['right'][$text->id] = $closeness;
                    $stats[$lang_id]['dialects'][$text_dialect]['right'][] = $text->id;
                } else {
                    $stats[$lang_id]['texts']['wrong'][$text->id] = $closeness;
                    $stats[$lang_id]['dialects'][$text_dialect]['wrong'][] = $text->id;
                }
                
                if ($guess_dialect->lang_id == $text->dialects()->first()->lang_id) {
                    $stats['langs']['right'][$lang_id] = empty($stats['langs']['right'][$lang_id]) ? 1 : 1+$stats['langs']['right'][$lang_id];
                } else {
                    $stats['langs']['wrong'][$lang_id] = empty($stats['langs']['wrong'][$lang_id]) ? 1: 1+$stats['langs']['wrong'][$lang_id];                    
                }
            }
        }       
        Storage::disk('public')->put('export/dialect_dmarker.txt', json_encode($stats));
print 'done.';
    }
    
    public function checkResults() {
        $stats = json_decode(Storage::disk('public')->get('export/dialect_dmarker.txt'), true);
        $langs = Lang::whereIn('id', [4,5,6])->orderBy('id')->get();
        foreach ($langs as $lang) {
            $labels[] = $lang->name. '('.$stats[$lang->id]['total'].')';
            $dataset[0][] = 100*$stats['langs']['right'][$lang->id] / $stats[$lang->id]['total'];
            $dataset[1][] = 100*$stats['langs']['wrong'][$lang->id] / $stats[$lang->id]['total'];
        }
//dd($stats['langs']);     
        $chart = new DistributionChart;
        $colors = $chart->colors();
        $chart->labels($labels);
        $chart->dataset('правильно', 'horizontalBar', $dataset[0])->fill(false)
              ->color('#'.$colors[3])->backgroundColor('#'.$colors[3]);
        $chart->dataset('ошибочно', 'horizontalBar', $dataset[1])->fill(false)
              ->color('#'.$colors[2])->backgroundColor('#'.$colors[2]);
        $chart->height(110);
        $charts['langs'] = $chart;            

        foreach ([4,5,6] as $lang_id) {
            $dialects = Dialect::whereIn('id', array_keys($stats[$lang_id]['dialects']))
                        ->orderBy('id')->get();
            $dataset = [];
            foreach ($dialects as $dialect) {
                $right_count = empty($stats[$lang_id]['dialects'][$dialect->id]['right']) ? 0 :
                        sizeof($stats[$lang_id]['dialects'][$dialect->id]['right']);
                $wrong_count = empty($stats[$lang_id]['dialects'][$dialect->id]['wrong']) ? 0 :
                        sizeof($stats[$lang_id]['dialects'][$dialect->id]['wrong']);
                $total = $right_count+$wrong_count; 
                $dataset[] = ['dialect_name' => $dialect->name. ' ('.$total.')',
                            'wrong' => 100*$right_count/$total,
                            'right' => 100*$wrong_count/$total];
            }
            $dataset = collect($dataset)->sortByDesc('wrong');
            
            $chart = new DistributionChart;
            $colors = $chart->colors();
            $chart->labels($dataset->pluck('dialect_name')->toArray());
            $chart->dataset('правильно', 'horizontalBar', $dataset->pluck('wrong')->toArray())->fill(false)
                  ->color('#'.$colors[3])->backgroundColor('#'.$colors[3]);
            $chart->dataset('ошибочно', 'horizontalBar', $dataset->pluck('right')->toArray())->fill(false)
                  ->color('#'.$colors[2])->backgroundColor('#'.$colors[2]);
            $chart->height(50+20*sizeof($dataset));
            $charts[$lang_id] = $chart;            
        }
        return view('experiments.dialect_dmarker.check_results', 
                compact('charts', 'langs', 'stats'));        
    }
    
    public function guess(Request $request) {
        $text = $request->text;
        $d_fractions = []; // частоты диалектов
        $mvariants = Mvariant::orderBy('id')->get();

        $dialects = Dialect::whereIn('id', function ($q) {
                        $q->select('dialect_id')->from('dialect_dmarker');
                    })->orderBy('id')->get();

        foreach ($dialects as $dialect) {
            $d_fractions[$dialect->id] = DialectDmarker::whereDialectId($dialect->id)
                    ->orderBy('mvariant_id')->pluck('w_fraction', 'mvariant_id')
                    ->toArray();
        }
        $closeness = DialectDmarker::dialectCloseness($text, $mvariants, $d_fractions);                

        $guess = array_keys($closeness)[0];
        $guess_dialect = Dialect::find($guess);
        return $guess_dialect->name;
    }
    
}
