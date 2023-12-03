<?php

namespace App\Library\Experiments;

use Illuminate\Database\Eloquent\Model;
use DB;

use App\Models\Corpus\Word;

use App\Models\Dict\Dialect;

class DialectDmarker extends Model
{
    protected $table='dialect_dmarker';
    
    public static function dialectListByGroups() {
        return [
            'северные собственно карельские' => 
                [6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15],
            'южные собственно карельские' => 
                [/*48, 49,*/ 17, 18, 16, 19, 20/*, 21*/],
            'тверские собственно карельские' => 
                [25, 26, 27, 28, 29],
            'ливвиковские' => 
                [30, 31, 32, 33, 34, 35, 36, 37],
            'людиковские' => 
                [38, 39, 42, 41],
        ];
    }
    
    public static function rules() {
        return 
        $variants = [
                 1=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 21, 25, 26, 29, 34, 35, 38, 39, 42, 41],
                 2=>[18, 20, 19, 21, 28, 30, 33, 34],
                 3=>[16, 31, 32],
                 4=>[16, 19, 31],
                 5=>[36, 39],
                 6=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 21, 25, 26, 27, 29, 34, 35, 36, 38, 39, 42, 41],
                 7=>[19, 20, 21, 30, 32],
                 8=>[16, 19, 31],
                 9=>[32],
                 10=>[36],
                 11=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15], 
                 12=>[48, 49, 17, 18, 16, 19, 20, 21, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 42, 41],
                 13=>[6, 7, 8, 11, 10, 12, 50, 9, 14],
                 14=>[9, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 42, 41],
                 15=>[6, 7, 50, 9, 8, 11, 10, 12],
                 16=>[13, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 42, 41],
//                 17=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21],
//                 18=>[30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 42, 41],
//                 19=>[30, 32, 33, 34, 36, 35, 38, 39, 42],
//                 20=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21, 31, 41],
//                 21=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 17, 18, 16, 30, 32, 33, 34, 36, 35, 38, 39, 42, 41],
//                 22=>[16, 19, 20, 21, 31],
//                 23=>[16, 19, 20, 21, 30, 31, 32, 33, 34, 35, 36, 37],
//                 24=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 17],
                 25=>[30, 31, 32, 33, 34, 35, 36, 37, 38, 39],
                 26=>[39, 42],
                 27=>[42],
                 28=>[41],
                 29=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21],            
//                 30=>[33, 30, 38, 39, 42],
//                 31=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21, 31, 32, 34, 36, 35, 41],
//                 32=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 18, 16, 17, 49, 48, 21, 33, 30, 34, 38, 39, 42],
//                 33=>[15, 20, 16, 19, 21, 31, 32, 36, 35, 41],
                 34=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21, 25, 26, 27, 28, 29],
                 35=>[30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 42, 41],
                 36=>[29, 38, 39, 41],
                 37=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21, 30, 31, 32, 33, 34, 35, 36, 37, 42],
//                 38=>[38, 39],
//                 39=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21, 30, 31, 32, 33, 34, 35, 36, 37, 42],
                 40=>[17, 16, 18, 19, 20, 21, 32, 31, 33, 30, 38, 39, 42, 41],
                 41=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49,  17, 16, 25, 26, 27, 28, 29, 34, 36, 35, 32, 31, 33, 30],
                 42=>[8, 11, 10, 12, 14, 13, 17, 49, 16, 18, 19, 20, 21, 32, 34, 36, 35, 38],
                 43=>[6, 7, 50, 9, 15, 48, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 42, 41],
                 44=>[16, 19, 20, 21, 30, 32, 38, 39, 42, 41],
                 45=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 16, 19, 20, 36, 35, 34, 31, 33, 32],
//                 46=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 16, 19, 20, 21, 36, 35, 34, 33, 32],
//                 47=>[16, 19, 20, 21, 48, 33, 31, 38, 39, 42, 41],
                 48=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21, 28, 36, 35, 34, 30, 39, 42],
                 49=>[25, 26, 27, 29, 32, 33, 31, 38, 41],
                 50=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 16, 18, 19, 25, 26, 27, 28, 29],
                 51=>[19, 20, 21, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 42, 41],
                 52=>[6, 7, 50, 8, 10, 11, 12, 13, 48, 49, 17, 16, 18, 21, 25, 26, 28, 29, 38, 39],
                 53=>[9, 14, 16, 19, 20, 21, 27, 30, 31, 32, 33, 34, 35, 36, 37, 39, 42, 41],
                 54=>[6, 7, 50, 8, 10, 11, 12, 13, 48, 49, 17, 16, 18, 20, 21, 26, 28, 29, 38, 39],
                 55=>[9, 14, 16, 19, 21, 25, 27, 30, 31, 32, 33, 34, 35, 36, 37, 39, 42, 41],
//                 56=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21, 30, 31, 32, 33, 34, 35, 36, 37],
//                 57=>[38, 39, 42, 41],
                 58=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 16, 48, 17, 18, 19, 25, 26, 27, 28, 29, 49],
                 59=>[19, 20, 21],
