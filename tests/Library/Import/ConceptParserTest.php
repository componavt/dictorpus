<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Import\ConceptParser;

// ./vendor/bin/phpunit tests/Library/Import/DictParserTest

class ConceptParserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testChooseDialectsForLemmas()
    {
        $place_lemmas = [];
        $result = ConceptParser::chooseDialectsForLemmas($place_lemmas);
        $expected = ["a1"=>[4 => [11=>[145], N=>[233], 7=>[175], 10=>[232], 17=>[234], 18=>235, 21=>[237], 25=>[169], 26=>[179], 29=>[239]],
                            5=> [30=>[240], 36=>[96]],
                            6=> [38=>[245], 39=>[246], 42=> [247], 41=>[248]]],
                     "a2"=>[4 => [16=>[140]], 
                            5=> [31=>[241], 33=>[243]]],
                     "a3"=>[4=>[19=>[236]]],
                     "a4"=>[4 => [28=>[238]], 
                            5=> [32=>[242]]],
                     "a5"=>[4 => [4=>[197]], 
                            5=> [37=>[244]]],
                     "a6"=>[1=>[1=>[53, 78]]],
                     "a7"=>[1=>[5=>[71, 26], 4=>[5], 3=>[38]]]];
    }
}
