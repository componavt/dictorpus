<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Corpus\Word;
// ./vendor/bin/phpunit tests/Models/Dict/WordTest.php

class WordTest extends TestCase
{
    public function testProjectLangIDs()
    {
        $word = 'kuiva';
        $lang_id=4;
        $result = (array)Word::getMeaningsByWord($word, $lang_id)->pluck('id');
dd($result);        
/*        $result = [];
        foreach ($meanings as $meaning) {
            $result[]=$mre
        }*/
        $expected = null;
        $this->assertEquals( $expected, $result);        
    }
}
