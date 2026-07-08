<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

use App\Models\Corpus\Text;
use App\Models\Dict\Meaning;

class TextWordform extends Model
{
    protected $table = 'text_wordform';

    public $timestamps = false;

    /**
     * Update text-wordform links after choosing meaning.
     * 
     * @param int $text_id
     * @param int $w_id
     * @param int $meaning_id
     */
    public static function updateWordformLinksAfterCheckExample($text_id, $w_id, $meaning_id)
    {
        $meaning = Meaning::find($meaning_id);
        $pos_id = $meaning->lemma->pos_id;

        DB::statement(
            'UPDATE text_wordform
             SET relevance = 0
             WHERE gramset_id NOT IN (
                 SELECT gramset_id FROM gramset_pos WHERE pos_id = ?
             )
             AND text_id = ?
             AND w_id = ?',
            [$pos_id, $text_id, $w_id]
        );
    }
}
