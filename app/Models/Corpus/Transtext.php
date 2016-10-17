<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Models\Dict\Lang;

class Transtext extends Model
{
    protected $fillable = ['lang_id','title','text','text_xml'];

    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    
    public static function boot()
    {
        parent::boot();
    }

    // Transtext __belongs_to__ Lang
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }

    /**
     * Checks request data. If the request data is not null, 
     * updates Transtext if it exists or creates new and returns id of Transtext
     * 
     * If the request data is null and Transtext exists, 
     * destroy it and sets transtext_id in Text as NULL.
     * 
     * @return INT or NULL
     */
    public static function storeTranstext($requestData, $text_obj=NULL){
        $is_empty_data = true;
        if ($requestData['transtext_title'] || $requestData['transtext_text']) {
            $is_empty_data = false;
        }
//dd($is_empty_data);
        if ($text_obj) {
            $transtext_id = $text_obj->transtext_id;
        } else {
            $transtext_id = NULL;
        }

        if (!$is_empty_data) {
            foreach (['lang_id','title','text','text_xml'] as $column) {
                $data_to_fill[$column] = ($requestData['transtext_'.$column]) ? $requestData['transtext_'.$column] : NULL;
            }
            if ($transtext_id) {
               
                $transtext = self::find($transtext_id);
                $old_text = $transtext->text;
                $transtext->fill($data_to_fill);
                if ($data_to_fill['text'] && $old_text != $data_to_fill['text']) {
                    $transtext->divSentence();
                }
                $transtext->save();
            } else {
                $transtext = self::firstOrCreate($data_to_fill);
                $text_obj->transtext_id = $transtext->id;
                $text_obj->save();
            }
            return $transtext->id;
            
        } elseif ($transtext_id) {
            $text_obj->transtext_id = NULL;
            $text_obj->save();
            if (!Text::where('id','<>',$text_obj->id)
                     ->where('transtext_id',$transtext_id)
                     ->count()) {
                self::destroy($transtext_id);
            }
        }
    }    
    
    /**
     * Sets text_xml as a markup text with sentences
     */
    public function divSentence(){
        $out = '';
        $text = $this->text;
        $count = 1;
/*  division on paragraphs and then on sentences       
        if (preg_match_all("/(.+?)(\r?\n){2,}/is",$text,$desc_out)) {
            foreach ($desc_out[0] as $ab) {
                $ab = nl2br($ab);
                $out_ab = '';
                if (preg_match_all("/(.+?)(\.|\?|!|:){1,}(\s|<br(| \/)>|<\/p>|<\/div>|$)/is",$ab,$desc_out)) {
                    foreach ($desc_out[0] as $sentence) {
                       $out_ab .= "\t<s id=\"".$count++.'">'.trim($sentence)."</s>\n";
                    }
                } 
                $out .= "<p>\n".$out_ab."</p>\n";
            }
        } 
*/        
        // division only on sentences
        $text = nl2br($text);
        if (preg_match_all("/(.+?)(\.|\?|!|:|\.»|\?»|!»|\.\"|\?\"|!\"){1,}(\s|<br(| \/)>|$)/is",
                           $text, $desc_out)) {
//dd($desc_out);
            for ($i=0; $i<sizeof($desc_out[1]); $i++) {
                $sentence = trim($desc_out[1][$i]);
                if (preg_match("/^(<br(| \/)>)(.+)$/is",$sentence,$regs)) {
                    $out .= $regs[1]."\n";
                    $sentence = trim($regs[3]);
                }
                $out .= "<s id=\"".$count++.'">'.$sentence.$desc_out[2][$i]."</s>\n";
                $div = trim($desc_out[3][$i]);
                if ($div) {
                    $out .= trim($div)."\n";
                }
            }
        } 
        
        $this->text_xml = trim($out);
    }
}
