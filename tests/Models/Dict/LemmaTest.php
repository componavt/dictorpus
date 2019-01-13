<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Dict\Lemma;

// php artisan make:test Models\Dict\LemmaTest
// ./vendor/bin/phpunit tests/Models/Dict/LemmaTest

class LemmaTest extends TestCase
{
    
    public function testExtractStemVepsVerbManyWordforms()
    {
        $lemma_id = 828;
        $lemma = Lemma::find($lemma_id);
        $result = $lemma->extractStem();
        
        $expected = ['pe','sta'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testExtractStemPropKarVerbManyWordforms()
    {
        $lemma_id = 2984;
        $lemma = Lemma::find($lemma_id);
        
        $result = $lemma->extractStem();
        
        $expected = ['aš','tuo'];
        $this->assertEquals( $expected, $result);        
    }
    
    // чередование в диалектах
    public function testExtractStemVepsNounManyWordforms()
    {
        $lemma_id = 744;
        $lemma = Lemma::find($lemma_id);
        $result = $lemma->extractStem();
        
        $expected = ['','aid'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseLemmaField()
    {
        $lemma_field="abei|";
        $result = Lemma::parseLemmaField($lemma_field);
        
        $expected = ['abei','','abei',''];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testToCONLLVepAnimateNoun()
    {
        $word = "vel'l";
        $lemma_obj = Lemma::find(24); 
        $result = $lemma_obj->getWordformsCONLL($word);
        
        $expected = ["Number=Sing"];
        $this->assertEquals( $expected, $result);        
    }
}
