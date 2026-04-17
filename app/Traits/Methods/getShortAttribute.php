<?php

namespace App\Traits\Methods;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

trait getShortAttribute
{
    /** Gets name of this object, takes into account locale.
     * 
     * @return String
     */
    public function getShortAttribute(): String
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $column = "short_" . $locale;
        $name = $this->{$column};

        if (!$name && $locale != 'ru') {
            $name = $this->short_ru;
        }

        return $name ? $name : '';
    }
}
