<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Models\Dict\Lang;
use App\Models\Corpus\Text;

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

    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Authors;
    
    // Transtext __belongs_to__ Lang
    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }

    /**
     * Sets text_xml as a markup text with sentences
     */
    public function markup(){  
        $this->text_xml = Text::markupText($this->text);
    }
    
    public static function removeByID($id) {
        $obj = self::find($id);
        if (!$obj) { return;}
        $obj->authors()->detach();
        $obj->delete();
    }    

    /**
     * remove transtext if exists and don't link with other texts
     * 
     * @param INT $transtext_id
     * @param INT $text_id
     */
    public static function removeUnused($transtext_id, $text_id) {
        if ($transtext_id && !Text::where('id','<>',$text_id)
                                  ->where('transtext_id',$transtext_id)
                                  ->count()) {
            Transtext::find($transtext_id)->delete();
        }        
    }
    
    public function authorsToString() {
        $authors = [];
        foreach ($this->authors as $author) {
            $name = $author->getNameByLang($this->lang_id);
            $authors[] = $name ? $name : $author->name;
        }
        return join(', ', $authors);
    }

}
