<?php namespace App\Traits\Relations\BelongsToMany;

trait Toponyms
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function Toponyms(){
        $builder = $this->belongsToMany('App\Models\Topkar\Toponym');
        return $builder;
    }
    
    public function toponymIDs($div = '; '){
        return join($div, $this->texts()->pluck('id')->toArray());
    }    
    
    public function toponymUrls($div = '; '){
        $out = [];
        foreach ($this->toponyms as $toponym) {
            $out[] = '<a href="'.env('TOPKAR_URL').app()->getLocale().'/dict/toponyms/'.$toponym->id.'">'.$toponym->name.'</a>';
        }
        return join($div, $out);
    }    
}