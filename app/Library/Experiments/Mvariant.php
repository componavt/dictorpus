<?php

namespace App\Library\Experiments;

use Illuminate\Database\Eloquent\Model;
use DB;

use App\Library\Experiments\Dmarker;

use App\Library\Grammatic;

use App\Models\Corpus\Word;

use App\Models\Dict\Dialect;

class Mvariant extends Model
{
    public $timestamps = false;
    protected $fillable = ['id', 'dmarker_id', 'name'];    
    
    public function dmarker(){
        return $this->belongsTo(Dmarker::class);
    }
    
    public function dialects(){
        return $this->belongsToMany(Dialect::class, 'dialect_dmarker')
                ->withPivot('dmarker_id', 'frequency');
    }
    
    public function rightFrequency($dialect_id): bool {
        $frequency = $this->frequency($dialect_id);
        $right_answers = DialectDmarker::rules();
        if (!$frequency && !in_array($dialect_id, $right_answers[$this->id])
            || $frequency>0 && in_array($dialect_id, $right_answers[$this->id])) {
            return true;
        }
        return false;
    }
    
    public function frequency($dialect_id){
//dd($this->dialects);        
        $dialect = $this->dialects()->where('dialect_id', $dialect_id)->first();
        return $dialect ? round($dialect->pivot->frequency, 4) : '';
    }
    
    public function calculateFrequency($dialect) {
        if (!$this->template) {
            if (DB::table('dialect_dmarker')->whereMvariantId($this->id)->count()) {
                DB::statement('DELETE FROM dialect_dmarker where mvariant_id='. $this->id);
            }
            return;
        }
//        DB::statement('UPDATE dialect_dmarker SET frequency=NULL where mvariant='. $this->id. ' and dialect_id='.$dialect_id);
/*        if ($dialect->absence) {
            $count = $this->countTexts($dialect->id);
            $frequency = $count === false ? NULL : $count / $dialect->totalTexts();
        } else {*/
            $count = $this->countWords($dialect->id);
            $frequency = $count === false ? NULL : $count / $dialect->totalWords();            
//        }
        if (DB::table('dialect_dmarker')->whereMvariantId($this->id)->whereDialectId($dialect->id)->count()) {
            DB::statement('UPDATE dialect_dmarker SET frequency='.$frequency.' where mvariant_id='. $this->id. ' and dialect_id='.$dialect->id);
        } else {
            DB::table('dialect_dmarker')->insert([
                'dialect_id' => $dialect->id, 
                'dmarker_id' => $this->dmarker_id, 
                'mvariant_id' => $this->id, 
                'frequency' => $frequency]);
        }
    }
    
    public function searchWords($dialect_id) {
        return self::searchByTemplate($this->template)
                    ->whereIn('text_id', function ($q) use ($dialect_id) {
                $q -> select('text_id')->from('dialect_text')
                   -> whereDialectId($dialect_id);
            });
    }
    
    public function countWords($dialect_id) {
        return self::searchWords($dialect_id)->count();
/*        if (!sizeof ($words)) {
            return false;
        }
        $count = 0; 
        foreach ($words as $word) {
            if ($this->fitWord($word->word)) {
                $count++;
            }
        }
        return $count;*/
    }
/*    
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
    public static function searchByTemplate(string $template) {
        $template = str_replace('C', "[".Grammatic::consSet()."]", $template);
        $template = str_replace('V', "[".Grammatic::vowelSet()."]", $template);
//dd($template);        
        if (substr($template, 0, 1) == '!') {
            $template = substr($template, 1);
            $sign = 'not rlike';
        } else {
            $sign = 'rlike';
        }
        return Word::where('word', $sign, $template);
    }
}
