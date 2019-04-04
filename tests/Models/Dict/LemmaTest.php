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
        $data = ['lemma'=>$lemma_field, 'lang_id'=>null, 'pos_id'=>null, 'dialect_id'=>null];
        $result = Lemma::parseLemmaField($data);
        
        $expected = ['abei','','abei','', false];
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
 
}
