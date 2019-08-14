<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\KarName;

// ./vendor/bin/phpunit tests/Library/Grammatic/KarNameTest

class KarNameTest extends TestCase
{
    /**
     * plural noun, nom sg
     *
     * @return void
     */
    public function testWordformByStems_pl_nom_sg()
    {
        $dialect_id=47;
        $lang_id=4;
        $gramset_id = 1;
        $name_num = 'pl';
        $stems = ['aluššovat', '', '', '', 'aluššovi', 'aluššobi'];
        $result = KarName::wordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num);
        
        $expected = '';
        $this->assertEquals( $expected, $result);        
    }
    
    /**
     * plural noun, nom sg
     *
     * @return void
     */
    public function testWordformByStems_pl_nom_pl()
    {
        $dialect_id=47;
        $lang_id=4;
        $gramset_id = 2;
        $name_num = 'pl';
        $stems = ['aluššovat', '', '', '', 'aluššovi', 'aluššobi'];
        $result = KarName::wordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num);
        
        $expected = 'aluššovat';
        $this->assertEquals( $expected, $result);        
    }
}
