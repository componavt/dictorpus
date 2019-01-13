<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Dict\Gramset;

class GramsetTest extends TestCase
{
    public function testToCONLLNomSing()
    {
        $gramset = Gramset::find(1);
        $result = $gramset->toCONLL();
        
        $expected = ["Number=Sing", "Case=Nom"];
        $this->assertEquals( $expected, $result);        
    }
}
