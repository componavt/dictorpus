<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic;
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
        $result = VepsVerb::getStemPoten($inf_stem, $pres_stem, $past_actv_ptcp_stem);
       
        $expected = 'voik';
        $this->assertEquals( $expected, $result);        
    }

    public function testGetStemPotenAstta() {
        $inf_stem = 'ast';
        $pres_stem = 'astu';
        $past_actv_ptcp_stem = 'ast';
        $result = VepsVerb::getStemPoten($inf_stem, $pres_stem, $past_actv_ptcp_stem);
       
        $expected = 'ast';
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
    
    public function testWordformByStems_IndImperf3PlSouth() {
        $stems = ['pes', 'peze', 'pezi', 'pez', 'pez', 'pez', 't', 'a'];
        $dialect_id = 3; // южновепсский
        $gramset_id = 85; // 24. индикатив, имперфект, 3 л., мн. ч., -
        $result = VepsVerb::wordformByStems($stems, $gramset_id, $dialect_id);
       
        $expected = 'ebad pezen, ebad pezend';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformByStems_IndPres3SgNegWest() {
        $stems = ['sa', 'sa', 'sai', 'sa', 'sa', 'sa', 't', 'a'];
        $dialect_id = 5; // средневепсский западный
        $gramset_id = 72; // 9. индикатив, презенс, 3 л., ед. ч., -
        $result = VepsVerb::wordformByStems($stems, $gramset_id, $dialect_id);
       
        $expected = 'ei sa, ii sa';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformByStems_IndImperf3SgWest() {
        $stems = ['sa', 'sa', 'sai', 'sa', 'sa', 'sa', 't', 'a'];
        $dialect_id = 5; // средневепсский западный
        $gramset_id = 34; // 15. индикатив, имперфект, 3 л., ед. ч., +
        $result = VepsVerb::wordformByStems($stems, $gramset_id, $dialect_id);
       
        $expected = 'sai';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformByStems_IndPerf1PlWest() {
        $stems = ['sa', 'sa', 'sai', 'sa', 'sa', 'sa', 't', 'a'];
        $dialect_id = 5; // средневепсский западный
        $gramset_id = 95; // 34. индикатив, перфект, 1 л., мн. ч., -
        $result = VepsVerb::wordformByStems($stems, $gramset_id, $dialect_id);
       
        $expected = 'emai ole sanuded, emai uugoi sanuded, emei ole sanuded, emei uugoi sanuded';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testImper_1Base() {
        $stem0 = 'anasta';
        $dt = 'd';
        $stem8 = '';
        $dialect_id = 43;
        $gramset_id = 52; // императив, 3 л., ед. ч., +
        $result = VepsVerb::imper3($stem0, $dt, $stem8, $gramset_id, $dialect_id);
       
        $expected = 'anastagha';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testImper3()
    {
        $stem0 = 'aht';
        $dt = 't';
        $stem8 = 'ahtkaha, laske ahtb, okha ahtb';
        $gramset_id = 55;
        $dialect_id = 5;
        $result = VepsVerb::imper3($stem0, $dt, $stem8, $gramset_id, $dialect_id);
       
        $expected = 'ahtkaha, laske ahtba, okha ahtba';
        $this->assertEquals( $expected, $result);        
    }

    
    public function testWordformByStemsAbutada() {
        $lang_id = 1;
        $pos_id = 11;
        $name_num = '';
        $gramsets = [89, 96, 103, 108];
        $dialect_id=1;
        $is_reflexive = false;
        $template = 'abut|ada (-ab, -i, -agha)';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id, $is_reflexive);
        $result = [];
        foreach ($gramsets as $gramset_id) {
            $result[] = VepsVerb::wordformByStems($stems, $gramset_id, $dialect_id);
        }
        $expected = ['olem abutadud', 
                     'ed olgii abutadud',
                     'ol’d’he abutadud',
                     'em oldud abutadud'];
        $this->assertEquals( $expected, $result);        
    }
}
