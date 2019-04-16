<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\KarGram;

// php artisan make:test Models\Library\Grammatic\KarGram
// ./vendor/bin/phpunit tests/Models/Library/Grammatic\KarGram

class KarGramTest extends TestCase
{
    public function testGarmVowelBack1Let() {
        $stem = 'anda';
        $vowel = 'a';
        $result = KarGram::garmVowel($stem, $vowel);
        
        $expected = 'a';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testGarmVowelFront2Let() {
        $stem = 'mäne';
        $vowel = 'ou';
        $result = KarGram::garmVowel($stem, $vowel);
        
        $expected = 'öy';
        $this->assertEquals( $expected, $result);        
    }
    
}
