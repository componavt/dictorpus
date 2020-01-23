<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Dict\Gramset;

// ./vendor/bin/phpunit tests/Models/Dict/GramsetTest
class GramsetTest extends TestCase
{
    public function testToCONLLNomSing()
    {
        $gramset = Gramset::find(1);
        $result = $gramset->toCONLL();
        
        $expected = ["Number=Sing", "Case=Nom"];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testIsIdForNameTrue()
    {
        $id = 1; //номинатив, ед. ч.
        $result = Gramset::isIdForName($id);
        
        $expected = true;
        $this->assertEquals( $expected, $result);        
    }
    
    public function testIsIdForNameFalse()
    {
        $id = 26; //индикатив, презенс, 1 л., ед. ч., полож. ф.
        $result = Gramset::isIdForName($id);
        
        $expected = false;
        $this->assertEquals( $expected, $result);        
    }
    
    public function testIsIdForVerbTrue()
    {
        $id = 26; //индикатив, презенс, 1 л., ед. ч., полож. ф.
        $result = Gramset::isIdForVerb($id);
        
        $expected = true;
        $this->assertEquals( $expected, $result);        
    }
    
    public function testIsIdForVerbFalse()
    {
        $id = 1; //номинатив, ед. ч.
        $result = Gramset::isIdForVerb($id);
        
        $expected = false;
        $this->assertEquals( $expected, $result);        
    }
}
