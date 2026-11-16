<?php

namespace Tests;

use Tests\IsolatedSqliteTestCase as TestCase;

use App\Models\Dict\Wordform;

class WordformTest extends TestCase
{
    public function testGetMainPartManySpaces()
    {
        $wordform = new Wordform;
        $wordform->wordform = '  en   astu  ';

        $result = $wordform->getMainPart();

        $expected = 'astu';
        $this->assertEquals($expected, $result);
    }

    public function testGetMainPartStrangeSpaces()
    {
        $wordform = new Wordform;
        $wordform->wordform = "ei ole\u{00A0}aštun";

        $result = $wordform->getMainPart();

        $expected = 'aštun';
        $this->assertEquals($expected, $result);
    }
}
