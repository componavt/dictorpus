<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Str;

// ./vendor/bin/phpunit tests/Library/StrTest
class StrTest extends TestCase
{
    public function testTrimEqualSubstrFromLeft()
    {
        $str1 = 'tul';
        $str2 = 'tulow';
        $result = Str::trimEqualSubstrFromLeft($str1, $str2);
        
        $expected = ['','ow'];
        $this->assertEquals( $expected, $result);        
    }
}
