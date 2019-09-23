<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\VepsVerb;

// php artisan make:test Library/Grammatic/VepsVerbTest
// ./vendor/bin/phpunit tests/Library/Grammatic/VepsVerbTest.php

class VepsVerbTest extends TestCase
{
    /**
     * base of past actvive participle
     */
    public function testGetStemPAPVoikta()
    {
        $inf_stem = 'voik';
        $pres_stem = 'voika';
        $result = VepsVerb::getStemPAP($inf_stem, $pres_stem);
       
        $expected = 'voik';
        $this->assertEquals( $expected, $result);        
    }

    public function testGetStemCondVoikta()
    {
        $pres_stem = 'voika';
        $result = VepsVerb::getStemCond($pres_stem);
       
        $expected = 'voika';
        $this->assertEquals( $expected, $result);        
    }

    public function testGetStemPotenVoikta()
    {
        $inf_stem = 'voik';
        $pres_stem = 'voika';
        $past_actv_ptcp_stem = 'voik';
        $result = VepsVerb::getStemPoten($past_actv_ptcp_stem, $inf_stem, $pres_stem);
       
        $expected = 'voik';
        $this->assertEquals( $expected, $result);        
    }

    public function testGetStemPotenAstta() {
        $inf_stem = 'ast';
        $pres_stem = 'astu';
        $past_actv_ptcp_stem = 'ast';
        $result = VepsVerb::getStemPoten($past_actv_ptcp_stem, $inf_stem, $pres_stem);
       
        $expected = 'ast';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testIndPres2Sg() {
        $stem1 = 'suli';
        $dialect_id = 3; // южновепсский
        $result = VepsVerb::IndPres2Sg($stem1, $dialect_id);
       
        $expected = 'sulid’';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testIndPres3Sg() {
        $stem1 = 'peze';
        $dialect_id = 1; // северновепсский
        $result = VepsVerb::IndPres3Sg($stem1, $dialect_id);
       
        $expected = 'pezzob';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformByStems_IndImperf3SgNegSouth() {
        $stems = ['pes', 'peze', 'pezi', 'pez', 'pez', 'pez', 't', 'a'];
        $dialect_id = 3; // южновепсский
        $gramset_id = 82; // 21. индикатив, имперфект, 3 л., ед. ч., -
        $result = VepsVerb::wordformByStems($stems, $gramset_id, $dialect_id);
       
        $expected = 'ii pezen, ii pezend';
        $this->assertEquals($expected, $result);        
    }
    
    public function testWordformByStemsPresImperf3PlSouth() {
        $stems = ['pes', 'peze', 'pezi', 'pez', 'pez', 'pez', 't', 'a'];
        $dialect_id = 3; // южновепсский
        $gramset_id = 85; // 24. индикатив, имперфект, 3 л., мн. ч., -
        $result = VepsVerb::wordformByStems($stems, $gramset_id, $dialect_id);
       
        $expected = 'ebad pezen, ebad pezend';
        $this->assertEquals( $expected, $result);        
    }
}
