<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Models\Corpus\Text;

class Source extends Model
{
    protected $fillable = ['title', 'author', 'year', 'ieeh_archive_number1', 'ieeh_archive_number2', 'pages', 'comment'];

    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    public static function boot()
    {
        parent::boot();
    }
    
    /**
     * remove source if exists and don't link with other texts
     * 
     * @param INT $source_id
     * @param INT $text_id
     */
    public static function removeUnused($source_id, $text_id) {
        if ($source_id && !Text::where('id','<>',$text_id)
                               ->where('source_id',$source_id)
                               ->count()) {
            Source::find($source_id)->delete();
        }
    }
}
