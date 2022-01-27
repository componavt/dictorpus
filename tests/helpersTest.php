<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

// php artisan make:test helpersTest
// ./vendor/bin/phpunit tests/helpersTest

class helpersTest extends TestCase
{
    public function testConvertQuotes()
    {
        $str = '«»„”“”';
        $result = convert_quotes($str);
        
        $expected = '""""""';
        $this->assertEquals( $expected, $result );        
    }
}
