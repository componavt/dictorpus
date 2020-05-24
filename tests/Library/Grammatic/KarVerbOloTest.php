<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\KarVerbOlo;

// php artisan make:test Library\Grammatic\KarVerbOloTest
// ./vendor/bin/phpunit tests/Library/Grammatic\KarVerbOloTest

class KarVerbOloTest extends TestCase
{
    public function testActiveBase() {
        $stems1 = ['peity', 'pengo', 'pie', 'polle', 'eči', 
            'pabaita', 'pala', 'pui', 'juo', 'sua', 
            'paganoiče', 'painele', 'pane', 'piere', 'peze', 
            'paikkua', 'puhkie/puhkene', 'pahene', 'pakiče', 'vie', 
            'kua', 'jua', 'jiä', ''];
        $stems8 = ['peitty', 'pengo', 'pidä', 'polge', 'ečči', 
            'pabaitta', 'pala', 'pui', 'juo', 'sua', 
            'paganoi', 'painel', 'pan', 'pier', 'pes', 
            'paikat', 'puhket', 'pahet', 'pakit', 'vedä', 
            'kuada', 'jaga', 'jiä', 'näh'];
        for ($i=0; $i<sizeof($stems8); $i++) {
            $result[$i] = KarVerbOlo::activeBase($stems1[$i], $stems8[$i]);
        }
        $expected = ['peittyn', 'pengon', 'pidän', 'polgen', 'eččin', 
            'pabaitann', 'palan', 'puinn', 'juonn', 'suann', 
            'paganoinn', 'painell', 'pann', 'pierr', 'pess', 
            'paikann', 'puhkenn', 'pahenn', 'pakinn', 'vedän', 
            'kuadan', 'jagan', 'jiänn', 'nähn'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testPartic2active() {
        $stems1 = ['peity', 'pengo', 'pie', 'polle', 'eči', 
            'pabaita', 'pala', 'pui', 'juo', 'sua', 
            'paganoiče', 'painele', 'pane', 'piere', 'peze', 
            'paikkua', 'puhkie/puhkene', 'pahene', 'pakiče', 'vie', 
            'kua', 'jua', 'jiä', ''];
        $stems8 = ['peitty', 'pengo', 'pidä', 'polge', 'ečči', 
            'pabaitta', 'pala', 'pui', 'juo', 'sua', 
            'paganoi', 'painel', 'pan', 'pier', 'pes', 
            'paikat', 'puhket', 'pahet', 'pakit', 'vedä', 
            'kuada', 'jaga', 'jiä', 'näh'];
        $harmony = [false, true, false, true, false, 
            true, true, true, true, true, 
            true, true, true, false, false, 
            true, true, true, true, false, 
            true, true, false, false];
        for ($i=0; $i<sizeof($stems8); $i++) {
            $result[$i] = KarVerbOlo::partic2active(null, $stems1[$i], $stems8[$i], $harmony[$i], null, null);
        }
        $expected = ['peittynyh', 'pengonuh', 'pidänyh', 'polgenuh', 'eččinyh', 
            'pabaitannuh', 'palanuh', 'puinnuh', 'juonnuh', 'suannuh', 
            'paganoinnuh', 'painelluh', 'pannuh', 'pierryh', 'pessyh', 
            'paikannuh', 'puhkennuh', 'pahennuh', 'pakinnuh', 'vedänyh', 
            'kuadanuh', 'jaganuh', 'jiännyh', 'nähnyh'];
        $this->assertEquals( $expected, $result);        
    }

    public function testPartic2activeRef() {
        $stems1 = ['pačkahtele', 'pačkahta', 'pahoittele', 'pakiče', 'palkua', 
                   'perra', 'peze', 'pie', 'potki', 'puaši', 'puno', 'pyrri'];
        $stems8 = ['pačkahtel', 'pačkahta', 'pahoitel', 'pakit', 'palkat', 
                   'perga', 'pes', 'pidä', 'potki', 'puašši', 'puno', 'pyrgi'];
        $harmony = [true, true, true, true, true, 
                   true, false, false, true, true, true, false];
        for ($i=0; $i<sizeof($stems8); $i++) {
            $result[$i] = KarVerbOlo::partic2active(null, $stems1[$i], $stems8[$i], $harmony[$i], null, true);
        }
        $expected = ['pačkahtelluhes', 'pačkahtannuhes', 'pahoitelluhes', 
            'pakinnuhes', 'palkannuhes', 'perganuhes', 'pessyhes', 'pidänyhes', 
            'potkinuhes', 'puaššinuhes', 'punonuhes', 'pyrginyhes'];
        $this->assertEquals( $expected, $result);        
    }
        
    public function testPartic2activeRefDef() {
        $stems0 = ['pakastuakseh'];
        for ($i=0; $i<sizeof($stems0); $i++) {
            $result[$i] = KarVerbOlo::partic2active($stems0[$i], null, null, true, true, true);
        }
        $expected = ['pakastannuhes'];
        $this->assertEquals( $expected, $result);        
    }
        
    public function testImpBaseSg() {
        $stems8 = ['peitty', 'pengo', 'pidä', 'polge', 'ečči', 
            'pabaitta', 'pala', 'pui', 'juo', 'sua', 
            'paganoi', 'painel', 'pan', 'pier', 'pes', 
            'paikat', 'puhket', 'pahet', 'pakit', 'vedä', 
            'kuada', 'jaga', 'jiä', 'näh'];
        for ($i=0; $i<sizeof($stems8); $i++) {
            $result[$i] = KarVerbOlo::impBaseSg($stems8[$i]);
        }
        $expected = ['peittykk', 'pengokk', 'pidäkk', 'polgekk', 'eččikk', 
            'pabaittakk', 'palakk', 'puig', 'juog', 'suag', 
            'paganoikk', 'painelk', 'pang', 'pierk', 'pesk', 
            'paikakk', 'puhkekk', 'pahekk', 'pakikk', 'vedäkk', 
            'kuadakk', 'jagakk', 'jiäg', 'nähk'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testCondImpBase() {
        $stems1 = ['peity', 'pengo', 'pie', 'polle', 'eči', 
            'pabaita', 'pala', 'pui', 'juo', 'sua', 
            'paganoiče', 'painele', 'pane', 'piere', 'peze', 
            'paikkua', 'puhkie/puhkene', 'pahene', 'pakiče', 'vie', 
            'kua', 'jua', 'jiä', ''];
        $stems8 = ['peitty', 'pengo', 'pidä', 'polge', 'ečči', 
            'pabaitta', 'pala', 'pui', 'juo', 'sua', 
            'paganoi', 'painel', 'pan', 'pier', 'pes', 
            'paikat', 'puhket', 'pahet', 'pakit', 'vedä', 
            'kuada', 'jaga', 'jiä', 'näh'];
        $harmony = [false, true, false, true, false, 
            true, true, true, true, true, 
            true, true, true, false, false, 
            true, true, true, true, false, 
            true, true, false, false];
        for ($i=0; $i<sizeof($stems8); $i++) {
            $result[$i] = KarVerbOlo::condImpBase(null, $stems1[$i], $stems8[$i], $harmony[$i], null, null);
        }
        $expected = ['peittyny', 'pengonu', 'pidäny', 'polgenu', 'eččiny', 
            'pabaitannu', 'palanu', 'puinnu', 'juonnu', 'suannu', 
            'paganoinnu', 'painellu', 'pannu', 'pierry', 'pessy', 
            'paikannu', 'puhkennu', 'pahennu', 'pakinnu', 'vedäny', 
            'kuadanu', 'jaganu', 'jiänny', 'nähny'];
        $this->assertEquals( $expected, $result);        
    }
    
}
