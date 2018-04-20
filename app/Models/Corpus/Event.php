<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Models\Corpus\Informant;
use App\Models\Corpus\Place;
use App\Models\Corpus\Recorder;

class Event extends Model
{
    protected $fillable = ['informant_id','place_id','date'];
    public $timestamps = false;
    
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.
    protected $revisionFormattedFields = array(
        'name_ru'  => 'string:<strong>%s</strong>',
    );
    protected $revisionFormattedFieldNames = array(
//        'title' => 'Title',
//        'small_name' => 'Nickname',
//        'deleted_at' => 'Deleted At'
    );

    public static function boot()
    {
        parent::boot();
    }
    
    /** 
     * Event belongs_to Informant
     * 
     * @return Relationship, Query Builder
     */
    public function informants()
    {
        return $this->belongsToMany(Informant::class);
    }    
    
    /** 
     * Event belongs_to Place
     * 
     * @return Relationship, Query Builder
     */
    public function place()
    {
        return $this->belongsTo(Place::class);
    }  
    
    // Event __has_many__ Recorders
    public function recorders(){
        return $this->belongsToMany(Recorder::class);
    }
    
    /**
     * Checks request data. If the request data is not null, 
     * updates Event if it exists or creates new and returns id of Event
     * 
     * If the request data is null and Event exists, 
     * destroy it and sets event_id in Text as NULL.
     * 
     * @return INT or NULL
     */
    public static function storeEvent($requestData, $text_obj=NULL){
        $is_empty_data = true;
        if(array_filter($requestData)) {
            $is_empty_data = false;
        }
//dd($is_empty_data);
        if ($text_obj) {
            $event_id = $text_obj->event_id;
        } else {
            $event_id = NULL;
        }

        if (!$is_empty_data) {
            $data_to_fill = [];
            foreach (['informant_id','place_id','date'] as $column) {
                $data_to_fill[$column] = ($requestData['event_'.$column]) ? $requestData['event_'.$column] : NULL;
            }
            if ($event_id) {
                $event = self::find($event_id)->fill($data_to_fill);
                $event->save();
            } else {
                $event = self::firstOrCreate($data_to_fill);
                $text_obj->event_id = $event->id;
                $text_obj->save();
//dd($text_obj);               
            }
            return $event->id;
            
        } elseif ($event_id) {
            $text_obj->event_id = NULL;
            $text_obj->save();
            if (!Text::where('id','<>',$text_obj->id)
                     ->where('event_id',$event_id)
                     ->count()) {
                $text_obj->event->recorders()->detach();
                self::destroy($event_id);
            }
        }
    }    

    /**
     * Gets full information about event as array
     * 
     * i.e. ['informant' => 'Калинина Александра Леонтьевна, г.р. 1909, урожд. Пондала (Pondal), Бабаевский р-н, Вологодская обл.',
     *       'date' => 'Петрозаводск, Республика Карелия',
     *       'place' => '1957', 
     *       'recoders' => ['Богданов Николай Иванович', 'Зайцева Мария Ивановна']
     *      ]
     * 
     * @param int $lang_id ID of text language for output translation of settlement title, f.e. Pondal
     * 
     * @return Array
     */
/*    public function eventArray() //$lang_id
    {
//        $informant = Informant::find($event->informant->id)->first();
        //var_dump($informant);
//exit(0);        
        $event_info['informant'] = $this->informant->informantString;//($lang_id);
        $event_info['place'] = $this->placeString;//($lang_id);  
        $event_info['date'] = $this->date;
        
        $recorders = $this->recorders;
//        var_dump($recorders);
//        exit(0);
        foreach ($recorders as $recorder) {
            $event_info['recoders'][] = $recorder->name;
        }
        return $event_info;
    }    
 * 
 */
    
    /**
     * Gets full information about event as string
     * 
     * i.e. "Информант: Калинина Александра Леонтьевна, г.р. 1909, урожд. Пондала (Pondal), Бабаевский р-н, Вологодская обл., место записи: Петрозаводск, Республика Карелия, г. записи: 1957, записали: Богданов Николай Иванович, Зайцева Мария Ивановна"
     * 
     * @param int $lang_id ID of text language for output translation of settlement title, f.e. Pondal
     * 
     * @return String
     */
/*    public function eventString()//$lang_id
    {
        $event_info = $this->eventArray();//($lang_id);
        if (!$event_info) {
            return NULL;
        }
        $event_info['informant'] = '<i>'. \Lang::get('corpus.informant'). ':</i> '. $event_info['informant'];
        $event_info['place'] = '<i>'. \Lang::get('corpus.record_place'). ':</i> '. $event_info['place'];       
        $event_info['date'] = '<i>'. \Lang::get('corpus.record_year'). ':</i> '. $event_info['date'];
        $event_info['recorders'] = '<i>'. \Lang::get('corpus.recorded'). ':</i> '. join(', ',$event_info['recorders']);
        
        return join('<br>',$event_info);
    }    
 * 
 */
    
}
