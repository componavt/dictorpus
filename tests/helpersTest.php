<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

// php artisan make:test helpersTest
// ./vendor/bin/phpunit tests/helpersTest

class helpersTest extends TestCase
{
    public function testConvertQuotes()
    {
        $str = '«»„”“”';
        $result = convert_quotes($str);
        
        $expected = '""""""';
        $this->assertEquals( $expected, $result );        
    }
    
    public function testHighlight2words()
    {
        $str = "\n".'<s class="trans_sentence" id="transtext_s12"><w id="80">Церковь-то</w> <w id="81">была</w> <w id="82">построена</w> <w id="83">три</w> <w id="84">тысячи</w> <w id="85">лет</w> <w id="86">назад</w>, <w id="87">так</w> <w id="88">уж</w> <w id="89">наверное</w> <w id="90">много</w> <w id="91">времени</w> <w id="92">прошло</w>.</s>';
        $substr = 'лет назад';
        $result = highlight($str, $substr);
        
        $expected = '<s class="trans_sentence" id="transtext_s12"><w id="80">Церковь-то</w> <w id="81">была</w> <w id="82">построена</w> <w id="83">три</w> <w id="84">тысячи</w> <w id="85"><span class="search-word">лет</span></w> <w id="86"><span class="search-word">назад</span></w>, <w id="87">так</w> <w id="88">уж</w> <w id="89">наверное</w> <w id="90">много</w> <w id="91">времени</w> <w id="92">прошло</w>.</s>';
        $this->assertEquals( $expected, $result );        
    }
    
    public function testHighlight1word()
    {
        $str = "\n".'<s class="trans_sentence" id="transtext_s12"><w id="80">Церковь-то</w> <w id="81">была</w> <w id="82">построена</w> <w id="83">три</w> <w id="84">тысячи</w> <w id="85">лет</w> <w id="86">назад</w>, <w id="87">так</w> <w id="88">уж</w> <w id="89">наверное</w> <w id="90">много</w> <w id="91">времени</w> <w id="92">прошло</w>.</s>';
        $substr = 'лет';
        $result = highlight($str, $substr);
        
        $expected = '<s class="trans_sentence" id="transtext_s12"><w id="80">Церковь-то</w> <w id="81">была</w> <w id="82">построена</w> <w id="83">три</w> <w id="84">тысячи</w> <w id="85"><span class="search-word">лет</span></w> <w id="86">назад</w>, <w id="87">так</w> <w id="88">уж</w> <w id="89">наверное</w> <w id="90">много</w> <w id="91">времени</w> <w id="92">прошло</w>.</s>';
        $this->assertEquals( $expected, $result );        
    }
    
    public function testHighlight3words()
    {
        $str = "\n".'<s class="trans_sentence" id="transtext_s12"><w id="80">Церковь-то</w> <w id="81">была</w> <w id="82">построена</w> <w id="83">три</w> <w id="84">тысячи</w> <w id="85">лет</w> <w id="86">назад</w>, <w id="87">так</w> <w id="88">уж</w> <w id="89">наверное</w> <w id="90">много</w> <w id="91">времени</w> <w id="92">прошло</w>.</s>';
        $substr = 'тысячи лет назад';
        $result = highlight($str, $substr);
        
        $expected = '<s class="trans_sentence" id="transtext_s12"><w id="80">Церковь-то</w> <w id="81">была</w> <w id="82">построена</w> <w id="83">три</w> <w id="84"><span class="search-word">тысячи</span></w> <w id="85"><span class="search-word">лет</span></w> <w id="86"><span class="search-word">назад</span></w>, <w id="87">так</w> <w id="88">уж</w> <w id="89">наверное</w> <w id="90">много</w> <w id="91">времени</w> <w id="92">прошло</w>.</s>';
        $this->assertEquals( $expected, $result );        
    }
    
/*    public function testHighlight3wordsAndSign()
    {
        $str = "\n".'<s class="trans_sentence" id="transtext_s12"><w id="80">Церковь-то</w> <w id="81">была</w> <w id="82">построена</w> <w id="83">три</w> <w id="84">тысячи</w> <w id="85">лет</w> <w id="86">назад</w>, <w id="87">так</w> <w id="88">уж</w> <w id="89">наверное</w> <w id="90">много</w> <w id="91">времени</w> <w id="92">прошло</w>.</s>';
        $substr = 'тысячи лет назад,';
        $result = highlight($str, $substr);
        
        $expected = '<s class="trans_sentence" id="transtext_s12"><w id="80">Церковь-то</w> <w id="81">была</w> <w id="82">построена</w> <w id="83">три</w> <w id="84"><span class="search-word">тысячи</span></w> <w id="85"><span class="search-word">лет</span></w> <w id="86"><span class="search-word">назад</span></w><span class="search-word">,</span> <w id="87">так</w> <w id="88">уж</w> <w id="89">наверное</w> <w id="90">много</w> <w id="91">времени</w> <w id="92">прошло</w>.</s>';
        $this->assertEquals( $expected, $result );        
    }*/
}
