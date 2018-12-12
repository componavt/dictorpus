<?php

namespace App\Models\Corpus;

use Illuminate\Database\Eloquent\Model;

use App\Models\Corpus\Informant;
use App\Models\Corpus\Place;
use App\Models\Corpus\Recorder;
use App\Models\Corpus\Text;

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
    
    // Event __has_many__ Textrs
    public function texts(){
        return $this->hasMany(Text::class);
    }
    
    public static function removeByID($id) {
        $event = self::find($id);
        if ($event) {
            $event->informants()->detach();
            $event->recorders()->detach();
            $event->delete();
        }
    }    

    /**
     * remove event if exists and don't link with other texts
     * 
     * @param INT $event_id
     * @param INT $text_id
     */
    public static function removeUnused($event_id, $text_id) {
        if ($event_id && !Text::where('id','<>',$text_id)
                                  ->where('event_id',$event_id)
                                  ->count()) {
            $event = self::find($event_id);
            if ($event) {
                $event->informants()->detach();
                $event->recorders()->detach();
                $event->delete();
            }
        }
    }

    public function otherTexts($withoutText) {
        if (!$this) {
            return NULL;
        }
//dd($this->texts);        
        $texts = $this->texts->except("id",$withoutText->id);
dd($texts);        
        return $texts;
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
