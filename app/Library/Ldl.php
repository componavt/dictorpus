<?php

namespace App\Library;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Models\Dict\Concept;

class Ldl
{
    public static function alphabet()
    {
        $locale = LaravelLocalization::getCurrentLocale();
        return Concept::whereNotNull('text_' . $locale)->where('text_' . $locale, '<>', '')
            ->forLdl()
            ->selectRaw('substr(text_' . $locale . ',1,1) as letter')
            ->groupBy('letter')
            ->orderBy('letter')
            ->pluck('letter')->toArray();
    }

    public static function concepts($search_letter = null)
    {
        $locale = LaravelLocalization::getCurrentLocale();
        $concepts = Concept::whereNotNull('text_' . $locale)
            ->forLdl();
        if ($search_letter) {
            $concepts->where('text_' . $locale, 'like', $search_letter . '%');
        }
        return $concepts->orderBy('text_' . $locale)->get();
    }
}
