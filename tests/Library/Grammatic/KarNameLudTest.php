<?php

//use Illuminate\Foundation\Testing\WithoutMiddleware;
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\KarNameLud;

// php artisan make:test Library\Grammatic\KarNameLudTest
// ./vendor/bin/phpunit tests/Library/Grammatic\KarNameLudTest

class KarVerbLudTest extends TestCase
{
    public function testSuggestTemplates() {
        $words = [
	    1 => 'ukko',
	    2 => 'kirikkö',
	    3 => 'počči',
	    4 => 'luad',
	    5 => 'lyhyd',
	    6 => 'kätkyd',
	    7 => 'vikateh',
	    8 => 'oruž',
	    9 => 'tobd’',
	    10 => 'oiged',
	    11 => 'bumag',
	    12 => 'abuniek',
	    13 => 'madal',
	    14 => 'tuatindam',
	    15 => 'taigin',
	    16 => 'tavar',
	    17 => 'ohot',
	    18 => 'vagav',
	    19 => 'köyh',
	    20 => 'd’auh',
	    21 => 'agan',
	    22 => 'hamar',
	    23 => 'ruaht',
	    24 => 'roiv',
	    25 => 'vessel',
	    26 => 'kuudain',
	    27 => 'uudim',
	    28 => 'd’oučen',
	    29 => 'nenahuogain',
	    30 => 'lumi',
	    31 => 'humbar',
	    32 => 'tytär',
	    33 => 'pelvas',
	    34 => 'taivaz',
	    35 => 'händikäs',
	    36 => 'ruopas',
	    37 => 'ratas',
	    38 => 'kyhläs',
	    39 => 'viizaš',
	    40 => 'kirves',
	    41 => 'kuurniš',
	    42 => 'nagriž',
	    43 => 'voimattomus',
	    44 => 'kevät',
	    45 => 'rinduz',
	    46 => 'teräs',
	    47 => 'redukaz',
	    48 => 'mätäz',
	    49 => 'kirvez',
	    50 => 'miez’',
	    51 => 'homeh',
	    52 => 'sygyz',
	    53 => 'bobra',
	    54 => 'halgo',
	    55 => 'hirve',
	    56 => 'cairi',
	    57 => 'tieduoinikka',
	    58 => 'räkke',
	    59 => 'počči',
	    60 => 'tuatto',
	    61 => 'maikku',
	    62 => 'meččuoi',
	    63 => 'apara',
	    64 => 'šiliä',
	    65 => 'lyhyt',
	    66 => 'sorze',
	    67 => 'habukk',
	    68 => 'hid’v’u',
	    69 => 'kirzi',
	    70 => 'buabo',
	    71 => 'mahlu',
	    72 => 'brihačče',
	    73 => 'pille',
	    74 => 'tuučče',
	    75 => 'därčäkke',
	    76 => 'skuuppe',
	    77 => 'komšše',
	    78 => 'vuasse',
	    79 => 'lautte',
	    80 => 'tobde',
	    81 => 'labd’e',
	    82 => 'petle',
	    83 => 'ruble',
	    84 => 'pihle',
	    85 => 'side',
	    86 => 'turme',
	    87 => 'päre',
	    88 => 'kaste',
	    89 => 'bošše',
	    90 => 'pyörei',
	    91 => 'br’ukve',
	    92 => 'rassale',
	    93 => 'talle',
	    94 => 'sulaime',
	    95 => 'kyhkyine',
	    96 => 'inehmine',
	    97 => 'mielehiine',
	    98 => 'syömiin’e',
	    99 => 'inehmine',
	    100 => 'näre',
	    101 => 'kypse',
	    102 => 'late',
	    103 => 'vate',
	    104 => 'uušte',
	    105 => 'ruoste',
	    106 => 'udžve',
	    107 => 'perze',
	    108 => 'täyze',
	    109 => 'hyvyz',
	    110 => 'suvi',
	    111 => 'taimen’',
	    112 => 'abai',
	    113 => 'abei',
	    114 => 'kondii',
	    115 => 'virui',
	    116 => 'harmai',
	    117 => 'veičči',
	    118 => 'anoppi',
	    119 => 'levei',
	    120 => 'astii',
	    121 => 'hiili',
	    122 => 'syli',
	    123 => 'sinini',
	    124 => 'iänetöi',
	    125 => 'kieletyöi',
	    126 => 'uksi',
	    127 => 'kypsi',
	    128 => 'uuzi',
	    129 => 'reiži',
	    130 => 'kuuzi',
	    131 => 'tikku',
	    132 => 'varačču',
	    133 => 'kinttu',
	    134 => 'paarussu',
	    135 => 'palau',
	    136 => 'mielöy',
	];        
        $result = [];
        foreach ($words as $lemma_id=>$word) {
            $result[$lemma_id] = KarNameLud::suggestTemplates($word);
        }
	$expected = [
	    1 => ['ukko []', 'ukk|o [a]', 'uk|ko [o]'],
	    2 => ['kirikk|ö [ä]', 'kirikkö []', 'kirik|kö [ö]'],
	    3 => ['poč|či [e]', 'poč|či [i]', 'počč|i [a]', 'počč|i [e]'],
	    4 => ['luad [a]', 'luad [u]'],
	    5 => ['lyhyd [ä]', 'lyhy|d [de, t]', 'lyhy|d [dä, t]'],
	    6 => ['kätkyd [ä]', 'kätky|d [de, t]', 'kätky|d [dä, t]'],
	    7 => ['vikateh [a]', 'vikateh [e]', 'vikateh [o]', 'vikat|eh [tehe, eh]'],
	    8 => ['oruž [a]'],
	    9 => ['tobd’ [a]'],
	    10 => ['oiged [a]'],
	    11 => ['bumag [a]'],
	    12 => ['abuniek [a]'],
	    13 => ['madal [a]', 'madal [i]', 'madal [’a]'],
	    14 => ['tuatindam [a]', 'tuatinda|m [me, n]'],
	    15 => ['taigin [a]', 'taigin [o]', 'taig|in [ime, n]', 'taig|in [me, n]'],
	    16 => ['tavar [a]', 'tavar [e, ]', 'tavar [o]'],
	    17 => ['ohot [a]', 'ohot [o]', 'oho|t [da]'],
	    18 => ['vagav [a]', 'vagav [o]'],
	    19 => ['köyh [e]', 'köyh [ä]', 'köyh [ö]'],
	    20 => ['d’auh [a]', 'd’auh [e]', 'd’auh [o]'],
	    21 => ['agan [a]', 'agan [o]', 'aga|n [me, n]'],
	    22 => ['hamar [a]', 'hamar [e, ]', 'hamar [o]'],
	    23 => ['ruaht [a]', 'ruaht [o]', 'ruah|t [da]'],
	    24 => ['roiv [a]', 'roiv [o]'],
	    25 => ['vessel [i]', 'vessel [ä]', 'vessel [’ä]'],
	    26 => ['kuudain [a]', 'kuudain [o]', 'kuuda|in [ime, n]', 'kuuda|in [me, n]'],
	    27 => ['uudim [a]', 'uudi|m [me, n]'],
	    28 => ['d’oučen [a]', 'd’oučen [o]', 'd’ouče|n [me, n]'],
	    29 => ['nenahuogain [a]', 'nenahuogain [o]', 'nenahuoga|in [ime, n]', 'nenahuoga|in [me, n]'],
	    30 => ['lum|i [a]', 'lum|i [e]', 'lu|mi [me, n]'],
	    31 => ['humbar [a]', 'humbar [e, ]', 'humbar [o]'],
	    32 => ['tyt|är [täre, är]', 'tytär [e, ]', 'tytär [ä]', 'tytär [ö]'],
	    33 => ['pelva|s [de, s]', 'pelva|s [ha, s]', 'pelva|s [ha, š]', 'pelva|s [kse, s]'],
	    34 => ['taivaz [e]', 'taiva|z [ha, s]', 'taiva|z [kse, s]'],
	    35 => ['händik|äs [kähä, äs]', 'händikä|s [de, s]', 'händikä|s [hä, s]', 'händikä|s [hä, š]', 'händikä|s [kse, s]'],
	    36 => ['ruopa|s [de, s]', 'ruopa|s [ha, s]', 'ruopa|s [ha, š]', 'ruopa|s [kse, s]', 'ruop|as [paha, as]'],
	    37 => ['rata|s [de, s]', 'rata|s [ha, s]', 'rata|s [ha, š]', 'rata|s [kse, s]', 'rat|as [taha, as]'],
	    38 => ['kyhlä|s [de, s]', 'kyhlä|s [hä, s]', 'kyhlä|s [hä, š]', 'kyhlä|s [kse, s]'],
	    39 => ['viiza|š [ha, š]'],
	    40 => ['kirve|s [he, s]', 'kirve|s [kse, s]'],
	    41 => ['kuurn|iš [ehe, iš]'],
	    42 => ['nagriž [a]', 'nagr|iž [ehe, iš]'],
	    43 => ['voimattomu|s [de, s]', 'voimattomu|s [kse, s]'],
	    44 => ['kevät [ä]', 'kevät [ö]', 'kevä|t [de, t]', 'kevä|t [dä]'],
	    45 => ['rinduz [e]', 'rindu|z [de, t]', 'rindu|z [kse, s]'],
	    46 => ['terä|s [de, s]', 'terä|s [hä, s]', 'terä|s [hä, š]', 'terä|s [kse, s]'],
	    47 => ['redukaz [e]', 'reduka|z [ha, s]', 'reduka|z [kse, s]', 'reduk|az [kaha, as]'],
	    48 => ['mät|äz [tähä, äs]', 'mätäz [e]', 'mätä|z [hä, s]', 'mätä|z [kse, s]'],
	    49 => ['kirvez [e]', 'kirve|z [he, s]', 'kirve|z [kse, s]'],
	    50 => ['mie|z’ [he, s]'],
	    51 => ['homeh [a]', 'homeh [e]', 'homeh [o]'],
	    52 => ['sygyz [e]', 'sygy|z [de, t]', 'sygy|z [kse, s]'],
	    53 => ['bobra []', 'bobr|a [o]'],
	    54 => ['halgo []', 'halg|o [a]'],
	    55 => ['hirve []', 'hirv|e [i]', 'hirv|e [ä]'],
	    56 => ['cairi []', 'cair|i [a]', 'cair|i [e]'],
	    57 => ['tieduoinikka []', 'tieduoinikk|a [o]', 'tieduoinik|ka [a]'],
	    58 => ['räkke []', 'räkk|e [ä]', 'räk|ke [e]', 'räk|ke [ä]'],
	    59 => ['poč|či [e]', 'poč|či [i]', 'počč|i [a]', 'počč|i [e]'],
	    60 => ['tuatto []', 'tuatt|o [a]', 'tuat|to [o]'],
	    61 => ['maikk|u [a]', 'maik|ku [a]', 'maik|ku [u]'],
	    62 => ['meč|čuoi [uoi]', 'meččuo|i [ja]'],
	    63 => ['apara []', 'apar|a [o]'],
	    64 => ['šili|ä [dä]'],
	    65 => ['lyhyt [ä]', 'lyhyt [ö]', 'lyhy|t [de, t]', 'lyhy|t [dä]'],
	    66 => ['sorze []', 'sorz|e [a]', 'sorz|e [ie, et]', 'sor|ze [de, t]'],
	    67 => ['habukk [a]', 'habuk|k [a]'],
	    68 => ['hid’v|’u [a]'],
	    69 => ['kirz|i [e]', 'kirz|i [ä]', 'kir|zi [de, t]', 'kir|zi [ze, s]'],
	    70 => ['buabo []', 'buab|o [a]'],
	    71 => ['mahl|u [a]'],
	    72 => ['brihač|če [a]', 'brihač|če [e]', 'brihač|če [u]', 'brihačče []', 'brihačč|e [a]'],
	    73 => ['pille []', 'pill|e [i]', 'pill|e [y]', 'pill|e [ä]', 'pil|le [’l’a]'],
	    74 => ['tuuč|če [a]', 'tuuč|če [e]', 'tuuč|če [u]', 'tuučče []', 'tuučč|e [a]'],
	    75 => ['därčäkke []', 'därčäkk|e [ä]', 'därčäk|ke [e]', 'därčäk|ke [ä]'],
	    76 => ['skuuppe []', 'skuupp|e [a]', 'skuup|pe [a]', 'skuup|pe [e]'],
	    77 => ['komš|še [a]', 'komš|še [e]', 'komš|še [i]', 'komšše []', 'komšš|e [a]'],
	    78 => ['vuasse []', 'vuass|e [a]', 'vuas|se [a]', 'vuas|se [e]'],
	    79 => ['lautte []', 'lautte [ge, t]', 'lautt|e [a]', 'laut|te [a]', 'laut|te [e]'],
	    80 => ['tobde []', 'tobde [ge, t]', 'tobd|e [a]', 'tobd|e [’a]'],
	    81 => ['labd|’e [’a]', 'labd’e []', 'labd’|e [a]'],
	    82 => ['petle []', 'petl|e [i]', 'petl|e [ä]', 'petl|e [’ä]'],
	    83 => ['ruble []', 'rubl|e [a]', 'rubl|e [i]', 'rubl|e [’a]'],
	    84 => ['pihle []', 'pihl|e [i]', 'pihl|e [ä]', 'pihl|e [’ä]'],
	    85 => ['side []', 'side [ge, t]', 'sid|e [ä]', 'sid|e [’ä]'],
	    86 => ['turme []', 'turme [ge, t]', 'turm|e [a]', 'tur|me [me, n]'],
	    87 => ['päre []', 'päre [ge, t]', 'pär|e [ge, t]', 'pär|e [ä]'],
	    88 => ['kaste []', 'kaste [ge, s]', 'kaste [ge, t]', 'kast|e [a]', 'kast|e [tege, et]', 'kast|e [’t’a]'],
	    89 => ['boš|še [a]', 'boš|še [e]', 'boš|še [i]', 'bošše []', 'bošš|e [a]'],
	    90 => ['pyöre|i [dä]', 'pyöre|i [jä]', 'pyör|ei [i]'],
	    91 => ['br’ukve []', 'br’ukv|e [a]', 'br’ukv|e [i]'],
	    92 => ['rassale []', 'rassal|e [a]', 'rassal|e [i]'],
	    93 => ['talle []', 'tall|e [a]', 'tall|e [i]', 'tall|e [u]', 'tal|le [’l’a]'],
	    94 => ['sulaime []', 'sulaime [ge, t]', 'sulaim|e [a]', 'sulai|me [me, n]'],
	    95 => ['kyhkyine []', 'kyhkyin|e [ä]', 'kyhky|ine [iže, š]', 'kyhky|ine [že, š]'],
	    96 => ['inehmine []', 'inehmin|e [ä]', 'inehmi|ne [že, š]'],
	    97 => ['mielehiine []', 'mielehiin|e [ä]', 'mielehi|ine [iže, š]', 'mielehi|ine [že, š]'],
	    98 => ['syömiin’e []', 'syömiin’|e [ä]', 'syömii|n’e [že, š]'],
	    99 => ['inehmine []', 'inehmin|e [ä]', 'inehmi|ne [že, š]'],
	    100 => ['näre []', 'näre [ge, t]', 'när|e [ge, t]', 'när|e [ä]'],
	    101 => ['kypse []', 'kyps|e [ä]', 'ky|pse [pse, s]'],
	    102 => ['late []', 'late [ge, t]', 'lat|e [a]', 'lat|e [tege, et]', 'lat|e [’t’a]'],
	    103 => ['vate []', 'vate [ge, t]', 'vat|e [a]', 'vat|e [tege, et]', 'vat|e [’t’a]'],
	    104 => ['uušte []', 'uušte [ge, s]', 'uušte [ge, t]', 'uušt|e [a]', 'uušt|e [tege, et]', 'uušt|e [’t’a]'],
	    105 => ['ruoste []', 'ruoste [ge, s]', 'ruoste [ge, t]', 'ruost|e [a]', 'ruost|e [tege, et]', 'ruost|e [’t’a]'],
	    106 => ['udžve []', 'udžve [he, t]', 'udžv|e [a]', 'udžv|e [i]'],
	    107 => ['perze []', 'perz|e [ie, et]', 'perz|e [ä]', 'per|ze [de, t]'],
	    108 => ['täyze []', 'täyz|e [ä]', 'täy|ze [de, t]'],
	    109 => ['hyvyz [e]', 'hyvy|z [de, t]', 'hyvy|z [kse, s]'],
	    110 => ['suv|i [a]', 'suv|i [e]'],
	    111 => ['taimen|’ [e]', 'taimen’ [a]'],
	    112 => ['aba|i [ga, s]', 'aba|i [ja]'],
	    113 => ['abe|i [da]', 'abe|i [ja]', 'ab|ei [i]'],
	    114 => ['kondi|i [ja]', 'kond|ii [’ai]'],
	    115 => ['viru|i [ja]'],
	    116 => ['harma|i [ga, s]', 'harma|i [ja]'],
	    117 => ['veič|či [e]', 'veič|či [i]', 'veičč|i [e]', 'veičč|i [ä]'],
	    118 => ['anopp|i [a]', 'anopp|i [e]', 'anop|pi [e]', 'anop|pi [i]'],
	    119 => ['leve|i [dä]', 'leve|i [jä]', 'lev|ei [i]'],
	    120 => ['asti|i [ja]','ast|ii [’ai]'],
	    121 => ['hiil|i [e, t]', 'hiil|i [e]', 'hiil|i [ä]', 'hii|li [le, l]'],
	    122 => ['syl|i [e, t]', 'syl|i [e]', 'syl|i [ä]', 'sy|li [le, l]'],
	    123 => ['sinin|i [e]', 'sinin|i [ä]', 'sini|ni [že, š]'],
	    124 => ['iänet|öi [tömä, ön]', 'iänetö|i [jä]'],
	    125 => ['kieletyö|i [jä]','kielet|yöi [tömä, yön]'],
	    126 => ['uks|i [a]', 'uks|i [e]', 'u|ksi [kse, s]'],
	    127 => ['kyps|i [e]', 'kyps|i [ä]', 'ky|psi [pse, s]'],
	    128 => ['uuz|i [a]', 'uuz|i [e]', 'uu|zi [de, t]', 'uu|zi [ze, s]'],
	    129 => ['rei|ži [de, t]', 'reiž|i [e]', 'reiž|i [ä]'],
	    130 => ['kuuz|i [a]', 'kuuz|i [e]', 'kuu|zi [de, t]', 'kuu|zi [ze, s]'],
	    131 => ['tikk|u [a]', 'tik|ku [a]', 'tik|ku [u]'],
	    132 => ['varač|ču [a]', 'varač|ču [u]', 'varačč|u [a]'],
	    133 => ['kintt|u [a]', 'kint|tu [a]', 'kint|tu [u]'],
	    134 => ['paaruss|u [a]', 'paarus|su [a]', 'paarus|su [u]'],
	    135 => ['pala|u [va]'],
	    136 => ['mielö|y [vä]'],
      ];
 
        $this->assertEquals( $expected, $result);        
    }
}
