<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Dict\Lemma;
use App\Models\Dict\LemmaFeature;

class LemmaFeatureTest extends TestCase
{
    public function testToCONLLVepAnimateNoun()
    {
//        $lemma = "vel'l";
        $lemma_obj = Lemma::find(24); //where('lemma','like',$lemma)->first();
        $result = $lemma_obj->features->toCONLL();
        
        $expected = ["Animacy=Anim"];
        $this->assertEquals( $expected, $result);        
    }
}
