<?php

namespace App\Traits\Scopes;

trait ForLangAndPos
{
    /*    public function scopeForLangAndPos($query, $langId, $posId)
    {
        return $query
            ->where('lang_id', $langId)
            ->where('pos_id', $posId);
    }*/
    public static function forLangAndPos($langId, $posId)
    {
        return static::query()
            ->where('lang_id', $langId)
            ->where('pos_id', $posId);
    }
}
