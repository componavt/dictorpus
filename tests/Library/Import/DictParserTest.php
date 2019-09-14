<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Import\DictParser;

// ./vendor/bin/phpunit tests/Library/Import/DictParserTest

class DictParserTest extends TestCase
{
    public function testSplitLine_1lemma_3bases_1meaning()
    {
        $lang_id=4;
        $dialect_id=47;
        $line = 'limon|a {-a, -ua, -oi} s. – лимон – sitruuna';
        $result = DictParser::splitLine($line, $lang_id, $dialect_id);
        
        $expected = [0 => $line,
                     1 => 'limon|a {-a, -ua, -oi} s',
                     2 => '',
                     3 => 'лимон',
                     4 => 'sitruuna'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testSplitLine_Kar()
    {
        $lang_id=4;
        $dialect_id=47;
        $line = 'a conj. – а, но – mutta, vaan, ja';
        $result = DictParser::splitLine($line, $lang_id, $dialect_id);
        
        $expected = [0 => $line,
                     1 => 'a conj',
                     2 => '',
                     3 => 'а, но',
                     4 => 'mutta, vaan, ja'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testSplitLine_Veps()
    {
        $lang_id=1;
        $dialect_id=43;
        $line = 'aid||rižing (-on, -oid) s. - ветхая изгородь';
        $result = DictParser::splitLine($line, $lang_id, $dialect_id);
//dd($result);        
        $expected = [0 => $line,
                     1 => 'aid||rižing (-on, -oid) s',
                     2 => '',
                     3 => 'ветхая изгородь'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseLemmaPart_simple()
    {
        $lang_id=4;
        $dialect_id=47;
        $num='';
        $lemma_pos = 'aivoin adv';
        $result = DictParser::parseLemmaPart($lemma_pos, $num, $lang_id, $dialect_id);
        
        $expected = ['lemmas'=>['aivoin'],'pos_id'=>2];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseLemmaPart_withComma()
    {
        $lang_id=4;
        $dialect_id=47;
        $num='';
        $lemma_pos = 'aijalleh, aijaldi adv';
        $result = DictParser::parseLemmaPart($lemma_pos, $num, $lang_id, $dialect_id);
        
        $expected = ['lemmas'=>['aijalleh', 'aijaldi'],'pos_id'=>2];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseLemmaPart_Veps()
    {
        $lang_id=1;
        $dialect_id=43;
        $num='';
        $lemma_pos = 'aid||rižing (-on, -oid) s';
        $result = DictParser::parseLemmaPart($lemma_pos, $num, $lang_id, $dialect_id);
        
        $expected = ['lemmas'=>['aid||rižing (-on, -oid)'],'pos_id'=>5];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseEntry_1lemma_1meaning_non_changeble()
    {
        $lang_id=4;
        $dialect_id=47;
        $line = 'a conj. – а, но – mutta, vaan, ja';
        $result = DictParser::parseEntry($line, $lang_id, $dialect_id);
//dd($result);        
        $expected = ["pos_id"=>3,
                     "lemmas"=>[0=>"a"],
                     "num"=> "",
                     "meanings"=>
                        [1=>['ru'=>"а, но",
                            // 'fi'=>"mutta, vaan, ja"
                            ]]
                    ];
        $this->assertEquals( $expected, $result);        
    }

    public function testParseEntry_1lemma_1meaning_3bases()
    {
        $lang_id=4;
        $dialect_id=47;
        $line = 'abiek|aš {-kaha, -ašta, -kahi} a. – грустный – surullinen';
        $result = DictParser::parseEntry($line, $lang_id, $dialect_id);
        
        $expected = ["pos_id"=>1,
                     "lemmas"=>[0=>"{abiekaš, abiekkaha, abiekkaha, abiekašta, abiekkahi, abiekkahi}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>['ru'=>"грустный",
                            // 'fi'=>"surullinen"
                            ]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
     
    public function testParseEntry_1lemma_2meanings_3bases()
    {
        $lang_id=4;
        $dialect_id=47;
        $line = 'čilu|ne {-ze, -sʼtʼa, -zi} s. – 1. погремушка 2. бубенчик – 1. helistin 2. kulkunen';
        $result = DictParser::parseEntry($line, $lang_id, $dialect_id);
        
        $expected = ["pos_id"=>5,
                     "lemmas"=>[0=>"{čilune, čiluze, čiluze, čilusʼtʼa, čiluzi, čiluzi}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>['ru'=>"погремушка",
                            // 'fi'=>"helistin"
                            ],
                         2=>['ru'=>"бубенчик",
                             //'fi'=>"kulkunen"
                             ]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testParseEntry_1lemma_1meaning_4bases()
    {
        $lang_id=4;
        $dialect_id=47;
        $line = 'a|bu {-vu : -bu, -buo, -buloi} s. – помощь – apu';
        $result = DictParser::parseEntry($line, $lang_id, $dialect_id);
        
        $expected = ["pos_id"=>5,
                     "lemmas"=>[0=>"{abu, avu, abu, abuo, abuloi, abuloi}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>['ru'=>"помощь",
                            // 'fi'=>"apu"
                            ]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseEntry_2lemma_1meaning_7bases()
    {
        $lang_id=4;
        $dialect_id=47;
        $line = 'ahavoi|ja {-če : -čče, -či : -čči, -, -ja, -d}, ahavoit|tua {-a : -ta, -i : -ti, -ta, -eta, -ett} v. – обветрить, высушить ветром (пашню и пр.) – kuivattaa tuulessa';
        $result = DictParser::parseEntry($line, $lang_id, $dialect_id);
        
        $expected = ["pos_id"=>11,
                     "lemmas"=>
                        [0=>"{ahavoija, ahavoiče, ahavoičče, ahavoiči, ahavoičči, ahavoi, ahavoija, ahavoid}",
                         1=>"{ahavoittua, ahavoita, ahavoitta, ahavoiti, ahavoitti, ahavoitta, ahavoiteta, ahavoitett}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>['ru'=>"обветрить, высушить ветром (пашню и пр.)",
                            // 'fi'=>"kuivattaa tuulessa"
                            ]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseEntry_1lemma_2meanings_5bases()
    {
        $lang_id=4;
        $dialect_id=47;
        $line = 'aj|ua {-a, -oi, -a, -eta, -ett} v. – 1. ехать 2. гнать – 1. ajaa 2. ajaa pois';
        $result = DictParser::parseEntry($line, $lang_id, $dialect_id);
        
        $expected = ["pos_id"=>11,
                     "lemmas"=>[0=>"{ajua, aja, aja, ajoi, ajoi, aja, ajeta, ajett}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>['ru'=>"ехать",
                            // 'fi'=>"ajaa"
                            ],
                         2=>['ru'=>"гнать",
                             //'fi'=>"ajaa pois"
                             ]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testParseEntry_1lemma_2meanings_7bases()
    {
        $lang_id=4;
        $dialect_id=47;
        $line = 'eis|työ {-sy : -ty, -syi : -ty, -ty, -sytä, -sytt} v. – 1. подвинуться, отодвинуться; переместиться 2. двигаться, проходить (о времени, действии) – siirtyä';
        $result = DictParser::parseEntry($line, $lang_id, $dialect_id);
        
        $expected = ["pos_id"=>11,
                     "lemmas"=>[0=>"{eistyö, eissy, eisty, eissyi, eisty, eisty, eissytä, eissytt}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>['ru'=>"подвинуться, отодвинуться; переместиться",
                            // 'fi'=>"siirtyä"
                            ],
                         2=>['ru'=>"двигаться, проходить (о времени, действии)",
                             //'fi'=>"siirtyä"
                             ]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testParseEntry_1lemma_1meaning_2bases()
    {
        $lang_id=4;
        $dialect_id=47;
        $line = 'aluššo|vat {-vi : -bi} s. pl. – нижнее белье – alusvaatteet';
        $result = DictParser::parseEntry($line, $lang_id, $dialect_id);
        
        $expected = ["pos_id"=>5,
                     "lemmas"=>[0=>"{aluššovat, , , , aluššovi, aluššobi}"],
                     "num"=> "pl",
                     "meanings"=>
                        [1=>['ru'=>"нижнее белье",
                            // 'fi'=>"alusvaatteet"
                            ]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseEntry_1lemma_2meanings_5bases_def()
    {
        $lang_id=4;
        $dialect_id=47;
        $line = 'avau|duo {-du, -du, -du, -vuta, -vutt} v. def. – 1. открываться, раскрываться; распускаться 2. освобождаться (ото льда и т.д.) – avautua';
        $result = DictParser::parseEntry($line, $lang_id, $dialect_id);
        
        $expected = ["pos_id"=>11,
                     "lemmas"=>[0=>"{avauduo, , avaudu, , avaudu, avaudu, avauvuta, avauvutt}"],
                     "num"=> "def",
                     "meanings"=>
                        [1=>['ru'=>"открываться, раскрываться; распускаться",
                            // 'fi'=>"avautua"
                            ],
                         2=>['ru'=>"освобождаться (ото льда и т.д.)",
                             //'fi'=>"avautua"
                             ]]
                    ];
        $this->assertEquals( $expected, $result);        
    }     
    
    public function testParseEntry_meaning_with_commas()
    {
        $lang_id=4;
        $dialect_id=47;
        $line = 'ni conj. – ни ... ни – ei ... eikä';
        $result = DictParser::parseEntry($line, $lang_id, $dialect_id);
        
        $expected = ["pos_id"=>3,
                     "lemmas"=>[0=>"ni"],
                     "num"=> "",
                     "meanings"=>
                        [1=>['ru'=>"ни ... ни",
                            // 'fi'=>"ei ... eikä"
                            ]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseEntry_1lemma_1meaning_verb_3bases()
    {
        $lang_id=4;
        $dialect_id=47;
        $line = 'vihmu|o {-, -, -, , } v. def. – дождить, идти дождю – sataa';
        $result = DictParser::parseEntry($line, $lang_id, $dialect_id);
        
        $expected = ["pos_id"=>11,
                     "lemmas"=>[0=>"{vihmuo, , vihmu, , vihmu, vihmu, , }"],
                     "num"=> "def",
                     "meanings"=>
                        [1=>['ru'=>"дождить, идти дождю",
                            // 'fi'=>"sataa"
                            ]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseEntry_Veps_1meaning()
    {
        $lang_id=1;
        $dialect_id=43;
        $line = 'aid||rižing (-on, -oid) s. - ветхая изгородь';
        $result = DictParser::parseEntry($line, $lang_id, $dialect_id);
        
        $expected = ["pos_id"=>5,
                     "lemmas"=>[0=>"aid||rižing (-on, -oid)"],
                     "num"=> "",
                     "meanings"=>
                        [1=>['ru'=>"ветхая изгородь"]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
   
}
