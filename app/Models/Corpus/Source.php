<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

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
     * Checks request data. If the request data is not null, 
     * updates Source if it exists or creates new and returns id of Source
     * 
     * If the request data is null and Source exists, 
     * destroy it and sets source_id in Text as NULL.
     * 
     * @return INT or NULL
     */
    public static function storeSource($requestData, $text_obj=NULL){
        $is_empty_data = true;
        if(array_filter($requestData)) {
            $is_empty_data = false;
        }
        if ($text_obj) {
            $source_id = $text_obj->source_id;
        } else {
            $source_id = NULL;
        }

        if (!$is_empty_data) {
            foreach (['title', 'author', 'year', 'ieeh_archive_number1', 'ieeh_archive_number2', 'pages', 'comment'] as $column) {
                $data_to_fill[$column] = ($requestData['source_'.$column]) ? $requestData['source_'.$column] : NULL;
            }
            if ($source_id) {
                $source = self::find($source_id)->fill($data_to_fill);
                $source->save();
            } else {
                $source = self::firstOrCreate($data_to_fill);
                $text_obj->source_id = $source->id;
                $text_obj->save();
            }
 //       dd($source);
            return $source->id;
            
        } elseif ($source_id) {
            $text_obj->source_id = NULL;
            $text_obj->save();
            
            if (!Text::where('id','<>',$text_obj->id)
                     ->where('source_id',$source_id)
                     ->count()) {
                self::destroy($source_id);
            }
        }
    }    
}