//                 60=>[38, 41],
//                 61=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21, 30, 31, 32, 33, 34, 35, 36, 37, 39, 42],
//                 62=>[30, 31, 32, 33, 34, 35, 36, 37, 42],
//                 63=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21, 38, 39, 41],
//                 64=>[8, 11, 10, 30, 31, 32, 33, 34, 35, 36, 37, 42],
//                 65=>[6, 7, 50, 9, 14, 12, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21],
//                 66=>[38, 39, 41],
                 67=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21, 25, 26, 27, 28, 29],
                 68=>[30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 42],
                 69=>[41],
                 70=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21, 25, 26, 27, 28, 29],
                 71=>[30, 31, 32, 33, 34, 35, 36, 37, 42, 41],
                 72=>[38, 39],
                 73=>[6, 7, 50, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21, 25, 26, 27, 28, 29],
                 74=>[30, 31, 32, 33, 34, 35, 36, 37],
                 75=>[38, 39, 42, 41],
                 76=>[29, 36],
                 77=>[6, 7, 9, 8, 10, 11, 12, 13, 14, 15, 48, 49, 17, 18, 16, 19, 20, 21, 25, 26, 27, 28, 30, 31, 32, 33, 34, 35, 37, 38, 39, 42, 41, 50],
                 78=>[30, 31, 32, 33, 34, 35, 36, 37, 42],            
                 79=>[27, 37],
                 80=>[28],
            
        ];        
    }
    
    public static function init($output) {
        $gr_dialects = $dialects = [];
        foreach (self::dialectListByGroups() as $gr_name => $dialect_grs) {
            $gr_dialects[$gr_name] = sizeof($dialect_grs);
            foreach ($dialect_grs as $dialect_id) {
                $dialect = Dialect::find($dialect_id);
                $dialects[$dialect_id] = [
                    'name' => $dialect->name,
                    'text_total' => $dialect->totalTexts(),
                    'word_total' => $dialect->totalWords()
                ];
            }
        }                    
        $dmarkers = Dmarker::orderBy('id')->get();
        foreach ($dmarkers as $marker) {
            $d = [];
            foreach ( $marker->mvariants as $variant ) {
                $d[$variant->id] = $variant->dataForTable($output, array_keys($dialects));
            }
            $marker->mvariant_dialect = $d;
        }
        return [$dialects, $dmarkers, $gr_dialects];
    }
    
    public static function updateData($mvariant_id, $dmarker_id, $dialect_id, $t_frequency, $t_fraction, $w_frequency, $w_fraction) {
        if (DB::table('dialect_dmarker')->whereMvariantId($mvariant_id)->whereDialectId($dialect_id)->count()) {
            DB::statement('UPDATE dialect_dmarker SET t_frequency='.$t_frequency.', t_fraction='.$t_fraction
                    . ', w_frequency='.$w_frequency.', w_fraction='.$w_fraction
                    . ' where mvariant_id='. $mvariant_id. ' and dialect_id='.$dialect_id);
        } else {
            DB::table('dialect_dmarker')->insert([
                'dialect_id' => $dialect_id, 
                'dmarker_id' => $dmarker_id, 
                'mvariant_id' => $mvariant_id, 
                't_frequency' => $t_frequency,
                't_fraction' => $t_fraction,
                'w_frequency' => $w_frequency,
                'w_fraction' => $w_fraction]);
        }        
    }
    
    public static function createCoalitions($dialect_id, $win_coef, $players_num) {
//        DB::statement("DELETE FROM coalition_dialect where dialect_id=".$dialect_id);
/*        $p1 = Mvariant::whereIn('id', function ($q) use ($dialect_id) {
                        $q->select('mvariant_id')->from('dialect_dmarker')
                          ->where('w_frequency', '>', 0)
                          ->whereDialectId($dialect_id);
                     })->orderBy('id')->pluck('id')->toArray();*/
        $p = self::where('w_frequency', '>', 0)
                           ->whereDialectId($dialect_id)
                           ->orderBy('w_frequency', 'desc')
                           ->take($players_num)
                           ->pluck('mvariant_id')->toArray();
/*        $min = DialectDmarker::whereDialectId($dialect_id)->whereMvariantId($p[0])
                             ->first()->w_frequency;*/
        $max = self::getVoices($dialect_id, $p); 
        $min = $win_coef*$max;
//dd($dialect_id, $max, $min);        
        sort($p);
//dd($p1, $p);                     
//        $p = [1, 2, 3, 4];
        self::addCoalitions($p, [], $dialect_id, $min);
print "<b>Коалиции для диалекта $dialect_id записаны</b><br>\n";             
//dd(self::$coalitions);        
    }
    
    public static function addCoalitions($players, $prev, $dialect_id, $min) {
        foreach ($players as $i => $p) {
            if (!sizeof($prev)) {
                $coalition = [$p];
            } else {
                $coalition = array_merge($prev, [$p]);
            }
//            self::$coalitions[] = join('_',$coalition);
            $coalition_str = join('_',$coalition);
            $v = self::getVoices($dialect_id, $coalition);
            
            if ($v>$min) {
                print $dialect_id. ": ";
                if (!DB::table('coalition_dialect')->whereDialectId($dialect_id)->where('coalition', 'like', $coalition_str)->count()) {
                    DB::statement("INSERT INTO coalition_dialect (coalition, dialect_id, frequency) VALUES ('".
                            $coalition_str."', ".$dialect_id.", ".$v.")");
                    print "записано ";
                }
                print $coalition_str. ' = <b>'. $v. '</b>';
                print "<br>\n";
            }
            unset($players[$i]);
            self::addCoalitions($players, $coalition, $dialect_id, $min);
        }
    }
    
    public static function getVoices($dialect_id, $coalition) {
        return self::selectRaw('sum(w_frequency) as sum')
                 ->whereDialectId($dialect_id)->whereIn('mvariant_id', $coalition)
                 ->first()->sum;
    }

    public static function calculateSSindex($dialect_id, $coalitions_num, $n) {
        // игроки
        $players = self::where('w_frequency', '>', 0)
                           ->whereDialectId($dialect_id)
                           ->orderBy('w_frequency', 'desc')
                           ->take($n)
                           ->pluck('mvariant_id')->toArray();
        sort($players);
      foreach ($players as $p) {
          $SSind[$p] = 0;
        // выигрышные коалиции, содержащие игрока
//select coalition from coalition_dialect where dialect_id=6 and (coalition like '50' or coalition rlike '^50_' or coalition rlike '_50_' or coalition rlike '_50$') limit 33
        $coalitions= DB::table('coalition_dialect')
              ->whereDialectId($dialect_id)
              ->where(function ($q) use ($p) {
                  $q->where('coalition', 'like', $p)
                    ->orWhere('coalition', 'rlike', '^'.$p.'_')
                    ->orWhere('coalition', 'rlike', '_'.$p.'_')
                    ->orWhere('coalition', 'rlike', '_'.$p.'$');
              })->get();
              
          foreach ($coalitions as $coalition) {
//              $cp = preg_split('_', $coalition);
//              $s = sizeof($cp);
            $s = 1+substr_count($coalition->coalition, '_');
            if (!DB::table('coalition_dialect') // если коалиция без игрока невыигрышная
                  ->whereDialectId($dialect_id)
                  ->whereCoalition(self::coalitionWithoutPlayer($coalition->coalition, $p))->count()) {
                $SSind[$p] += fact($n-$s)*fact($s-1)/fact($n);
            }                     
          }
          DB::statement("UPDATE dialect_dmarker SET SSindex=".$SSind[$p].
                  " WHERE dialect_id=".$dialect_id. " AND mvariant_id=".$p);
      }
//dd($SSind);      
    }
    
    public static function coalitionWithoutPlayer($coalition, $player) {
        if ($coalition === (string)$player) {
            return '';
        }
        if (preg_match("/^".$player."\_(.+)$/", $coalition, $regs)
                || preg_match("/^(.+)\_".$player."$/", $coalition, $regs)) {
            return $regs[1];
        }
        if (preg_match("/^(.+)\_".$player."(\_.+)$/", $coalition, $regs)) {
            return $regs[1].$regs[2];
        }
    }
    
    public static function getWFractions($text, $mvariants) {
        $words = Word::whereTextId($text->id)->groupBy('word')->pluck('word')->toArray();

        return self::getWFractionsForWords($words, $mvariants);
    }
    
    public static function getWFractionsForWords($words, $mvariants) {
        $w_fractions = [];
        $total_words = sizeof($words);
        
        foreach ($mvariants as $mvariant) {
            list($absence, $template) = Mvariant::processTemplate($mvariant->template);
            $w_frequency = 0;
            foreach ($words as $word) {
                $p = preg_match("/".$template."/", $word);
                if (!$absence && $p || $absence && !$p) {
                    $w_frequency++;  
                }
            }
            $w_fractions[$mvariant->id] = $w_frequency === false ? 0 : $w_frequency / $total_words;   
            
        }
        return $w_fractions;
    }
    
    public static function dialectFractions() {
        $d_fractions = []; // частоты диалектов
        $dialects = Dialect::whereIn('id', function ($q) {
                        $q->select('dialect_id')->from('dialect_dmarker');
                    })->orderBy('id')->get();

        foreach ($dialects as $dialect) {
            $d_fractions[$dialect->id] = DialectDmarker::whereDialectId($dialect->id)
                    ->orderBy('mvariant_id')->pluck('w_fraction', 'mvariant_id')
                    ->toArray();
        }
        return $d_fractions;
    }
    
    public static function dialectCloseness($text, $mvariants, $d_fractions) {
        $closeness = [];
        $words = preg_split("/[\s,.:!]+/", $text);        
        
        $fractions = DialectDmarker::getWFractionsForWords($words, $mvariants);
        foreach (array_keys($d_fractions) as $dialect_id) {
            $c = 0;
            foreach ($fractions as $mvariant_id => $f) {
                $c += pow($f-$d_fractions[$dialect_id][$mvariant_id], 2);
            }
            $closeness[$dialect_id] = sqrt($c);
        }
        asort($closeness);
        return $closeness;
    }
}
