<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Dict\Wordform;

class WordformTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetMainPartManySpaces()
    {
        $wordform_id = 8439; // en astu
        $wordform = Wordform::find($wordform_id);
        
        $result = $wordform->getMainPart();
        
        $expected = 'astu';
        $this->assertEquals( $expected, $result);        
    }
    
    // Should be: "ei ole aštun" -> aštun
    public function testGetMainPartStrangeSpaces()
    {
        $wordform_id = 8861; // ei ole aštun
        $wordform = Wordform::find($wordform_id);
//print $wordform->wordform . " sixth='". ord(mb_substr($wordform->wordform, 6, 1, 'UTF-8')). "'";
        
        $result = $wordform->getMainPart();
        
        $expected = 'aštun';
        $this->assertEquals( $expected, $result);        
    }
    
}
