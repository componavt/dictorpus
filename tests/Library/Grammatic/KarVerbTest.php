<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\KarVerb;

// php artisan make:test Library\Grammatic\KarVerbTest
// ./vendor/bin/phpunit tests/Library/Grammatic\KarVerbTest

class KarVerbTest extends TestCase
{
    public function testPerfectVerbFormTulla() {
        $lang_id = 4;
        $stem = 'tul';
        $result = KarVerb::perfectForm($stem, $lang_id);
        
        $expected = 'tullun';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testPerfectVerbFormAndua() {
        $lang_id = 4;
        $stem = 'anda';
        $result = KarVerb::perfectForm($stem, $lang_id);
        
        $expected = 'andan';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testAuxFormIndPerf2SingPol() {
        $lang_id = 4;
        $gramset_id = 87; // индикатив, перфект, 2 л., ед. ч., пол
        $dialect_id=47;
        $result = KarVerb::auxForm($gramset_id, $lang_id, $dialect_id);
        
        $expected = 'olet ';
        $this->assertEquals( $expected, $result);        
    }
/*    
    public function testAuxFormIndPerf3PlurNeg() {
        $lang_id = 4;
        $gramset_id = 97; // 36. индикатив, перфект, 3 л., мн. ч., отриц
        $dialect_id=47;
        $result = KarVerb::auxForm($gramset_id, $lang_id, $dialect_id);
        
        $expected = 'ei ole ';
        $this->assertEquals( $expected, $result);        
    }
*/    
    public function testAuxFormIndPlurf3PlurNeg() {
        $lang_id = 4;
        $gramset_id = 103; // 42. индикатив, плюсквамперфект, 3 л., мн. ч., пол.
        $dialect_id=47;
        $result = KarVerb::auxForm($gramset_id, $lang_id, $dialect_id);
        
        $expected = 'oldih ';
        $this->assertEquals( $expected, $result);        
    }
   
    public function testAuxFormCondPlur3SingPol() {
        $lang_id = 4;
        $gramset_id = 136; // 97. кондиционал, плюсквамперфект, 3 л., ед. ч., пол.
        $dialect_id=47;
        $result = KarVerb::auxForm($gramset_id, $lang_id, $dialect_id);
        
        $expected = 'olis’ ';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testimp3SingPolByStem() {
        $stem5 = 'lien';
        $lemma = 'lietä';
        $dialect_id=47;
        $result = KarVerb::imp3SingPolByStem($stem5, $lemma, $dialect_id);
        
        $expected = 'liekkäh';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testimp2PlurPolByStem() {
        $stem5 = 'lien';
        $lemma = 'lietä'; 
        $dialect_id=47;
        $result = KarVerb::imp2PlurPolByStem($stem5, $lemma, $dialect_id);
        
        $expected = 'liekkiä';
        $this->assertEquals( $expected, $result);        
    }
    
}
