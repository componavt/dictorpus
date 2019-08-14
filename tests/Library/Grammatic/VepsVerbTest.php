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

    public function testGetStemPotenAstta()
    {
        $inf_stem = 'ast';
        $pres_stem = 'astu';
        $past_actv_ptcp_stem = 'ast';
        $result = VepsVerb::getStemPoten($past_actv_ptcp_stem, $inf_stem, $pres_stem);
       
        $expected = 'ast';
        $this->assertEquals( $expected, $result);        
    }
}
