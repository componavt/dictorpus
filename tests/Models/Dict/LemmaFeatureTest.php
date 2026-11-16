<?php

namespace Tests;

use Tests\IsolatedSqliteTestCase as TestCase;

use App\Models\Dict\Lemma;
use App\Models\Dict\LemmaFeature;

class LemmaFeatureTest extends TestCase
{
    public function testToCONLLVepAnimateNoun()
    {
        $lemma = new Lemma;
        $lemma->pos_id = 5; // NOUN

        $features = new LemmaFeature;
        $features->animacy = 1;
        $features->setRelation('lemma', $lemma);

        $result = $features->toCONLL();

        $expected = ["Animacy=Anim"];
        $this->assertEquals($expected, $result);
    }
}
