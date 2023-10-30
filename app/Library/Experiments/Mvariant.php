<?php

namespace App\Library\Experiments;

use Illuminate\Database\Eloquent\Model;
use DB;

use App\Library\Experiments\Dmarker;
use App\Library\Experiments\DialectDmarker;

use App\Library\Grammatic;

use App\Models\Corpus\Text;
use App\Models\Corpus\Word;

use App\Models\Dict\Dialect;

class Mvariant extends Model
{
    public $timestamps = false;
    protected $fillable = ['id', 'dmarker_id', 'name'];    
    static private $coalitions = [];
    
    public function dmarker(){
        return $this->belongsTo(Dmarker::class);
    }
    
    public function dialects(){
        return $this->belongsToMany(Dialect::class, 'dialect_dmarker')
                ->withPivot('dmarker_id', 't_frequency', 't_fraction', 'w_frequency', 'w_fraction');
    }
    
    public function rightFrequency($dialect_id, $frequency=null): bool {
        if (!$frequency) {
            $frequency = (int)$this->frequency($dialect_id);
        }
        $right_answers = DialectDmarker::rules();
        if (!$frequency && !in_array($dialect_id, $right_answers[$this->id])
            || $frequency>0 && in_array($dialect_id, $right_answers[$this->id])) {
            return true;
        }
        return false;
    }
    
    public function fraction($dialect_id){
        $dialect = $this->dialects()->where('dialect_id', $dialect_id)->first();
//dd($dialect->pivot->fraction);  
        $t_fraction = round($dialect->pivot->t_fraction, 4);
        return !$dialect ? '' : (!$t_fraction ? 0 : $t_fraction. ' / '. round($dialect->pivot->w_fraction, 4));
    }
    
    public function frequency($dialect_id){
//dd($this->dialects);        
        $dialect = $this->dialects()->where('dialect_id', $dialect_id)->first();
        $t_frequency = $dialect->pivot->t_frequency;
        return !$dialect ? '' : (!$t_frequency ? 0 : $t_frequency. ' / '. $dialect->pivot->w_frequency);
    }
    
    public function dataForTable($output, $dialect_ids) {
        $out = ['name' => $this->name,
                'template' => $this->template,
                'dialects' => []];
        list($absence, $template) = self::processTemplate($this->template);
        foreach ($dialect_ids as $dialect_id) {
            $dialect = $this->dialects()->where('dialect_id', $dialect_id)->first();
            if ($output == 'words') {
                $d['words'] = $this->searchWords($dialect_id, $absence, $template)
                        ->groupBy('word')->selectRaw('word, count(*) as count, text_id, w_id')
                        ->orderBy('count','desc')->orderBy('word')->get();
            } else {
                $d['t_frequency'] = $dialect ? $dialect->pivot->t_frequency : '';
                $d['w_frequency'] = $dialect ? $dialect->pivot->w_frequency : '';
                $d['t_fraction'] = $dialect ? round($dialect->pivot->t_fraction, 4) : '';
                $d['w_fraction'] = $dialect ? round($dialect->pivot->w_fraction, 4) : '';
                $d['color'] = $this->rightFrequency($dialect_id, $d['t_frequency']) ? 'black' : 'red';
            }
            $out['dialects'][$dialect_id] = $d;
        }
        return $out;
    }

    public function calculateFrequencyAndFraction($dialect_id, $total_texts, $total_words) {
        if (!$this->template) {
            if (DB::table('dialect_dmarker')->whereMvariantId($this->id)->count()) {
                DB::statement('DELETE FROM dialect_dmarker where mvariant_id='. $this->id);
            }
            return;
        }
        list($absence, $template) = self::processTemplate($this->template);

        $t_frequency = $this->searchTexts($dialect_id, $absence, $template)->count();
        $t_fraction = $t_frequency === false ? NULL : $t_frequency / $total_texts;   
        
//        $w_frequency = $this->searchWords($dialect_id, $absence, $template)->count();
        $w_frequency = sizeof($this->searchUniqueWords($dialect_id, $absence, $template)->get());
        $w_fraction = $w_frequency === false ? NULL : $w_frequency / $total_words;   
        
        DialectDmarker::updateData($this->id, $this->dmarker_id, $dialect_id, $t_frequency, $t_fraction, $w_frequency, $w_fraction);
    }
    
    public function searchTexts($dialect_id, $absence, $template) {
        $builder = Text::whereIn('id', function ($q) use ($dialect_id) {
                        $q -> select('text_id')->from('dialect_text')
                           -> whereDialectId($dialect_id);
                    });
        if ($absence) {
                return $builder->whereNotIn('id', function ($q) use ($absence, $template) {
                        $q -> select('text_id')->from('words')
                           -> where('word', 'rlike',  $template);   
                    });
        }
        return $builder->whereIn('id', function ($q) use ($absence, $template) {
                        $q -> select('text_id')->from('words')
                           -> where('word', 'rlike',  $template);   
                    });
    }
    
    public function searchWords($dialect_id, $absence, $template) {
        return Word::whereRaw("word ".($absence ? 'not ': '')."rlike '$template'")        
                    ->whereIn('text_id', function ($q) use ($dialect_id) {
                        $q -> select('text_id')->from('dialect_text')
                           -> whereDialectId($dialect_id);
                    });
    }
    
    public function searchUniqueWords($dialect_id, $absence, $template) {
        return Word::whereRaw("word ".($absence ? 'not ': '')."rlike '$template'")        
                    ->whereIn('text_id', function ($q) use ($dialect_id) {
                        $q -> select('text_id')->from('dialect_text')
                           -> whereDialectId($dialect_id);
                    })->groupBy('word')->groupBy('text_id');
    }
    
    public static function processTemplate(string $template) {
        $template = str_replace('C', "[".Grammatic::consSet()."]", $template);
        $template = str_replace('V', "[".Grammatic::vowelSet()."]", $template);
//dd($template);        
        if (substr($template, 0, 1) == '!') {
            $template = substr($template, 1);
            $absence = true;
        } else {
            $absence = false;
        }
        return [$absence, $template];
    }
    

    /*    public function countWords($dialect_id) {
        return self::searchWords($dialect_id)->count();
        if (!sizeof ($words)) {
            return false;
        }
        $count = 0; 
        foreach ($words as $word) {
            if ($this->fitWord($word->word)) {
                $count++;
            }
        }
        return $count;
    }
    
    public function fitWord(string $word) : bool {
        if (in_array($this->dmarker_id, [1,2])) {
            return self::checkInFirstSyllable($this->name, $word);
        }
        return false;
    }
    
    public static function checkInFirstSyllable(string $template, string $word) : bool {
        $C = "[".Grammatic::consSet()."]";
        if (preg_match("/^".$C."*".$template."/u", $word)) {
            return true;
        }
        return false;
    }
*/   
}
