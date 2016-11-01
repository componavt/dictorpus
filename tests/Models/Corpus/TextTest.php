<?php

use App\Models\Corpus\Text;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
// use TestCase;


// php artisan make:test 'Models\Corpus\TextTest'

class TextTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }
    
    /** The empty string results to empty string
     */
    public function testMarkupTextEmpty()
    {
        //$this->assertTrue(true);
        $source_text = "";
        
        $text = new Text();
        $xml_text = $text->markupText($source_text);

        $this->assertEquals( strlen($xml_text), 0);
    }
    
    /** Let's markup one sentence.
     */
    public function testMarkupText1sentence()
    {
        $source_text = "I was born to love you.";
        $expected_xml  = '<s id="1"><w id="1">I</w> <w id="2">was</w> <w id="3">born</w> <w id="4">to</w> <w id="5">love</w> <w id="6">you</w>.</s>';
        
        $text = new Text();
        $xml_text = $text->markupText($source_text);

        $this->assertEquals( $xml_text, $expected_xml);
    }
    
    /** Let's markup two sentences with !..
     */
    public function testMarkupText2sentencesWithExclamationMark()
    {
        $source_text   = "Step one!.. Trace two.";
        $expected_xml  = '<s id="1"><w id="1">Step</w> <w id="2">one</w>!..</s>'."\n"
                       . '<s id="2"><w id="3">Trace</w> <w id="4">two</w>.</s>';
        
        $text = new Text();
        $result_xml = $text->markupText($source_text);

        $this->assertEquals( $expected_xml, $result_xml);
    }
    
    /** Let's markup One sentence with hyphens between words (should be treated as one compound word).
     */
    public function testMarkupTextAndWordWithHyphenInsideOneWord()
    {
        $source_text   = "Self-assured - -.";
        $expected_xml  = '<s id="1"><w id="1">Self-assured</w> - -.</s>';
        
        $text = new Text();
        $result_xml = $text->markupText($source_text);
        
        $this->assertEquals( $expected_xml, $result_xml);
    }
    
    // Self-assured trans-American great-great-grandfather likes sugar-free soda.
    
    /** Let's markup One sentence with hyphens between words (should be treated as one compound word).
     */
    public function testMarkupTextAndWordWithHyphenInside()
    {
        $source_text   = "Self-assured trans‒American great–great—grandfather likes sugar―free soda.";
        $expected_xml  = '<s id="1"><w id="1">Self-assured</w> <w id="2">trans‒American</w> <w id="3">great–great—grandfather</w> <w id="4">likes</w> <w id="5">sugar―free</w> <w id="6">soda</w>.</s>';
        
        $text = new Text();
        $result_xml = $text->markupText($source_text);

        $this->assertEquals( $expected_xml, $result_xml);
    }
    
    /** Let's markup One sentence with Indirect speech with quotes.
     */
    public function testMarkupText1sentenceWithIndirectSpeechWithQuotes()
    {
        $source_text   = "”Коса!” – sanutaze venäks.";
        $expected_xml  = '<s id="1">”<w id="1">Коса</w>!”</s>'."\n"
                       . '<s id="2">– <w id="2">sanutaze</w> <w id="3">venäks</w>.</s>';
        
        $text = new Text();
        $result_xml = $text->markupText($source_text);

        $this->assertEquals( $expected_xml, $result_xml);
    }
}
