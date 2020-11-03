<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\KarGram;
use App\Library\Grammatic\KarVerb;

// php artisan make:test Library\Grammatic\KarVerbTest
// ./vendor/bin/phpunit tests/Library/Grammatic\KarVerbTest

class KarVerbTest extends TestCase
{
    // основа 5 заканчивается на согласный g + гласный: gV    
    public function testPerfectVerbFormBringua() {
        $lang_id = 4;
        $stem = 'bringa';
        $result = KarVerb::perfectForm($stem, KarGram::isBackVowels($stem), $lang_id);
        
        $expected = 'bringan';
        $this->assertEquals( $expected, $result);        
    }
    
    // основа 5 заканчивается на l
    public function testPerfectVerbFormTulla() {
        $lang_id = 4;
        $stem = 'tul';
        $result = KarVerb::perfectForm($stem, KarGram::isBackVowels($stem), $lang_id);
        
        $expected = 'tullun';
        $this->assertEquals( $expected, $result);        
    }
    
    // основа 5 заканчивается на согласный + гласный: СV
    public function testPerfectVerbFormAndua() {
        $lang_id = 4;
        $stem = 'anda';
        $result = KarVerb::perfectForm($stem, KarGram::isBackVowels($stem), $lang_id);
        
        $expected = 'andan';
        $this->assertEquals( $expected, $result);        
    }
    
    // in base 5 the last letter = ’
    public function testPerfectVerbFormWithLastApost() {
        $lang_id = 4;
        $stem = 'avual’';
        $result = KarVerb::perfectForm($stem, KarGram::isBackVowels($stem), $lang_id);
        
        $expected = 'avual’lun';
        $this->assertEquals( $expected, $result);        
    }
    
    // northern proper
    public function testPerfectVerbFormWithBeforeLastItke() {
        $lang_id = 4;
        $stems = ['itke','anta','juo','kapaloi','tul','ruvet','tarit'];
        $result = [];
        foreach ($stems as $stem) {
            $result[] = KarVerb::perfectForm($stem, KarGram::isBackVowels($stem), $lang_id);
        }
        $expected = ['itken','antan','juonun','kapaloinun','tullun','ruvennun','tarinnun'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testAuxFormIndPerf2SingPol() {
        $gramset_id = 87; // индикатив, перфект, 2 л., ед. ч., пол
        $dialect_id=47;
        $result = KarVerb::auxVerb($gramset_id, $dialect_id);
        
        $expected = 'olet';
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
        $gramset_id = 103; // 42. индикатив, плюсквамперфект, 3 л., мн. ч., пол.
        $dialect_id=47;
        $result = KarVerb::auxVerb($gramset_id, $dialect_id);
        
        $expected = 'oli';
        $this->assertEquals( $expected, $result);        
    }
   
    public function testAuxFormCondPlur3SingPol() {
        $gramset_id = 136; // 97. кондиционал, плюсквамперфект, 3 л., ед. ч., пол.
        $dialect_id=47;
        $result = KarVerb::auxVerb($gramset_id, $dialect_id);
        
        $expected = 'olis’';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testimp3SingPolByStem() {
        $stem5 = 'lien';
        $lemma = 'lietä';
        $dialect_id=47;
        $result = KarVerb::imp3PolByStem($stem5, $lemma, KarGram::isBackVowels($lemma), $dialect_id);
        
        $expected = 'liekkäh';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testimp2PlurPolByStem() {
        $stem5 = 'lien';
        $lemma = 'lietä'; 
        $dialect_id=47;
        $result = KarVerb::imp2PlurPolByStem($stem5, $lemma, KarGram::isBackVowels($lemma), $dialect_id);
        
        $expected = 'liekkiä';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testImpBaseVarata() {
        $stem5 = 'varat';
        //$dialect_id=44;
        $result = KarVerb::impBase($stem5);
        
        $expected = 'varakk';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testImpBaseRuveta() {
        $stem5 = 'varat';
        //$dialect_id=44;
        $result = KarVerb::impBase($stem5);
        
        $expected = 'varakk';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStem2FromMiniTemplateAkkiloija() {
        $lang_id = 4;
        $stem0 = 'akkiloija';
        $stem1 = 'akkiloičе';
        $result = KarVerb::stem2FromMiniTemplate($stem0, $stem1);
        
        $expected = 'akkiloičo';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStem3FromMiniTemplate() {
        $lang_id = 4;
        $stem0 = 'akkiloija';
        $stem1 = 'akkiloiče';
        $stem4 = 'akkiloiče';
        $result = KarVerb::stem3FromMiniTemplate($stem0, $stem1, $stem4);
        
        $expected = 'akkiloiči';
        $this->assertEquals( $expected, $result);        
    }
}
