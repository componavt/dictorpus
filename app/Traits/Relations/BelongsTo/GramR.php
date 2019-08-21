<?php namespace App\Traits\Relations\BelongsTo;

use App\Models\Dict\Gram;

trait GramR
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gramNumber()
    {
        return $this->belongsTo(Gram::class, 'gram_id_number');
    }
    
    public function gramCase()
    {
        return $this->belongsTo(Gram::class, 'gram_id_case');
    }
    
    public function gramTense()
    {
        return $this->belongsTo(Gram::class, 'gram_id_tense');
    }
    
    public function gramPerson()
    {
        return $this->belongsTo(Gram::class, 'gram_id_person');
    }
    
    public function gramMood()
    {
        return $this->belongsTo(Gram::class, 'gram_id_mood');
    }
    
    public function gramNegation()
    {
        return $this->belongsTo(Gram::class, 'gram_id_negation');
    }
    
    public function gramInfinitive()
    {
        return $this->belongsTo(Gram::class, 'gram_id_infinitive');
    }
    
    public function gramVoice()
    {
        return $this->belongsTo(Gram::class, 'gram_id_voice');
    }
    
    public function gramParticiple()
    {
        return $this->belongsTo(Gram::class, 'gram_id_participle');
    }
    
}