<?php namespace App\Traits\Relations\BelongsTo;

trait ParentTrait
{
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}