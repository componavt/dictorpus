<?php

namespace Tests;

use Tests\TestCase;

use App\Models\Dict\Lemma;

class LemmaFeatureTest extends TestCase
{
    public function testToCONLLVepAnimateNoun()
    {
        //        $lemma = "vel'l";
        $lemma_obj = Lemma::find(24); //where('lemma','like',$lemma)->first();
        $result = $lemma_obj->features->toCONLL();

        $expected = ["Animacy=Anim"];
        $this->assertEquals($expected, $result);
    }
}
