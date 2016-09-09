<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Models\Dict\Lang;

class Transtext extends Model
{
    protected $fillable = ['lang_id','title','text'];

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
            foreach (['lang_id','title','text'] as $column) {
                $data_to_fill[$column] = ($requestData['transtext_'.$column]) ? $requestData['transtext_'.$column] : NULL;
            }
            if ($transtext_id) {
                $transtext = self::find($transtext_id)->fill($data_to_fill);
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
    
}
