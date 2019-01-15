<?php

use App\Models\Corpus\Text;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
// use TestCase;


// php artisan make:test 'Models\Corpus\TextTest'

class TextTest extends TestCase
{
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
        $source_text = "A was born to love you.";
        $expected_xml  = '<s id="1"><w id="1">A</w> <w id="2">was</w> <w id="3">born</w> <w id="4">to</w> <w id="5">love</w> <w id="6">you</w>.</s>';
        
        $text = new Text();
        $result_xml = $text->markupText($source_text);

        $this->assertEquals( $expected_xml, $result_xml );
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
    
    public function testMarkupTextWithComma()
    {
        $source_text   = "Hö openziba kaikid tulnuzid ristituid kirjutamha da lugemaha vepsän kelel, starinoičiba vepsläižes literaturas, "
                       . "Kodima-lehteses da Kipinäkulehteses.";
        $expected_xml  = '<s id="1"><w id="1">Hö</w> <w id="2">openziba</w> <w id="3">kaikid</w> <w id="4">tulnuzid</w> '
                       . '<w id="5">ristituid</w> <w id="6">kirjutamha</w> <w id="7">da</w> <w id="8">lugemaha</w> <w id="9">vepsän</w> '
                       . '<w id="10">kelel</w>, <w id="11">starinoičiba</w> <w id="12">vepsläižes</w> <w id="13">literaturas</w>, '
                       . '<w id="14">Kodima-lehteses</w> <w id="15">da</w> <w id="16">Kipinäkulehteses</w>.</s>';
        
        $text = new Text();
        $result_xml = $text->markupText($source_text);

        $this->assertEquals( $expected_xml, $result_xml);
    }
    
    public function testMarkupSentenceWithComma()
    {
        $source_text   = "kelel, literaturas, Kodima-lehteses.";
        $expected_xml  = '<s id="1"><w id="1">kelel</w>, <w id="2">literaturas</w>, <w id="3">Kodima-lehteses</w>.</s>';
        
        $text = new Text();
        $result_xml = $text->markupText($source_text);

        $this->assertEquals( $expected_xml, $result_xml);
    }

    public function testMarkupSentenceWithNumbers()
    {
        $source_text   = "Voittajakši tuli Sport-joukko: 6:0.”";
        $expected_xml  = '<s id="1"><w id="1">Voittajakši</w> <w id="2">tuli</w> <w id="3">Sport-joukko</w>: 6:0.”</s>';
        
        $text = new Text();
        $result_xml = $text->markupText($source_text);

        $this->assertEquals( $expected_xml, $result_xml);
    }
    
    public function testMarkupSentenceWithDashes()
    {
        $source_text   = "Niiden keskes om Šoutjärven rahvahan hor, ”Noid”-, ”Vepsän hel’m”-, ”Randaine”-, ”Linduižed”-, ”Armas”- ansamblid da äi toižid.";
//        $expected_xml  = '<s id="1"><w id="1">Niiden</w> <w id="2">keskes</w> <w id="3">om</w> <w id="4">Šoutjärven</w> <w id="5">rahvahan</w> <w id="6">hor</w>, ”<w id="7">Noid</w>”<w id="8">-</w>, ”<w id="9">Vepsän</w> <w id="10">hel’m</w>”<w id="11">-</w>, ”<w id="12">Randaine</w>”<w id="13">-</w>, ”<w id="14">Linduižed</w>”<w id="15">-</w>, ”<w id="16">Armas</w>”- <w id="17">ansamblid</w> <w id="18">da</w> <w id="19">äi</w> <w id="20">toižid</w>.</s>';
        $expected_xml  = '<s id="1"><w id="1">Niiden</w> <w id="2">keskes</w> <w id="3">om</w> <w id="4">Šoutjärven</w> <w id="5">rahvahan</w> <w id="6">hor</w>, ”<w id="7">Noid</w>”-, ”<w id="8">Vepsän</w> <w id="9">hel’m</w>”-, ”<w id="10">Randaine</w>”-, ”<w id="11">Linduižed</w>”-, ”<w id="12">Armas</w>”- <w id="13">ansamblid</w> <w id="14">da</w> <w id="15">äi</w> <w id="16">toižid</w>.</s>';
        
        $text = new Text();
        $result_xml = $text->markupText($source_text);

        $this->assertEquals( $expected_xml, $result_xml);
    }
    
    public function testMarkupSentenceDashBeforeTag()
    {
        $source_text   = "Sid’ susedas Kalag’-posadas jo koumanden kerdan mäni ”Vepsän sarn”-
festival’-konkurs.";
        $expected_xml  = '<s id="1"><w id="1">Sid’</w> <w id="2">susedas</w> <w id="3">Kalag’-posadas</w> <w id="4">jo</w> <w id="5">koumanden</w> <w id="6">kerdan</w> <w id="7">mäni</w> ”<w id="8">Vepsän</w> <w id="9">sarn</w>”-<br />
<w id="10">festival’-konkurs</w>.</s>';
        $text = new Text();
        $result_xml = $text->markupText($source_text);

        $this->assertEquals( $expected_xml, $result_xml);
    }
    
    public function testMarkupSentenceWithProcent()
    {
        $source_text   = "Tegihe sel’ktaks, miše küzutud ristituiden keskes 40% - aktivižid, 35% - ”kacujid”";
        $expected_xml  = '<s id="1"><w id="1">Tegihe</w> <w id="2">sel’ktaks</w>, <w id="3">miše</w> <w id="4">küzutud</w> <w id="5">ristituiden</w> <w id="6">keskes</w> 40% - <w id="7">aktivižid</w>, 35% - ”<w id="8">kacujid</w>”';
        $text = new Text();
        $result_xml = $text->markupText($source_text);

        $this->assertEquals( $expected_xml, $result_xml);
    }
}
