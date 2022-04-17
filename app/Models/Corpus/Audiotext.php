<?php

namespace App\Models\Corpus;

//use \Venturecraft\Revisionable\Revision;
use Illuminate\Database\Eloquent\Model;
use Storage;

class Audiotext extends Model
{
    const DISK = 'audiotexts';
    const DIR = 'audio/texts/';
    protected $fillable = ['text_id', 'filename'];
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.
    
    public static function boot()
    {
        parent::boot();
    }
    
    // Belongs To Relations
    use \App\Traits\Relations\BelongsTo\Text;
    
    /**
     * 
     * @return array with all file names in the disk directory 
     */
    public static function getAllFiles($without_text=null) {
        $files = Storage::disk(self::DISK)->files();
        if ($without_text) {
            $audiotexts = self::whereTextId($without_text)->pluck('filename')->toArray();
            $files = array_diff($files, $audiotexts);
        }
        return $files;
    }
    
    public function url() {
//        return route('audiotext.show', ['id'=>$this->id]);
//        return Storage::disk('audiotexts')->url($this->filename);
        return Storage::url(self::DIR . $this->filename);
    }
}
