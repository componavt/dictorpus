<?php

//use Illuminate\Foundation\Testing\WithoutMiddleware;
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Corpus\Sentence;

// php artisan make:test Models\Corpus\SentenceTest
// ./vendor/bin/phpunit tests/Models/Corpus/SentenceTest

class SentenceTest extends TestCase
{
    public function testWordAddToSentenceWithAloneApostroph()
    {
        $word   = "’";
        $is_word = true;
        $str = '';
        $word_count = 1;
        
        $result_xml = Sentence::wordAddToSentence($is_word, $word, $str, $word_count);

        $expected_xml  = [false, '’', 1];
        $this->assertEquals( $expected_xml, $result_xml);
    }
    
    public function testWordDelimetersQuotation()
    {
        $char   = "“";
        
        $result_xml = mb_strpos(Sentence::word_delimeters(), $char)!==false;

        $expected_xml  = true;
        $this->assertEquals( $expected_xml, $result_xml);
    }
    
        public function testMarkup()
    {
        $str   = "Nikolai Os’kin, “Yhteiskunnallizien laitoksien karjalazen resursukeskuksen” piämies";
        
        $result_xml = Sentence::markup($str, 28);

        $expected_xml  = ['<w id="28">Nikolai</w> <w id="29">Os’kin</w>, “<w id="30">Yhteiskunnallizien</w> <w id="31">laitoksien</w> <w id="32">karjalazen</w> <w id="33">resursukeskuksen</w>” <w id="34">piämies</w>', 35];
        $this->assertEquals( $expected_xml, $result_xml);
    }

}
