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
}
