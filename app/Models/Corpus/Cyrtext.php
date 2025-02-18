<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Models\Corpus\Text;

class Cyrtext extends Model
{
    use \App\Traits\Methods\getSentencesFromXML;
    protected $fillable = ['id','title','text','text_xml'];

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
    
    // Cyrtext __belongs_to__ Text 1:1
    public function text()
    {
        return $this->belongsTo(Text::class,'id','id');
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
     * remove cyrtext if exists and don't link with other texts
     * 
     * @param INT $id
     */
    public static function removeUnused($id) {
        if ($id && Text::where('id',$id)->count()) {
            Cyrtext::find($id)->delete();
        }        
    }
    
    public static function store($id, $title, $text) {
        if (empty($title) && empty($text)) {
            return;
        }

        $cyrtext = self::find($id);
        
        if (!empty($cyrtext)) {
            $cyrtext->title = $title;
            $cyrtext->text = $text;
            $cyrtext->save();
        } else {
            self::create(['id'=>$id, 'title'=>$title, 'text'=>$text]);
        }
    }
    

}
