<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\VepsGram;

// php artisan make:test Library\Grammatic\VepsGramTest
// ./vendor/bin/phpunit tests/Library/Grammatic/VepsGramTest

class VepsGramTest extends TestCase
{
    public function testCountSyllable1() {
        $stems = ['su','pü','ma','pä','so','vö','ö','voi'];
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsGram::countSyllable($stem);
        }
//dd($result);        
        $expected = [1,1,1,1,1,1,1,1];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testCountSyllable2() {
        $stems = ['meca','kaivo','lume','sene','vilu','verko','kego', 'maido','agja','perti', 'une', 'veikoi', 'soboi', 'lauda','kirvhe','armha','nagrhe'];
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsGram::countSyllable($stem);
        }
//dd($result);        
        $expected = [2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testCountSyllable3() {
        $stems = ['sizare','abajo','abido'];
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsGram::countSyllable($stem);
        }
//dd($result);        
        $expected = [3,3,3];
        $this->assertEquals( $expected, $result);        
    }
        
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
        $template = "{{vep-decl-stems|adjektiv||an|ad|id}}";
        $result = VepsGram::stemsFromTemplate($template, $pos_id);
        
        $expected = [['adjektiv', 'adjektiva', 'adjektiva', 'adjektivad', 'adjektivi', ''], null, 'adjektiv', ''];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateSg() {
        $pos_id = 5; // noun
        $template = "{{vep-decl-stems|n=sg|Amerik||an|ad}}";
        $result = VepsGram::stemsFromTemplate($template, $pos_id);
        
        $expected = [['Amerik', 'Amerika', 'Amerika', 'Amerikad', '', ''], 'sg', 'Amerik', ''];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testStemsFromTemplatePl() {
        $pos_id = 5; // noun
        $template = "{{vep-decl-stems|n=pl|Alama|d|id}}";
        $result = VepsGram::stemsFromTemplate($template, $pos_id);
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
    
    public function testStemsFromTemplateVerb1Base() {
        $pos_id = 11; // verb
        $template = "anast|ada (-ab, -i)";
        $result = VepsGram::stemsFromTemplate($template, $pos_id);
//dd($result);        
        $expected = [['anasta', 'anasta', 'anasti', 'anasta', 'anasta', 'anasta', 'd', 'a', ''], null, 'anast', 'ada'];
        $this->assertEquals( $expected, $result);        
    }
}
