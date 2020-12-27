<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;
use DB;

use App\Models\Corpus\Informant;
use App\Models\Corpus\Place;
use App\Models\Corpus\Recorder;
use App\Models\Corpus\Text;

class Event extends Model
{
    protected $fillable = ['place_id','date']; //'informant_id',
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
    
    // Event __has_many__ Textrs
    public function texts(){
        return $this->hasMany(Text::class);
    }
    
    public function updateInformantsAndRecorders($request_data) {
//dd($this);        
//dd($request_data);        
        $this->informants()->detach();
        $this->informants()->attach($request_data['event_informants']);
        $this->recorders()->detach();
        $this->recorders()->attach($request_data['event_recorders']);
//dd($this);        
//dd($this->informants);        
    }
    
    public static function removeByID($id) {
        $event = self::find($id);
        if (!$event) { return; }
        $event->informants()->detach();
        $event->recorders()->detach();
        $event->delete();
    }    

    /**
     * remove event if exists and don't link with other texts
     * 
     * @param INT $event_id
     * @param INT $text_id
     */
    public static function removeUnused($event_id, $text_id) {
        if (!$event_id) { 
            return;             
        }
        if (Text::where('id','<>',$text_id)
                ->where('event_id',$event_id) 
                ->count()) {
            return; 
                
        }
        $event = self::find($event_id);
        if (!$event) { return; }
        
        $event->informants()->detach();
        $event->recorders()->detach();
        $event->delete();
    }
    
    /**
     * Is it possible to change the event of text?
     * Yes, if no other texts with event_id=$this->id, besides $text
     * No,
     * @param Text $withoutText 
     * @param Array $new_data = ['event_place_id'=>$place_id, 'event_date'=>$date,
     *                          'event_informants'=>[$informants1, $informants2,...],
     *                          'event_recorders' => [$recorder1, $recorder2, ...]] 
     * @return Integer  = 0 - to create a new event 
     *                    1 - to update the event
     *                    2 - nothing doing, because event is not changed
     */
    public function isPossibleChanged($text, $new_data) {
        if (!$this) { return 0; }
//var_dump($new_data);        
        
        $texts = Text::where('event_id',$this->id)
                ->where('id','<>',$text->id)->get();
        // no other texts besides $text
        if (sizeof($texts)==0) { return 1; }

        if ($this->place_id != $new_data['event_place_id']
            ||  $this->date != $new_data['event_date']) {
            return 0; }
            
        $informants = DB::table('event_informant')->where('event_id', $this->id)->lists('informant_id');    
//var_dump($informants);
//var_dump($new_data['event_informants']);
        if (sizeof(array_diff($informants,(array)$new_data['event_informants']))
                || sizeof(array_diff((array)$new_data['event_informants'],$informants))) {
            return 0; } // different informants
        
        $recorders = (array)DB::table('event_recorder')->where('event_id', $this->id)->lists('recorder_id');    
        if (sizeof(array_diff($recorders, (array)$new_data['event_recorders']))
                || sizeof(array_diff((array)$new_data['event_recorders'],$recorders))) {
            return 0; } // different recorders      
        
        return 2;
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
