<?php

namespace App\Models\Dict;

use App\Library\Grammatic\KarGram;
use App\Library\Grammatic\VepsGram;

use Illuminate\Database\Eloquent\Model;

/*
 * for veps nominals:
 * 0 = nominativ sg
 * 1 = base of genetive sg (genetive sg - 'n')
 * 2 = partitive sg
 * 3 = base of illative sg (???)
 * 4 = base of partitive pl (partitive pl - 'd')
 * 
 * for karelian nominals:
 * 0 = nominativ sg
 * 1 = base of genetive sg (genetive sg - 'n')
 * 2 = base of illative sg (illative sg - 'h')
 * 3 = partitive sg
 * 4 = base of genetive pl (genetive pl - 'n')
 * 5 = base of illative pl (illative pl - 'h')
 * 
 * for veps verbs:
 * 0 = infinitive 1
 * 1 = base of indicative presence 1 sg  (indicative presence 1 sg - 'n')
 * 2 = base of indicative imperfect 1 sg  (indicative imperfect 1 sg - 'n')
 * 3 = base of 2 active particle  (conditional imperfect 3 sg - 'nuiži')
 * 4 = base of conditional  (conditional presence 3 sg - 'iži')
 * 5 = base of potential (2 active particle - 'nu')
 * 
 * for karelian verbs:
 * 0 = infinitive 1
 * 1 = base of indicative presence 1 sg (indicative presence 1 sg - 'n')
 * 2 = base of indicative presence 3 sg (3 infinitive illative - 'mah / mäh')
 * 3 = base of indicative imperfect 1 sg (indicative imperfect 1 sg - 'n')
 * 4 = indicative imperfect 3 sg
 * 5 = base of perfect  (???)
 * 6 = base of indicative presence 3 pl (indicative presence 3 pl - 'h')
 * 7 = base of indicative imperfect 3 pl (indicative imperfect 3 pl - 'ih')
 */

class LemmaBase extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.
    
    protected $fillable = ['lemma_id','base_n','base', 'dialect_id'];
    
    public static function boot()
    {
        parent::boot();
    }
    
    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Lemma;
    
    public static function updateStem($lemma_id, $stem_n, $stem, $dialect_id) {
        $base = self::where('lemma_id', $lemma_id)
                    ->where('dialect_id',$dialect_id)
                    ->where('base_n', $stem_n)->first();
        if (!$stem) {
            if ($base) {
                $base->delete();                
            }
            return;
        }
        if (!$base) {
            $base = self::create(['lemma_id'=>$lemma_id, 'base_n'=> $stem_n, 'dialect_id'=>$dialect_id]);
        }
        $base->base = $stem;
        $base->save();
    }
    
    public static function updateStemsFromSet($lemma_id, $stems, $dialect_id) {
        if (!$stems) {
            return;
        }
        for ($i=0; $i<sizeof($stems); $i++) {
            if (isset($stems[$i])) {
                self::updateStem($lemma_id, $i, $stems[$i], $dialect_id);
            }
        }
    }
    
    public static function updateStemsFromDB($lemma, $pos_id, $dialect_id) {
        if ($lemma->lang_id == 1) {
            $stems = VepsGram::stemsFromDB($lemma, $pos_id, $dialect_id);
        } else {
            $stems = KarGram::stemsFromDB($lemma, $pos_id, $dialect_id);            
        }
        self::updateStemsFromSet($lemma->id, $stems, $dialect_id);
//            if ($pos_id != PartOfSpeech::getVerbID() && !in_array($pos_id, PartOfSpeech::getNameIDs())) {
    }
    
    public static function baseList($lang_id, $pos_id) {
        $base_list_title = 'base_list_'.$lang_id.'_';
        if ($pos_id == PartOfSpeech::getVerbID()) {
            $base_list_title .= 'verb';
        } elseif (in_array($pos_id, PartOfSpeech::getNameIDs())) {
            $base_list_title .= 'name';
        } else {
            return null;            
        }
        
        return trans('dict.'.$base_list_title);        

    }
    
}
