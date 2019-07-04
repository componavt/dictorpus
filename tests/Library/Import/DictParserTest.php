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
        $line = 'a conj. – а, но';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>3,
                     "lemmas"=>[0=>"a"],
                     "num"=> "",
                     "meanings"=>
                        [1=>"а, но"]
                    ];
        $this->assertEquals( $expected, $result);        
    }

    public function testParseEntry_1lemma_1meaning_3bases()
    {
        $dialect_id=47;
        $num='';
        $line = 'abiek|aš {-kaha, -ašta, -kahi} a. – грустный, жалобный';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>1,
                     "lemmas"=>[0=>"{abiekaš, abiekkaha, abiekkaha, abiekašta, abiekkahi, abiekkahi}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>"грустный, жалобный"]
                    ];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseEntry_1lemma_2meanings_3bases()
    {
        $dialect_id=47;
        $num='';
        $line = 'abie {-, -da, -loi} s. – 1. обида, горечь 2. грустный, обидный';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>5,
                     "lemmas"=>[0=>"{abie, abie, abie, abieda, abieloi, abieloi}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>"обида, горечь",
                         2=>"грустный, обидный"]
                    ];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testParseEntry_1lemma_1meaning_4bases()
    {
        $dialect_id=47;
        $num='';
        $line = 'a|bu {-vu / -bu, -buo, -buloi} s. – помощь, поддержка; подспорье';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>5,
                     "lemmas"=>[0=>"{abu, avu, abu, abuo, abuloi, abuloi}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>"помощь, поддержка; подспорье"]
                    ];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseEntry_2lemma_1meaning_7bases()
    {
        $dialect_id=47;
        $num='';
        $line = 'ahavoit|tua {-a / -ta, -i / -ti, -ta, -eta, -ett}, ahavoi|ja {-če / -ččo, -či / -čči, -, -ja, -d} v. – обветрить, высушить ветром (пашню и пр.)';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>11,
                     "lemmas"=>
                        [0=>"{ahavoittua, ahavoita, ahavoitta, ahavoiti, ahavoitti, ahavoitta, ahavoiteta, ahavoitett}",
                         1=>"{ahavoija, ahavoiče, ahavoiččo, ahavoiči, ahavoičči, ahavoi, ahavoija, ahavoid}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>"обветрить, высушить ветром (пашню и пр.)"]
                    ];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseEntry_1lemma_2meanings_7bases()
    {
        $dialect_id=47;
        $num='';
        $line = 'ahis|tua {-sa / -ta, -si / -ti, -ta, -seta, -sett} v. – 1. теснить, загонять кого-л. в неудобную позицию, положение 2. сдавливать, спирать дыхание';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>11,
                     "lemmas"=>[0=>"{ahistua, ahissa, ahista, ahissi, ahisti, ahista, ahisseta, ahissett}"],
                     "num"=> "",
                     "meanings"=>
                        [1=>"теснить, загонять кого-л. в неудобную позицию, положение",
                         2=>"сдавливать, спирать дыхание"]
                    ];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testParseEntry_1lemma_1meaning_2bases()
    {
        $dialect_id=47;
        $num='pl';
        $line = 'aluššo|vat {-vi / -bi} s. pl. – нижнее нательное белье';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>5,
                     "lemmas"=>[0=>"{aluššovat, , , , aluššovi, aluššobi}"],
                     "num"=> "pl",
                     "meanings"=>
                        [1=>"нижнее нательное белье"]
                    ];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseEntry_1lemma_4meanings_5bases()
    {
        $dialect_id=47;
        $num='';
        $line = 'avauǀduo {-du, -du, -du, -vuta, -vutt} v. impers. – 1. открываться, раскрываться; распускаться 2. отмыкаться 3. освобождаться (ото льда и т.д.) 4. открываться, начинать функционировать';
        $result = DictParser::parseEntry($line, $dialect_id);
        
        $expected = ["pos_id"=>11,
                     "lemmas"=>[0=>"{avauduo, , avaudu, , avaudu, avaudu, avauvuta, avauvutt}"],
                     "num"=> "impers",
                     "meanings"=>
                        [1=>"открываться, раскрываться; распускаться",
                         2=>"отмыкаться",
                         3=>"освобождаться (ото льда и т.д.)",
                         4=>"открываться, начинать функционировать"]
                    ];
        $this->assertEquals( $expected, $result);        
    }
     
}
