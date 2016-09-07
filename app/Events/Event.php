<?php

namespace App\Events;

use App\Models\Corpus\Informant;
use App\Models\Corpus\Place;
use App\Models\Corpus\Recorder;

abstract class Event
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Stop tracking revisions after 500 changes have been made.
    protected $revisionCreationsEnabled = true; // By default the creation of a new model is not stored as a revision. Only subsequent changes to a model is stored.

    protected $fillable = ['informant','place_id','date'];
    
    // Event __belongs_to__ Informant
    public function informant()
    {
        return $this->belongsTo(Informant::class);
    }    

    // Event __belongs_to__ Place
    public function place()
    {
        return $this->belongsTo(Place::class);
    }    

    // Event __has_many__ Recorders
    public function recorders(){
        return $this->belongsToMany(Recorder::class);
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
    public function eventArray() //$lang_id
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
    
    /**
     * Gets full information about event as string
     * 
     * i.e. "Информант: Калинина Александра Леонтьевна, г.р. 1909, урожд. Пондала (Pondal), Бабаевский р-н, Вологодская обл., место записи: Петрозаводск, Республика Карелия, г. записи: 1957, записали: Богданов Николай Иванович, Зайцева Мария Ивановна"
     * 
     * @param int $lang_id ID of text language for output translation of settlement title, f.e. Pondal
     * 
     * @return String
     */
    public function eventString()//$lang_id
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
    
}
