<?php

namespace Tests;

use Tests\IsolatedSqliteTestCase as TestCase;

use App\Models\Corpus\Word;
// ./vendor/bin/phpunit tests/Models/Corpus/WordTest.php

class WordTest extends TestCase
{
    public function testSplitWordBySpecialSymbol()
    {
        $token   = "gor’o-¦gor’kija";
        $word_count = 932;

        $result = Word::splitWord($token, $word_count);

        $expected  = ["gor’o</w>-¦<w id=\"932\">gor’kija", [931 => 'gor’o', 932 => 'gor’kija']];
        $this->assertEquals($expected, $result);
    }
}
