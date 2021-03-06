<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Models\Corpus\Text;
use App\Models\Dict\Meaning;

class TextWordform extends Model
{
    protected $table = 'text_wordform';
    
    public $timestamps = false;
    
    /**
     * Update text-wordform links after choosing meaning.
     * 
     * @param type $text_id
     * @param type $w_id
     * @param type $meaning_id
     */
    public static function updateWordformLinksAfterCheckExample($text_id, $w_id, $meaning_id) {
        $meaning=Meaning::find($meaning_id);
        $pos_id=$meaning->lemma->pos_id;
        DB::statement('UPDATE text_wordform SET relevance=0'
                     .' WHERE gramset_id not in (select gramset_id from gramset_pos where pos_id='.$pos_id.')'
                     .' AND text_id='.$text_id
                     .' AND w_id='.$w_id);
    }
}
