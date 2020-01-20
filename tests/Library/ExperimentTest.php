<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Experiment;

// ./vendor/bin/phpunit tests/Library/ExperimentTest
class ExperimentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSearchPosGramsetByWord()
    {
        $lang_id = 4;
        $property = 'pos';
        $table_name = 'search_'.$property;
        $wordforms = DB::table($table_name)
                  ->whereLangId($lang_id)
                  ->take(10)
                  ->get();
        $expected = $result = [];
        foreach ($wordforms as $wordform) {
            $expected[] = Experiment::searchPosGramsetByWord($lang_id, $wordform->wordform, $property);
            $result[] = Experiment::searchPosGramsetByWordWithWList($lang_id, $wordform->wordform, $property);
        }
        $this->assertEquals($expected, $result);        
    }
}
