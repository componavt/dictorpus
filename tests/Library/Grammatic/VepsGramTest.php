<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\VepsGram;

// php artisan make:test Library\Grammatic\VepsGramTest
// ./vendor/bin/phpunit tests/Library/Grammatic/VepsGramTest

class VepsGramTest extends TestCase
{
    public function testStemsFromTemplateNounPlDict() {
        $pos_id = 5; // noun
        $template = "Alama|d (-id)";
        $result = VepsGram::stemsFromTemplate($template, $pos_id, 'pl');
//dd($result);        
        $expected = [['Alamad', '', '', '', 'Alamai', ''], 'pl', 'Alama', 'd'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateMultiNum() {
        $pos_id = 5; // noun
        $template = "vep-decl-stems|adjektiv||an|ad|id";
        $result = VepsGram::stemsFromTemplate($template, $pos_id);
        
        $expected = [['adjektiv', 'adjektiva', 'adjektiva', 'adjektivad', 'adjektivi', ''], null, 'adjektiv', ''];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateSg() {
        $pos_id = 5; // noun
        $template = "vep-decl-stems|n=sg|Amerik||an|ad";
        $result = VepsGram::stemsFromTemplate($template, $pos_id);
        
        $expected = [['Amerik', 'Amerika', 'Amerika', 'Amerikad', '', ''], 'sg', 'Amerik', ''];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplatePl() {
        $pos_id = 5; // noun
        $template = "vep-decl-stems|n=pl|Alama|d|id";
        $result = VepsGram::stemsFromTemplate($template, $pos_id);
//dd($result);        
        $expected = [['Alamad', '', '', '', 'Alamai', ''], 'pl', 'Alama', 'd'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateNounDict2Suff() {
        $pos_id = 5; // noun
        $template = "abekirj (-an, -oid)";
        $result = VepsGram::stemsFromTemplate($template, $pos_id);
//dd($result);        
        $expected = [['abekirj', 'abekirja', 'abekirja', 'abekirjad', 'abekirjoi', ''], null, 'abekirj', ''];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateNounDict3Suff() {
        $pos_id = 5; // noun
        $template = "abidkirje|ine (-žen, -št, -ižid)";
        $result = VepsGram::stemsFromTemplate($template, $pos_id);
//dd($result);        
        $expected = [['abidkirjeine', 'abidkirježe', 'abidkirježe', 'abidkirješt', 'abidkirjeiži', ''], null, 'abidkirje', 'ine'];
        $this->assertEquals( $expected, $result);        
    }
   
}
