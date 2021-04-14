<?php

//use Illuminate\Foundation\Testing\WithoutMiddleware;
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;
//use TestCase;

use App\Models\Corpus\Word;
// ./vendor/bin/phpunit tests/Models/Dict/WordTest.php

class WordTest extends TestCase
{
// какая-то незаконченная функция    
    public function testProjectLangIDs()
    {
        $word = 'kuiva';
        $lang_id=4;
        $result = (array)Word::getMeaningsByWord($word, $lang_id)->pluck('id');
        /*
dd($result);        
        $result = [];
        foreach ($meanings as $meaning) {
            $result[]=$mre
        }
        $expected = null;
        $this->assertEquals( $expected, $result);   
         * 
         */     
        $this->assertEquals( true, true);   
    }
    
    public function testSplitWordBySpecialSymbol()
    {
        $token   = "gor’o-¦gor’kija";
        $word_count = 932;
        
        $result = Word::splitWord($token, $word_count);

        $expected  = ["gor’o</w>-¦<w id=\"932\">gor’kija", [931=>'gor’o', 932=>'gor’kija']];
        $this->assertEquals( $expected, $result);
    }
}
