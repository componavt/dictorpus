<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Import\DictParser;

// ./vendor/bin/phpunit tests/Library/Import/DictParserTest

class DictParserTest extends TestCase
{
    public function testParseLemmaPart_simple()
    {
        $dialect_id=47;
        $num='';
        $lemma_pos = 'aivoin adv';
        $result = DictParser::parseLemmaPart($lemma_pos, $num, $dialect_id);
        
        $expected = ['lemmas'=>['aivoin'],'pos_id'=>2];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseLemmaPart_withComma()
    {
        $dialect_id=47;
        $num='';
        $lemma_pos = 'aijalleh, aijaldi adv';
        $result = DictParser::parseLemmaPart($lemma_pos, $num, $dialect_id);
        
        $expected = ['lemmas'=>['aijalleh', 'aijaldi'],'pos_id'=>2];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseEntry_1lemma_1meaning_non_changeble()
    {
        $dialect_id=47;
        $num='';
        $line = 'a conj. – а, но – mutta, vaan, ja';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>3,
                     "lemmas"=>[0=>"a"],
                     "num"=> "",
                     "meanings"=>
                        [1=>['r'=>"а, но",
                             'f'=>"mutta, vaan, ja"]]
                    ];
        $this->assertEquals( $expected, $result);        
    }

    public function testParseEntry_1lemma_1meaning_3bases()
    {
        $dialect_id=47;
        $num='';
        $line = 'abiek|aš {-kaha, -ašta, -kahi} a. – грустный – surullinen';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>1,
                     "lemmas"=>[0=>"{abiekaš, abiekkaha, abiekkaha, abiekašta, abiekkahi, abiekkahi}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>['r'=>"грустный",
                             'f'=>"surullinen"]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseEntry_1lemma_2meanings_3bases()
    {
        $dialect_id=47;
        $num='';
        $line = 'čilu|ne {-ze, -sʼtʼa, -zi} s. – 1. погремушка 2. бубенчик – 1. helistin 2. kulkunen';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>5,
                     "lemmas"=>[0=>"{čilune, čiluze, čiluze, čilusʼtʼa, čiluzi, čiluzi}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>['r'=>"погремушка",
                             'f'=>"helistin"],
                         2=>['r'=>"бубенчик",
                             'f'=>"kulkunen"]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testParseEntry_1lemma_1meaning_4bases()
    {
        $dialect_id=47;
        $num='';
        $line = 'a|bu {-vu : -bu, -buo, -buloi} s. – помощь – apu';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>5,
                     "lemmas"=>[0=>"{abu, avu, abu, abuo, abuloi, abuloi}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>['r'=>"помощь",
                             'f'=>"apu"]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseEntry_2lemma_1meaning_7bases()
    {
        $dialect_id=47;
        $num='';
        $line = 'ahavoi|ja {-če : -čče, -či : -čči, -, -ja, -d}, ahavoit|tua {-a : -ta, -i : -ti, -ta, -eta, -ett} v. – обветрить, высушить ветром (пашню и пр.) – kuivattaa tuulessa';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>11,
                     "lemmas"=>
                        [0=>"{ahavoija, ahavoiče, ahavoičče, ahavoiči, ahavoičči, ahavoi, ahavoija, ahavoid}",
                         1=>"{ahavoittua, ahavoita, ahavoitta, ahavoiti, ahavoitti, ahavoitta, ahavoiteta, ahavoitett}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>['r'=>"обветрить, высушить ветром (пашню и пр.)",
                             'f'=>"kuivattaa tuulessa"]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseEntry_1lemma_2meanings_5bases()
    {
        $dialect_id=47;
        $num='';
        $line = 'aj|ua {-a, -oi, -a, -eta, -ett} v. – 1. ехать 2. гнать – 1. ajaa 2. ajaa pois';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>11,
                     "lemmas"=>[0=>"{ajua, aja, aja, ajoi, ajoi, aja, ajeta, ajett}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>['r'=>"ехать",
                             'f'=>"ajaa"],
                         2=>['r'=>"гнать",
                             'f'=>"ajaa pois"]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testParseEntry_1lemma_2meanings_7bases()
    {
        $dialect_id=47;
        $num='';
        $line = 'eis|työ {-sy : -ty, -syi : -ty, -ty, -sytä, -sytt} v. – 1. подвинуться, отодвинуться; переместиться 2. двигаться, проходить (о времени, действии) – siirtyä';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>11,
                     "lemmas"=>[0=>"{eistyö, eissy, eisty, eissyi, eisty, eisty, eissytä, eissytt}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>['r'=>"подвинуться, отодвинуться; переместиться",
                             'f'=>"siirtyä"],
                         2=>['r'=>"двигаться, проходить (о времени, действии)",
                             'f'=>"siirtyä"]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testParseEntry_1lemma_1meaning_2bases()
    {
        $dialect_id=47;
        $num='pl';
        $line = 'aluššo|vat {-vi : -bi} s. pl. – нижнее белье – alusvaatteet';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>5,
                     "lemmas"=>[0=>"{aluššovat, , , , aluššovi, aluššobi}"],
                     "num"=> "pl",
                     "meanings"=>
                        [1=>['r'=>"нижнее белье",
                             'f'=>"alusvaatteet"]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseEntry_1lemma_2meanings_5bases_def()
    {
        $dialect_id=47;
        $num='';
        $line = 'avau|duo {-du, -du, -du, -vuta, -vutt} v. def. – 1. открываться, раскрываться; распускаться 2. освобождаться (ото льда и т.д.) – avautua';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>11,
                     "lemmas"=>[0=>"{avauduo, , avaudu, , avaudu, avaudu, avauvuta, avauvutt}"],
                     "num"=> "def",
                     "meanings"=>
                        [1=>['r'=>"открываться, раскрываться; распускаться",
                             'f'=>"avautua"],
                         2=>['r'=>"освобождаться (ото льда и т.д.)",
                             'f'=>"avautua"]]
                    ];
        $this->assertEquals( $expected, $result);        
    }
     
}
