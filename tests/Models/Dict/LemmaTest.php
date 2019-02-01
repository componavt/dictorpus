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
        $result = Lemma::parseLemmaField($lemma_field);
        
        $expected = ['abei','','abei',''];
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
 
    /*
     * ------------------------------------------------------wordformsByTemplate
     */
    public function testWordformsByTemplateIncorrectLang() {
        $lemma_id = 3058; //livvic lemma
        $lemma = Lemma::find($lemma_id); 
        $template = "ativo, ativo, ativo, ativu, ativo, ativo";
        $result = $lemma->wordformsByTemplate($template);
        
        $expected = false;
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformsByTemplateIncorrectPOS() {
        $lemma_id = 15796; // conjuction
        $lemma = Lemma::find($lemma_id); 
        $template = "ativo, ativo, ativo, ativu, ativo, ativo";
        $result = $lemma->wordformsByTemplate($template);
        
        $expected = false;
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformsByTemplateWithoutTemplate() {
        $lemma_id = 15252; //ativo
        $lemma = Lemma::find($lemma_id); 
        $template = "ativo, ativo, ativo, ativu, ativo, ativo";
        $result = $lemma->wordformsByTemplate($template);
        
        $expected = false;
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformsByTemplateIncorrectNumberOfStems() {
        $lemma_id = 15252; //ativo
        $lemma = Lemma::find($lemma_id); 
//        $template = "{ativo, ativo, ativo, ativu, ativo, ativo}";
        $template = "{ativo, ativo, ativo, ativu, ativo}";
        $result = $lemma->wordformsByTemplate($template);
        
        $expected = false;
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformsByTemplateAtivo() {
        $lemma_id = 15252; //ativo
        $lemma = Lemma::find($lemma_id); 
        $template = "{ativo, ativo, ativo, ativuo, ativo, ativo}";
        $result = $lemma->wordformsByTemplate($template);
        //$stems = ['ativo', 'ativo', 'ativo', 'ativu', 'ativo'];
        //$result = $lemma->wordformsByTemplate($stems);
        
        $dialect_id=46;
        $expected = $lemma->getWordformsForTest($dialect_id);
        $this->assertEquals( $expected, $result);        
    }
}
