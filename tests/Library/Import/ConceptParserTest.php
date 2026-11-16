<?php

namespace Tests;

use Tests\IsolatedSqliteTestCase as TestCase;

use App\Library\Import\ConceptParser;

// ./vendor/bin/phpunit tests/Library/Import/ConceptParserTest

class ConceptParserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testChooseDialectsForLemmas()
    {
        $place_words = [
            '01' => ['a1'],
            '02' => ['a1'],
            '03' => ['a1'],
            '04' => ['a1'],
            '05' => ['c1'],
            '06' => ['a2'],
            '07' => ['a1'],
            '08' => ['b1'],
            '09' => ['b1'],
            10 => ['c3'],
            11 => ['c3'],
            12 => ['c2'],
            13 => ['c3'],
            14 => ['c4'],
            15 => ['a4'],
            16 => ['a3'],
            17 => ['a4'],
            18 => ['a4', 'b2'],
            19 => ['a4', 'b2'],
            20 => ['b2'],
            21 => ['a5', 'b1'],
            22 => ['b3'],
            23 => ['a4', 'b2'],
            24 => ['b4'],
            25 => ['c5'],
            26 => ['c5'],
            27 => ['c5'],
            28 => ['b3'],
            29 => ['c5'],
            30 => ['c5']
        ];

        $words = [
            "a1" => ["vasara", "vasara"],
            "a2" => ["bazara", "bazara"],
            "a3" => ["vazar", "vazar"],
            "a4" => ["vazaraine", "vazaraine"],
            "a5" => ["vazarane", "vazarane"],
            "b1" => ["pal’l’ane", "paĺĺane"],
            "b2" => ["pal’l’aine", "paĺĺaine"],
            "b3" => ["pal’l’aine", "paĺĺaińe"],
            "b4" => ["pal’l’aane", "paĺĺaańe"],
            "c1" => ["molotta", "molotta"],
            "c2" => ["malatta", "malatta"],
            "c3" => ["molotka", "molotka"],
            "c4" => ["molotk", "molotk"],
            "c5" => ["malat", "malat"]
        ];

        $place_dialects = [
            '01' => ['id' => 145, 'dialects' => [11 => 4]],
            '02' => ['id' => 233, 'dialects' => [52 => 4]],
            '03' => ['id' => 175, 'dialects' => [7 => 4]],
            '04' => ['id' => 232, 'dialects' => [10 => 4]],
            '05' => ['id' => 234, 'dialects' => [17 => 4]],
            '06' => ['id' => 140, 'dialects' => [16 => 4]],
            '07' => ['id' => 235, 'dialects' => [18 => 4]],
            '08' => ['id' => 236, 'dialects' => [19 => 4]],
            '09' => ['id' => 237, 'dialects' => [21 => 4]],
            10 => ['id' => 169, 'dialects' => [25 => 4]],
            11 => ['id' => 197, 'dialects' => [27 => 4]],
            12 => ['id' => 238, 'dialects' => [28 => 4]],
            13 => ['id' => 179, 'dialects' => [26 => 4]],
            14 => ['id' => 239, 'dialects' => [29 => 4]],
            15 => ['id' => 240, 'dialects' => [30 => 5]],
            16 => ['id' => 241, 'dialects' => [31 => 5]],
            17 => ['id' => 242, 'dialects' => [32 => 5]],
            18 => ['id' => 243, 'dialects' => [33 => 5]],
            19 => ['id' => 96, 'dialects' => [36 => 5]],
            20 => ['id' => 244, 'dialects' => [37 => 5]],
            21 => ['id' => 245, 'dialects' => [38 => 6]],
            22 => ['id' => 246, 'dialects' => [39 => 6]],
            23 => ['id' => 247, 'dialects' => [42 => 6]],
            24 => ['id' => 248, 'dialects' => [41 => 6]],
            25 => ['id' => 53, 'dialects' => [1 => 1]],
            26 => ['id' => 78, 'dialects' => [1 => 1]],
            27 => ['id' => 71, 'dialects' => [5 => 1]],
            28 => ['id' => 5, 'dialects' => [4 => 1]],
            29 => ['id' => 26, 'dialects' => [5 => 1]],
            30 => ['id' => 38, 'dialects' => [3 => 1]],
        ];

        $result = ConceptParser::chooseDialectsForLemmas(
            $place_words,
            $words,
            $place_dialects
        );

        $expected = [
            'a' => [
                "vasara" => [
                    4 => [
                        "vasara" => [
                            11 => [145],
                            52 => [233],
                            7 => [175],
                            10 => [232],
                            18 => [235]
                        ]
                    ]
                ],
                "bazara" => [
                    4 => [
                        "bazara" => [
                            16 => [140]
                        ]
                    ]
                ],
                "vazaraine" => [
                    5 => [
                        "vazaraine" => [
                            30 => [240],
                            32 => [242],
                            33 => [243],
                            36 => [96]
                        ]
                    ],
                    6 => [
                        "vazaraine" => [
                            42 => [247]
                        ]
                    ]
                ],
                "vazar" => [
                    5 => [
                        "vazar" => [
                            31 => [241]
                        ]
                    ]
                ],
                "vazarane" => [
                    6 => [
                        "vazarane" => [
                            38 => [245]
                        ]
                    ]
                ],
            ],
            'c' => [
                "molotta" => [
                    4 => [
                        "molotta" => [
                            17 => [234]
                        ]
                    ]
                ],
                "molotka" => [
                    4 => [
                        "molotka" => [
                            25 => [169],
                            27 => [197],
                            26 => [179]
                        ]
                    ]
                ],
                "malatta" => [
                    4 => [
                        "malatta" => [
                            28 => [238]
                        ]
                    ]
                ],
                "molotk" => [
                    4 => [
                        "molotk" => [
                            29 => [239]
                        ]
                    ]
                ],
                "malat" => [
                    1 => [
                        "malat" => [
                            1 => [53, 78],
                            5 => [71, 26],
                            3 => [38]
                        ]
                    ]
                ]
            ],
            'b' => [
                "pal’l’ane" => [
                    4 => [
                        "paĺĺane" => [
                            19 => [236],
                            21 => [237]
                        ]
                    ],
                    6 => [
                        "paĺĺane" => [
                            38 => [245]
                        ]
                    ]
                ],
                "pal’l’aine" => [
                    5 => [
                        "paĺĺaine" => [
                            33 => [243],
                            36 => [96],
                            37 => [244]
                        ]
                    ],
                    6 => [
                        "paĺĺaińe" => [
                            39 => [246]
                        ],
                        "paĺĺaine" => [
                            42 => [247]
                        ]
                    ],
                    1 => [
                        "paĺĺaińe" => [
                            4 => [5]
                        ]
                    ]
                ],
                "pal’l’aane" => [
                    6 => [
                        "paĺĺaańe" => [
                            41 => [248]
                        ]
                    ]
                ],
            ]
        ];
        $this->assertEquals($expected, $result);
    }

    public function testCheckWrongSymbols()
    {
        $line = 'alanko/alaηgo/alaηg||notko/notk||loga/logo/logandeh/logandez||lodma/lod’ma/lod’m/lodmu/lodm||orgo/ork/org||ložme||alava||alova paikka||alavo mua||alaw kohtu||madal kohto';

        $result = ConceptParser::checkWrongSymbols($line);
        $expected = true;
        $this->assertEquals($expected, $result);
    }
}
