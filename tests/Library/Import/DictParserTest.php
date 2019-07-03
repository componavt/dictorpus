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
        $lemma_pos = 'aivoin adv';
        $result = DictParser::parseLemmaPart($lemma_pos);
        
        $expected = ['lemma'=>'aivoin','pos_id'=>2];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseLemmaPart_withComma()
    {
//        $lemma_pos = 'aivokkazeh, aivokkazin adv';
        $lemma_pos = 'aijalleh, aijaldi adv';
        $result = DictParser::parseLemmaPart($lemma_pos);
        
//        $expected = ['lemma'=>'aivokkazeh, aivokkazin','pos_id'=>2];
        $expected = ['lemma'=>'aijalleh, aijaldi','pos_id'=>2];
        $this->assertEquals( $expected, $result);        
    }
}
