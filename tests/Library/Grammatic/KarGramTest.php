<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\KarGram;

// php artisan make:test Library\Grammatic\KarGram
// ./vendor/bin/phpunit tests/Library/Grammatic\KarGram

class KarGramTest extends TestCase
{
    public function testGarmVowelBack1Let() {
        $stem = 'anda';
        $vowel = 'a';
        $result = KarGram::garmVowel($stem, $vowel);
        
        $expected = 'a';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testGarmVowelFront2Let() {
        $stem = 'mäne';
        $vowel = 'ou';
        $result = KarGram::garmVowel($stem, $vowel);
        
        $expected = 'öy';
        $this->assertEquals( $expected, $result);        
    }
    
     public function testToRightTemplate_3bases()
    {
        $template = "ič|e {-čie, -čiedä, -čei}";
        $num = null;
        $pos_id = 5; // noun
        $result = KarGram::toRightTemplate($template, $num, $pos_id);
       
        $expected = "{iče, iččie, iččie, iččiedä, iččei, iččei}";
        $this->assertEquals($expected, $result);        
    }
           
     public function testToRightTemplateSg()
    {
        $template = "Kariel|a {-a, -ua}";
        $num = 'sg';
        $pos_id = 5; // noun
        $result = KarGram::toRightTemplate($template, $num, $pos_id);
       
        $expected = "{Kariela, Kariela, Kariela, Karielua, , }";
        $this->assertEquals($expected, $result);        
    }
              
    public function testStemsFromFullList()
    {
        $template = "{Kariela, Kariela, Kariela, Karielua, , }";
        $lang_id = 4;
        $pos_id = 5; // noun
        $result = KarGram::stemsFromFullList($template);
       
        $expected = [0=>'Kariela', 
                     1=>'Kariela', 
                     2=>'Kariela', 
                     3=>'Karielua', 
                     4=>'', 
                     5=>''];
        $this->assertEquals($expected, $result);        
    }
    
    public function testStemsFromTemplateNameSg() {
        $template = "Kariel|a {-a, -ua}";
        $pos_id = 5; // noun
        $num = 'sg';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num);
//dd($result);        
        $expected = [[0=>'Kariela', 
                      1=>'Kariela', 
                      2=>'Kariela', 
                      3=>'Karielua', 
                      4=>'', 
                      5=>''], $num, 'Kariel', 'a'];
        $this->assertEquals( $expected, $result);        
    }
}
