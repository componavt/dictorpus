<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\KarVerbOlo;

// php artisan make:test Library\Grammatic\KarVerbOloTest
// ./vendor/bin/phpunit tests/Library/Grammatic\KarVerbOloTest

class KarVerbOloTest extends TestCase
{
    public function testPartic2active() {
        $lang_id = 4;
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
            $result[$i] = KarVerbOlo::partic2active($stems1[$i], $stems8[$i]);
        }
        $expected = ['peittynyh', 'pengonuh', 'pidänyh', 'polgenuh', 'eččinyh', 
            'pabaitannuh', 'palanuh', 'puinnuh', 'juonnuh', 'suannuh', 
            'paganoinnuh', 'painelluh', 'pannuh', 'pierryh', 'pessyh', 
            'paikannuh', 'puhkennuh', 'pahennuh', 'pakinnuh', 'vedänyh', 
            'kuadanuh', 'jaganuh', 'jiännyh', 'nähnyh'];
        $this->assertEquals( $expected, $result);        
    }
}
