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
    
    public function testStem1FromStem3() {
        $stems = ['pakasta', 'pakičče', 'puašši', 'pakkua'];
        
        foreach ($stems as $stem) {
            $result[] = KarVerbOlo::stem1FromStem3($stem);
        }
        
        $expected = ['pakasta', 'pakiče', 'puaši', 'pakua'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testActiveBaseWithApostroph() {
        $stem1 = 'kil’l’u';
        $stem8 = 'kil’l’u';
        
        $result = KarVerbOlo::activeBase($stem1,$stem8);
        
        $expected = 'kil’l’un';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformByStemsWithApostroph() {
        $dialect_id=44;
        $gramset_id=80; // 19. индикатив, имперфект, 1 л., ед.ч., отриц. 
        $stems = [0=>'kil’l’uo',
                  1=>'kil’l’u',
                  2=>'kil’l’uu',
                  3=>'kil’l’u',
                  4=>'kil’l’ui',
                  5=>'kil’l’ui',
                  6=>'kil’l’uta',
                  7=>'kil’l’utt',
                  8=>'kil’l’u',
                  10=>TRUE
                 ];
        
        $result = KarVerbOlo::wordformByStems($stems, $gramset_id, $dialect_id);
        
        $expected = 'en kil’l’unuh';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseImp3Pl() {
        // Примеры из файла rules_verb_reflex_Olo_v2.docx
        // п.о. 7 (сильноступенная пассивная основа, имперфект индикатива, 3л. мн.ч.)
        // Формат: [словоформа_имперфект_3л_мн_ч, ожидаемая_основа_7, является_возвратным]

        $testCases = [
            // Возвратные глаголы (с суффиксом -ihes)
            ['pačkahteltihes', 'pačkahtelt', true],   // пример 1
            ['pačkahtettihes', 'pačkahtett', true],   // пример 2
            ['pahoiteltihes', 'pahoitelt', true],     // пример 3
            ['pakittihes', 'pakitt', true],           // пример 5
            ['palkattihes', 'palkatt', true],         // пример 6
            ['perrettihes', 'perrett', true],         // пример 7
            ['pestihes', 'pest', true],               // пример 8
            ['piettihes', 'piett', true],             // пример 9
            ['potkittihes', 'potkitt', true],         // пример 10
            ['puašittihes', 'puašitt', true],         // пример 11
            ['punottihes', 'punott', true],           // пример 12
            ['pyrrittihes', 'pyrritt', true],         // пример 13

            // Невозвратные глаголы (с суффиксом -ih)
            ['pačkahteltih', 'pačkahtelt', false],
            ['pakittih', 'pakitt', false],
            ['palkattih', 'palkatt', false],
        ];

        foreach ($testCases as $case) {
            $wordform = $case[0];
            $expected = $case[1];
            $isReflexive = $case[2];

            $result = KarVerbOlo::parseImp3Pl($wordform, $isReflexive);

            $this->assertEquals(
                $expected, 
                $result, 
                "Failed for wordform: $wordform, reflexive: " . ($isReflexive ? 'true' : 'false')
            );
        }
    }    
}
