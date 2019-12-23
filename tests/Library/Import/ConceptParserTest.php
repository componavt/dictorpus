<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Import\ConceptParser;

// ./vendor/bin/phpunit tests/Library/Import/ConceptParserTest

class ConceptParserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testChooseDialectsForLemmas()
    {
        $place_lemmas = ['01'=>['a1'],
                         '02'=>['a1'],
                         '03'=>['a1'],
                         '04'=>['a1'],
                         '05'=>['a1'],
                         '06'=>['a2'],
                         '07'=>['a1'],
                         '08'=>['a3'],
                         '09'=>['a1'],
                           10=>['a1'],
                           11=>['a5'],
                           12=>['a4'],
                           13=>['a1'],
                           14=>['a1'],
                           15=>['a1'],
                           16=>['a2'],
                           17=>['a4'],
                           18=>['a2'],
                           19=>['a1'],
                           20=>['a5'],
                           21=>['a1'],
                           22=>['a1'],
                           23=>['a1'],
                           24=>['a1'],
                           25=>['a6'],
                           26=>['a6'],
                           27=>['a7'],
                           28=>['a7'],
                           29=>['a7'],
                           30=>['a7']];

        $result = ConceptParser::chooseDialectsForLemmas($place_lemmas);
//dd($result["a5"]);        
        $expected = ["a1"=>[4 => [11=>[145], 7=>[175], 10=>[232], 17=>[234], 18=>[235], 21=>[237], 25=>[169], 26=>[179], 29=>[239]],
                            5=> [30=>[240], 36=>[96]],
                            6=> [38=>[245], 39=>[246], 42=> [247], 41=>[248]]],
                     "a2"=>[4 => [16=>[140]], 
                            5=> [31=>[241], 33=>[243]]],
                     "a3"=>[4=>[19=>[236]]],
                     "a4"=>[4 => [28=>[238]], 
                            5=> [32=>[242]]],
                     "a5"=>[4 => [27=>[197]], 
                            5=> [37=>[244]]],
                     "a6"=>[1=>[1=>[53, 78]]],
                     "a7"=>[1=>[5=>[71, 26], 4=>[5], 3=>[38]]]];
        $this->assertEquals( $expected, $result);        
    }
}
