<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Misc Monument Lines
    |--------------------------------------------------------------------------
    |
    |
    */

    'archive' => 'archive data (archive name, code)',
    'author' => 'author / compiler / translator',  
    'bibl_descr' => 'full bibliographic description',
    'comment' => 'comment',
    'dcopy_link' => 'link to a digital copy of the original',
    'graphic' => 'graphics',
    'graphic_values' => [
        1 => 'Cyrillic (uncial)',
        2 => 'Cyrillic (semi-uncial)',
        3 => 'Cyrillic (cursive)',
        4 => 'Cyrillic (civil script)',
        5 => 'Cyrillic (civil writing)',
        6 => 'Latin'
    ],
    'has_trans' => 'availability of translation/original (in Russian)',
    'has_trans_values' => [
        1 => 'yes',
        0 => 'no',
    ],
    'is_printed' => 'variety',
    'is_printed_values' => [
        0 => 'printed',
        1 => 'handwritten',
    ],
    'lang' => 'language of the monument (dialect)',
    'pages' => 'pages with Karelian/Vepsian text',
    'place' => 'place of creation/publication',
    'publ' => 'publication of the text of the monument',
    'publ_date' => 'date of creation/publication',
    'study' => 'monument study',
    'type' => 'type of monument',
    'type_values' => [
        1 => 'dictionary',
        2 => 'translation',
        3 => 'grammar essay',
    ],
    'volume' => 'volume of Karelian/Vepsian text (words)',
];
