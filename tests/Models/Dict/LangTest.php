<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\Dict\Lang;

// php artisan make:test Models\Dict\LangTest
// ./vendor/bin/phpunit tests/Models/Dict/LangTest.php
    
class LangTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testProjectLangIDs()
    {
        $result = Lang::projectLangIDs();
        $expected = [1, 4, 5, 6];
        $this->assertEquals( $expected, $result);        
    }
}
