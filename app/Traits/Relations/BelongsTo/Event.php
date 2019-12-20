<?php namespace App\Traits\Relations\BelongsTo;


trait Event
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo('App\Models\Corpus\Event');
    }
    
    /**
     * Gets IDs of informants for informant's form field
     *
     * @return Array
     */
    public function informantValue():Array{
        $informant_value = [];
        if ($this->event && $this->event->informants) {
            foreach ($this->event->informants as $informant) {
                $informant_value[] = $informant->id;
            }
        }
        return $informant_value;
    }

    /**
     * Gets IDs of recorders for record's form field
     *
     * @return Array
     */
    public function recorderValue():Array{
        $recorder_value = [];
        if ($this->event && $this->event->recorders) {
            foreach ($this->event->recorders as $recorder) {
                $recorder_value[] = $recorder->id;
            }
        }
        return $recorder_value;
    }

}