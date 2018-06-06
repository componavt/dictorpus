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
}
