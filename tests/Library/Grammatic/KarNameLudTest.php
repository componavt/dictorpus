<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\KarNameOlo;

// ./vendor/bin/phpunit tests/Library/Grammatic/KarNameOloTest

class KarNameOloTest extends TestCase
{
    public function testWordformByStemsWithApostroph()
    {
        $dialect_id=44;
        $gramset_id = 22;
        $stems = ['gor’a', 'gor’a', 'gor’a', 'gor’ua', 'gor’a', 'gor’i'];
        $result = KarNameOlo::wordformByStemsPl($stems, $gramset_id, $dialect_id);
        
        $expected = 'gor’ii';
        $this->assertEquals( $expected, $result);        
    }
}
