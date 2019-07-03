<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\VepsName;

// php artisan make:test Library\Grammatic\VepsNameTest
// ./vendor/bin/phpunit tests/Library/Grammatic/VepsNameTest.php

class VepsNameTest extends TestCase
{
    public function testCountSyllable1() {
        $stems = ['su','pü','ma','pä','so','vö','ö','voi'];
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsName::countSyllable($stem);
        }
//dd($result);        
        $expected = [1,1,1,1,1,1,1,1];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testCountSyllable2() {
        $stems = ['meca','kaivo','lume','sene','vilu','verko','kego', 'maido','agja','perti', 'une', 'veikoi', 'soboi', 'lauda'];
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsName::countSyllable($stem);
        }
//dd($result);        
        $expected = [2,2,2,2,2,2,2,2,2,2,2,2,2,2];
        $this->assertEquals( $expected, $result);        
    }
    
    
    public function testCountSyllable3() {
        $stems = ['sizare','kirvhe','armha','nagrhe','abajo','abido'];
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsName::countSyllable($stem);
        }
//dd($result);        
        $expected = [3,3,3,3,3,3];
        $this->assertEquals( $expected, $result);        
    }
        
    public function testIllSgBase1() {
        $stems = ['su','pü','ma','pä','so','vö','ö','voi'];
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsName::illSgBase($stem);
        }
//dd($result);        
        $expected = ['su','pü','ma','pä','so','vö','ö','voi'];
        $this->assertEquals( $expected, $result);        
    }
        
    public function testIllSgBase2() {
        $stems = ['meca','kaivo','lume','sene','vilu','verko','kego', 'maido','agja','perti', 'une', 'veikoi', 'soboi', 'lauda'];
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsName::illSgBase($stem);
        }
//dd($result);        
        $expected = ['mec','kaivo','lum','sen','vil','verko','kego', 'maid','agja','pert','un', 'veikoi', 'soboi', 'laud'];
        $this->assertEquals( $expected, $result);        
    }
        
    public function testIllSgBase3() {
        $stems = ['sizare','kirvhe','armha','nagrhe','abajo','abido'];
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsName::illSgBase($stem);
        }
//dd($result);        
        $expected = ['sizare','kirvhe','armha','nagrhe','abajo','abido'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testIllSg1() {
        $stems = ['su','pü','ma','pä','so','vö','ö','voi'];
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsName::illSg($stem);
        }
//dd($result);        
        $expected = ['suhu','pühü','maha','pähä','soho','vöhö','öhö','voihe'];
        $this->assertEquals( $expected, $result);        
    }
        
    public function testIllSg2() {
        $stems = ['meca','kaivo','lume','sene','vilu','verko','kego', 'maido','agja','perti', 'une', 'veikoi', 'soboi', 'lauda'];
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsName::illSg($stem);
        }
//dd($result);        
        $expected = ['mecha','kaivoho','lumhe','senhe','vilhu','verkoho','kegoho', 'maidho','agjaha','perthe','unhe', 'veikoihe', 'soboihe', 'laudha'];
        $this->assertEquals( $expected, $result);        
    }
        
    public function testIllSg3() {
        $stems = ['sizare','kirvhe','armha','nagrhe','abajo','abido'];
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsName::illSg($stem);
        }
//dd($result);        
        $expected = ['sizarehe','kirvheze','armhaze','nagrheze','abajoho','abidoho'];
        $this->assertEquals( $expected, $result);        
    }
/*    
    public function testWordformsByTemplatePl() {
        $dialect_id=43;
        $template = "{{vep-decl-stems|n=pl|Alama|d|id}}";
        $result = VepsName::wordformsByTemplate($template, $dialect_id);
//dd($result);        
        $lemma_id = 21530; //Alamad 
        $lemma = Lemma::find($lemma_id); 

        $expected = $lemma->getWordformsForTest($dialect_id);
//dd($result);        
        $this->assertEquals( $expected, $result[1]);        
    }
    
    public function testWordformsByTemplate() {
        $dialect_id=43;
        $template = "{{vep-decl-stems|aba|i|jon|jod|joid}}";
        $result = VepsName::wordformsByTemplate($template, $dialect_id);
//dd($result);        
        $lemma_id = 21324; // abai 
        $lemma = Lemma::find($lemma_id); 

        $expected = $lemma->getWordformsForTest($dialect_id);
//dd($result);        
        $this->assertEquals( $expected, $result[1]);        
    }
    
    public function testWordformsByTemplateSg() {
        $dialect_id=43;
        $template = "{{vep-decl-stems|n=sg|Amerik||an|ad}}";
        $result = VepsName::wordformsByTemplate($template, $dialect_id);
//dd($result);        
        $lemma_id = 21531; //Amerik 
        $lemma = Lemma::find($lemma_id); 

        $expected = $lemma->getWordformsForTest($dialect_id);
//dd($result);        
        $this->assertEquals( $expected, $result[1]);        
    }
*/    
}
