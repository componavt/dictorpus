<?php

namespace App\Library;

use LaravelLocalization;

use App\Models\Dict\Concept;
//use App\Models\Dict\ConceptCategory;
//use App\Models\Dict\Label;
//use App\Models\Dict\Lemma;
//use App\Models\Dict\LemmaWordform;
//use App\Models\Dict\PartOfSpeech;
//use App\Models\Dict\Relation;

//use App\Models\Corpus\Word;

class Ldl
{
    public static function alphabet() {
        $locale = LaravelLocalization::getCurrentLocale();
        return Concept::whereNotNull('text_'.$locale)->where('text_'.$locale, '<>', '')
                      ->forLdl()
                      ->selectRaw('substr(text_'.$locale.',1,1) as letter')
                      ->groupBy('letter')
                      ->orderBy('letter')
                      ->pluck('letter')->toArray();
    }
}
