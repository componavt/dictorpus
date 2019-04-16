<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\VepsName;

// php artisan make:test Models\Library\Grammatic\VepsName
// ./vendor/bin/phpunit tests/Models/Library/Grammatic\VepsName

class VepsNameTest extends TestCase
{
    public function testCountSyllable1() {
        $stems = ['su','pü','ma','pä','so','vö','ö','voi'];
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsName::countSyllable($stem);
        }
//dd($result);        
        $expected = [1,1,1,1,1,1,1,1];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testCountSyllable2() {
        $stems = ['meca','kaivo','lume','sene','vilu','verko','kego', 'maido'];
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsName::countSyllable($stem);
        }
//dd($result);        
        $expected = [2,2,2,2,2,2,2,2];
        $this->assertEquals( $expected, $result);        
    }
    
    
    public function testCountSyllable3() {
        $stems = ['sizare','kirvhe','armha','nagrhe','abajo','abido'];
        $result = [];
        foreach ($stems as $stem) {
            $result[] = VepsName::countSyllable($stem);
        }
//dd($result);        
        $expected = [3,3,3,3,3,3];
        $this->assertEquals( $expected, $result);        
    }
}
