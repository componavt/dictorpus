<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\VepsName;

// php artisan make:test Library/Grammatic/VepsNameTest
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
        $stems = ['meca','kaivo','vilu','verko','kego','agja','perti', 'veikoi', 'soboi']; //,'sene','lume', 'maido', 'une', 'lauda'
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsName::illSgBase($stem);
        }
//dd($result);        
        $expected = ['mec','kaivo','vil','verko','kego','agja','pert', 'veikoi', 'soboi']; //,'sen','lum', 'maid','un', 'laud'
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
        $stems = ['meca','kaivo','vilu','verko','kego','agja','perti', 'veikoi', 'soboi'];//,'sene', 'maido', 'une', 'lauda','lume'
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsName::illSg($stem);
        }
//dd($result);        
        $expected = ['mecaha, mecha','kaivoho','viluhu, vilhu','verkoho','kegoho','agjaha','pertihe, perthe', 'veikoihe', 'soboihe'];//,'senhe', 'maidho','unhe', 'laudha','lumhe'
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

    public function testStemsFromTemplateCompound() {
        $template = "abuozuteseli|ne (-žen, -št, -žid)";
        $num = NULL;
        $result = VepsName::stemsFromTemplate($template, $num);
//dd($result);        
        $expected = [[0=>'abuozuteseline', 
                      1=>'abuozuteseliže', 
                      2=>'abuozuteseliže', 
                      3=>'abuozuteselišt', 
                      4=>'abuozuteseliži', 
                      5=>''], $num, 'abuozuteseli', 'ne'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformByStemsAccusativ() {
        $stems = [0=>'abuozuteseline', 
                  1=>'abuozuteseliže', 
                  2=>'abuozuteseliže', 
                  3=>'abuozuteselišt', 
                  4=>'abuozuteseliži', 
                  5=>''];
        $gramset_id = 56;
        $dialect_id=43;
        $num = '';
        $result = VepsName::wordformByStems($stems, $gramset_id, $dialect_id, $num);
        $expected = 'abuozuteseline, abuozuteseližen';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformByStemsAccusativPl() {
        $stems = [0=>'Alamad', 
                  1=>'', 
                  2=>'', 
                  3=>'', 
                  4=>'Alamai', 
                  5=>''];
        $gramset_id = 56;
        $dialect_id=43;
        $num = 'pl';
        $result = VepsName::wordformByStems($stems, $gramset_id, $dialect_id, $num);
        $expected = '';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformByStemsAccusativSg() {
        $stems = [0=>'Amerik', 
                  1=>'Amerika', 
                  2=>'Amerika', 
                  3=>'Amerika', 
                  4=>'', 
                  5=>''];
        $gramset_id = 56;
        $dialect_id=43;
        $num = 'sg';
        $result = VepsName::wordformByStems($stems, $gramset_id, $dialect_id, $num);
        $expected = 'Amerik, Amerikan';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformByStemsComitativ() {
        $stems = [0=>'abuozuteseline', 
                  1=>'abuozuteseliže', 
                  2=>'abuozuteseliže', 
                  3=>'abuozuteselišt', 
                  4=>'abuozuteseliži', 
                  5=>''];
        $gramset_id = 14;
        $dialect_id=43;
        $num = '';
        $result = VepsName::wordformByStems($stems, $gramset_id, $dialect_id, $num);
        $expected = 'abuozuteseliženke';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformByStemsComitativPl() {
        $stems = [0=>'Alamad', 
                  1=>'', 
                  2=>'', 
                  3=>'', 
                  4=>'Alamai', 
                  5=>''];
        $gramset_id = 14;
        $dialect_id=43;
        $num = 'pl';
        $result = VepsName::wordformByStems($stems, $gramset_id, $dialect_id, $num);
        $expected = '';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformByStemsComitativSg() {
        $stems = [0=>'Amerik', 
                  1=>'Amerika', 
                  2=>'Amerika', 
                  3=>'Amerika', 
                  4=>'', 
                  5=>''];
        $gramset_id = 14;
        $dialect_id=43;
        $num = 'sg';
        $result = VepsName::wordformByStems($stems, $gramset_id, $dialect_id, $num);
        $expected = 'Amerikanke';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformByStemsComitativSouth() {
        $stems = [0=>'abuozuteseline', 
                  1=>'abuozuteseliže', 
                  2=>'abuozuteseliže', 
                  3=>'abuozuteselišt', 
                  4=>'abuozuteseliži', 
                  5=>''];
        $gramset_id = 14;
        $dialect_id=3;
        $num = '';
        $result = VepsName::wordformByStems($stems, $gramset_id, $dialect_id, $num);
        $expected = 'abuozuteseližedmu, abuozuteseližemu';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformByStemsProlativ() {
        $stems = [0=>'abuozuteseline', 
                  1=>'abuozuteseliže', 
                  2=>'abuozuteseliže', 
                  3=>'abuozuteselišt', 
                  4=>'abuozuteseliži', 
                  5=>''];
        $gramset_id = 15;
        $dialect_id=43;
        $num = '';
        $result = VepsName::wordformByStems($stems, $gramset_id, $dialect_id, $num);
        $expected = 'abuozuteselištme';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformByStemsProlativSouth() {
        $stems = [0=>'abuozuteseline', 
                  1=>'abuozuteseliže', 
                  2=>'abuozuteseliže', 
                  3=>'abuozuteselišt', 
                  4=>'abuozuteseliži', 
                  5=>''];
        $gramset_id = 15;
        $dialect_id=3;
        $num = '';
        $result = VepsName::wordformByStems($stems, $gramset_id, $dialect_id, $num);
        $expected = 'abuozuteselištme, abuozuteselišmu';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformByStemsProlativPl() {
        $stems = [0=>'Alamad', 
                  1=>'', 
                  2=>'', 
                  3=>'', 
                  4=>'Alamai', 
                  5=>''];
        $gramset_id = 15;
        $dialect_id=43;
        $num = 'pl';
        $result = VepsName::wordformByStems($stems, $gramset_id, $dialect_id, $num);
        $expected = '';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformByStemsProlativSg() {
        $stems = [0=>'Amerik', 
                  1=>'Amerika', 
                  2=>'Amerika', 
                  3=>'Amerikad', 
                  4=>'', 
                  5=>''];
        $gramset_id = 15;
        $dialect_id=43;
        $num = 'sg';
        $result = VepsName::wordformByStems($stems, $gramset_id, $dialect_id, $num);
        $expected = 'Amerikadme';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testElatSgNewWritten() {
        $stem1 = 'aida';
        $dialect_id=43;
        $result = VepsName::elatSg($stem1, $dialect_id);
        $expected = 'aidaspäi';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testElatSgSouth() {
        $stem1 = 'aeda';
        $dialect_id=3;
        $result = VepsName::elatSg($stem1, $dialect_id);
        $expected = 'aedaspää';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testElatSgEast() {
        $stem1 = 'eida';
        $dialect_id=4;
        $result = VepsName::elatSg($stem1, $dialect_id);
        $expected = 'eidaspei';
        $this->assertEquals( $expected, $result);        
    }
    
    
    public function testAdesSgNewWritten() {
        $stem1 = 'aida';
        $dialect_id=43;
        $result = VepsName::adesSg($stem1, $dialect_id);
        $expected = 'aidal';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testAdesSgSouth() {
        $stem1 = 'aeda';
        $dialect_id=3;
        $result = VepsName::adesSg($stem1, $dialect_id);
        $expected = 'aedaa';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testAdesSgEast() {
        $stem1 = 'eida';
        $dialect_id=4;
        $result = VepsName::adesSg($stem1, $dialect_id);
        $expected = 'eidata';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testAdesSgWest() {
        $stem1 = 'aida';
        $dialect_id=5;
        $result = VepsName::adesSg($stem1, $dialect_id);
        $expected = 'aidau';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testAblatSgNewWritten() {
        $stem1 = 'aida';
        $dialect_id=43;
        $result = VepsName::ablatSg($stem1, $dialect_id);
        $expected = 'aidalpäi';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testAblatSgSouth() {
        $stem1 = 'aeda';
        $dialect_id=3;
        $result = VepsName::ablatSg($stem1, $dialect_id);
        $expected = 'aedaapää';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testAblatSgEast() {
        $stem1 = 'eida';
        $dialect_id=4;
        $result = VepsName::ablatSg($stem1, $dialect_id);
        $expected = 'eiduu';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testAblatSgWest() {
        $stem1 = 'aida';
        $dialect_id=5;
        $result = VepsName::ablatSg($stem1, $dialect_id);
        $expected = 'aidaupäi';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testAllatSgNewWritten() {
        $stem1 = 'aida';
        $dialect_id=43;
        $result = VepsName::allatSg($stem1, $dialect_id);
        $expected = 'aidale';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testAllatSgEast() {
        $stem1 = 'eida';
        $dialect_id=4;
        $result = VepsName::allatSg($stem1, $dialect_id);
        $expected = 'eiduupei';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testApproxSgNewWritten() {
        $stem1 = 'aida';
        $dialect_id=43;
        $result = VepsName::approxSg($stem1, $dialect_id);
        $expected = 'aidanno, aidannoks';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testApproxSgEast() {
        $stem1 = 'eida';
        $dialect_id=4;
        $result = VepsName::approxSg($stem1, $dialect_id);
        $expected = 'eidannoks';
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
