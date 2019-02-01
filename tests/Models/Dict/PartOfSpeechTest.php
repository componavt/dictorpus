<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Dict\PartOfSpeech;

/* 
 * php artisan make:test Models\Dict\PartOfSpeechTest
 * ./vendor/bin/phpunit tests/Models/Dict/PartOfSpeechTest
*/
class PartOfSpeechTest extends TestCase
{
    public function testGetNameIDs()
    {
        $result = PartOfSpeech::getNameIDs();
        
        $expected = [1, 5, 6, 10, 14, 20];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testGetVerbIDs()
    {
        $result = PartOfSpeech::getVerbID();
        
        $expected = 11;
        $this->assertEquals( $expected, $result);        
    }
}
