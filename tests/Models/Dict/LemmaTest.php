<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Dict\Lemma;
use App\Models\Dict\Wordform;

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
    
    public function testGetWordformsCONLLVepAnimateNoun()
    {
        $word = "Vellenke";
        $lemma_obj = Lemma::find(24); 
        $result = $lemma_obj->getWordformsCONLL($word);
        
        $expected = [["Number=Sing","Case=Com"]];
        $this->assertEquals( $expected, $result);        
    }

    public function testGetGramsetsByWordVepAnimateNoun()
    {
        $word = "Vellenke";
        $lemma_obj = Lemma::find(24); 
        $result = $lemma_obj->getGramsetsByWord($word);
        
        $expected = [14];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testGetBaseVocalPl()
    {
        $lemma_id = 26420;
        $lemma = Lemma::find($lemma_id);

        $base_n = 1;
        $dialect_id = 43;
        $result = $lemma->getBase($base_n, $dialect_id);
        
        $expected = null;
        $this->assertEquals( $expected, $result);        
    }
    
    public function testGetStemAffix()
    {
        $lemma_id = 29586;
        $lemma = Lemma::find($lemma_id);

        $result = $lemma->getStemAffix();
        
        $expected = ['Anu','s'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testDialectIds()
    {
        $lemma_id = 17;
        $lemma = Lemma::find($lemma_id);
        $result = $lemma->dialectIds();
        
        $expected = [1,3,4,5];
        $this->assertEquals( $expected, $result);        
    }
    
/* 
    public function testWordformTerminativ()
    {
        $lemma_id = 21531;
        $gramset_id = 16;
        $dialect_id=43;
        
        $lemma = Lemma::find($lemma_id);
        $result = $lemma->wordform($gramset_id,$dialect_id);
        
        $expected = 'Amerikahasai, Amerikalesai';
        $this->assertEquals( $expected, $result);        
    }
*/    
}
