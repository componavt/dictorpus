<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Models\Dict\Lang;

class Word extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['text_id', 'sentence_id', 'w_id', 'word'];
    
    /** Word belongs_to Text
     * 
     * @return Relationship, Query Builder
     */
    public function text()
    {
        return $this->belongsTo(Text::class);
    } 
    
    // Word __has_many__ Meanings
    public function meanings(){
        $builder = $this->belongsToMany(Meaning::class)
                 -> withPivot('relevance');
        return $builder;
    }

    /**
     * Changes obsolete letters to modern
     * If a parameter lang_id is given, then does the check need such a replacement
     * 
     * @param String $word
     * @param Int $lang_id
     * @return String
     */
    public static function changeLetters($word,$lang_id=null) {
        if ($lang_id && !isLangVepsOrKarelian($lang_id)) {
            return $word;
        }
        $word = str_replace('w','y',$word);
        $word = str_replace('W','Y',$word);
        $word = str_replace('ü','y',$word);
        $word = str_replace('Ü','Y',$word);
        return $word;
    }
    
    public function leftNeighbor() {
        if ($this->w_id == 1) { return; }
        $word = Word::where('text_id',$this->text_id)
                ->where('sentence_id',$this->sentence_id)
                ->where('w_id','<',$this->w_id)
                ->orderBy('w_id','desc')
                //->toSql();
                ->first();
//dd($word.'|'.$this->text_id.'|'.$this->sentence_id.'|'.$this->w_id);        
        return $word;
    }
}
