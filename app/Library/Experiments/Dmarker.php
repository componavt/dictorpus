<?php

namespace App\Library\Experiments;

use Illuminate\Database\Eloquent\Model;

use App\Library\Experiments\Mvariant;

class Dmarker extends Model
{
    public $timestamps = false;
    protected $fillable = ['id', 'name', 'absence'];
    
    // Belongs To Many Relations
    use \App\Traits\Relations\BelongsToMany\Dialects;
    
    public function mvariants(){
        return $this->hasMany(Mvariant::class);
    }
    
}
