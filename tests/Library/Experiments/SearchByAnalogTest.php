<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Experiments\SearchByAnalog;

// ./vendor/bin/phpunit tests/Library/ExperimentTest
class SearchByAnalogTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
/*    
    public function testSearchPosGramsetByWord()
    {
        $lang_id = 4;
        $property = 'pos';
        $table_name = 'search_'.$property;
        $wordforms = DB::table($table_name)
                  ->whereLangId($lang_id)
                  ->take(10)
                  ->get();
        $expected = $result = [];
        foreach ($wordforms as $wordform) {
            $expected[] = SearchByAnalog::searchPosGramsetByWord($lang_id, $wordform->wordform, $property);
            $result[] = SearchByAnalog::searchPosGramsetByWordWithWList($lang_id, $wordform->wordform, $property);
        }
        $this->assertEquals($expected, $result);        
    }
    
    public function testSearchGramsetByAffix()
    {
        $lang_id = 4;
        $property = 'pos';
        $table_name = 'search_'.$property;
        $wordforms = DB::table($table_name)
                  ->whereLangId($lang_id)
                  ->take(2)
                  ->get();
        $expected = $result = [];
        foreach ($wordforms as $wordform) {
print  "\n".$wordform->wordform."\n";           
            $expected[] = SearchByAnalog::searchGramsetByAffixWithWList($wordform->wordform, $lang_id);
            $result[] = SearchByAnalog::searchGramsetByAffix($wordform->wordform, $lang_id);
        }
        $this->assertEquals($expected, $result);        
    }
*/    
    public function testgGroupGramsetNodeList()
    {
        $node_list = [24 => "gen,\npl\n1678",
          22 => "part,\npl\n1677",
          62 => "abl,\npl\n1676",
          65 => "com,\npl\n1675",
          61 => "ill,\npl\n1673",
          279 => "essive,\npl\n1672",
          280 => "adessive-allative,\npl\n1671",
          60 => "elat,\npl\n1671",
          59 => "trans,\npl\n1670",
          64 => "abes,\npl\n1670",
          23 => "ines,\npl\n1669",
          2 => "nom,\npl\n1669",
          281 => "instructive,\npl\n1668",
          66 => "prol,\npl\n1666",
          4 => "part,\nsg\n1658",
          1 => "nom,\nsg\n1656",
          12 => "abl,\nsg\n1652",
          10 => "ill,\nsg\n1651",
          9 => "elat,\nsg\n1651",
          5 => "trans,\nsg\n1650",
          277 => "essive,\nsg\n1649",
          8 => "ines,\nsg\n1649",
          3 => "gen,\nsg\n1649",
          278 => "adessive-allative,\nsg\n1648",
          14 => "com,\nsg\n1647",
          6 => "abes,\nsg\n1646",
          15 => "prol,\nsg\n1640",
          173 => "infinitive\nIII,\nades\n681",
          179 => "active,\n2nd\nparticiple\n680",
          282 => "perfect,\nactive,\n2nd\nparticiple\n679",
          151 => "potential,\nprs,\n3rd,\npl,\npositive\nform\n679",
          177 => "infinitive\nIII,\nabes\n679",
          172 => "infinitive\nII,\ninstructive\n679",
          171 => "infinitive\nII,\nines\n679",
          174 => "infinitive\nIII,\nill\n679",
          176 => "infinitive\nIII,\nelat\n679",
          175 => "infinitive\nIII,\nines\n679",
          49 => "conditional,\nimperfect,\n3rd,\npl,\npositive\nform\n678",
          28 => "indicative,\nprs,\n3rd,\nsg,\npositive\nform\n678",
          178 => "active,\n1st\nparticiple\n678",
          46 => "conditional,\nimperfect,\n3rd,\nsg,\npositive\nform\n678",
          148 => "potential,\nprs,\n3rd,\nsg,\npositive\nform\n677",
          170 => "infinitive\nI\n677",
          181 => "passive,\n2nd\nparticiple\n676",
          37 => "indicative,\nimperfect,\n3rd,\npl,\npositive\nform\n676",
          180 => "passive,\n1st\nparticiple\n676",
          55 => "imperative,\n3rd,\npl,\npositive\nform\n675",
          34 => "indicative,\nimperfect,\n3rd,\nsg,\npositive\nform\n675",
          52 => "imperative,\n3rd,\nsg,\npositive\nform\n675",
          31 => "indicative,\nprs,\n3rd,\npl,\npositive\nform\n675",
          146 => "potential,\nprs,\n1st,\nsg,\npositive\nform\n644",
          147 => "potential,\nprs,\n2nd,\nsg,\npositive\nform\n644",
          44 => "conditional,\nimperfect,\n1st,\nsg,\npositive\nform\n644",
          45 => "conditional,\nimperfect,\n2nd,\nsg,\npositive\nform\n644",
          48 => "conditional,\nimperfect,\n2nd,\npl,\npositive\nform\n644",
          150 => "potential,\nprs,\n2nd,\npl,\npositive\nform\n644",
          47 => "conditional,\nimperfect,\n1st,\npl,\npositive\nform\n644",
          149 => "potential,\nprs,\n1st,\npl,\npositive\nform\n644",
          27 => "indicative,\nprs,\n2nd,\nsg,\npositive\nform\n642",
          51 => "imperative,\n2nd,\nsg,\npositive\nform\n642",
          54 => "imperative,\n2nd,\npl,\npositive\nform\n642",
          29 => "indicative,\nprs,\n1st,\npl,\npositive\nform\n642",
          30 => "indicative,\nprs,\n2nd,\npl,\npositive\nform\n642",
          26 => "indicative,\nprs,\n1st,\nsg,\npositive\nform\n642",
          36 => "indicative,\nimperfect,\n2nd,\npl,\npositive\nform\n641",
          35 => "indicative,\nimperfect,\n1st,\npl,\npositive\nform\n641",
          33 => "indicative,\nimperfect,\n2nd,\nsg,\npositive\nform\n640",
          32 => "indicative,\nimperfect,\n1st,\nsg,\npositive\nform\n640",
          297 => "indicative,\nimperfect,\nsg,\nconneg.\n321",
          298 => "indicative,\nimperfect,\npl,\nconneg.\n320",
          296 => "indicative,\nprs,\npl,\nconneg.\n320",
          295 => "indicative,\nprs,\nsg,\nconneg.\n306",
          310 => "potential,\nprs,\nconneg.\n45",
          311 => "3rd,\npl,\nconneg.\n44",
          56 => "acc,\nsg\n19",
          57 => "acc,\npl\n16",
          53 => "imperative,\n1st,\npl,\npositive\nform\n2",
          286 => "indicative,\n1st,\npl\n1",
          291 => "imperative,\n3rd,\nsg\n1",
          283 => "indicative,\n1st,\nsg\n1",
          293 => "imperative,\n2nd,\npl\n1",
          290 => "imperative,\n2nd,\nsg\n1",
          288 => "indicative,\n3rd,\npl\n1",
          285 => "indicative,\n3rd,\nsg\n1",
          287 => "indicative,\n2nd,\npl\n1",
          294 => "imperative,\n3rd,\npl\n1",
          284 => "indicative,\n2nd,\nsg\n1"
        ];        
        $expected = [
            0=>[24 => "gen,\npl\n1678",
                22 => "part,\npl\n1677",
                62 => "abl,\npl\n1676",
                65 => "com,\npl\n1675",
                61 => "ill,\npl\n1673",
                279 => "essive,\npl\n1672",
                280 => "adessive-allative,\npl\n1671",
                60 => "elat,\npl\n1671",
                59 => "trans,\npl\n1670",
                64 => "abes,\npl\n1670",
                23 => "ines,\npl\n1669",
                2 => "nom,\npl\n1669",
                281 => "instructive,\npl\n1668",
                66 => "prol,\npl\n1666",
                4 => "part,\nsg\n1658",
                1 => "nom,\nsg\n1656",
                12 => "abl,\nsg\n1652",
                10 => "ill,\nsg\n1651",
                9 => "elat,\nsg\n1651",
                5 => "trans,\nsg\n1650",
                277 => "essive,\nsg\n1649",
                8 => "ines,\nsg\n1649",
                3 => "gen,\nsg\n1649",
                278 => "adessive-allative,\nsg\n1648",
                14 => "com,\nsg\n1647",
                6 => "abes,\nsg\n1646",
                15 => "prol,\nsg\n1640",
                56 => "acc,\nsg\n19",
                57 => "acc,\npl\n16",
                ], 
            1=>[173 => "infinitive\nIII,\nades\n681",
                179 => "active,\n2nd\nparticiple\n680",
                282 => "perfect,\nactive,\n2nd\nparticiple\n679",
                151 => "potential,\nprs,\n3rd,\npl,\npositive\nform\n679",
                177 => "infinitive\nIII,\nabes\n679",
                172 => "infinitive\nII,\ninstructive\n679",
                171 => "infinitive\nII,\nines\n679",
                174 => "infinitive\nIII,\nill\n679",
                176 => "infinitive\nIII,\nelat\n679",
                175 => "infinitive\nIII,\nines\n679",
                49 => "conditional,\nimperfect,\n3rd,\npl,\npositive\nform\n678",
                28 => "indicative,\nprs,\n3rd,\nsg,\npositive\nform\n678",
                178 => "active,\n1st\nparticiple\n678",
                46 => "conditional,\nimperfect,\n3rd,\nsg,\npositive\nform\n678",
                148 => "potential,\nprs,\n3rd,\nsg,\npositive\nform\n677",
                170 => "infinitive\nI\n677",
                181 => "passive,\n2nd\nparticiple\n676",
                37 => "indicative,\nimperfect,\n3rd,\npl,\npositive\nform\n676",
                180 => "passive,\n1st\nparticiple\n676",
                55 => "imperative,\n3rd,\npl,\npositive\nform\n675",
                34 => "indicative,\nimperfect,\n3rd,\nsg,\npositive\nform\n675",
                52 => "imperative,\n3rd,\nsg,\npositive\nform\n675",
                31 => "indicative,\nprs,\n3rd,\npl,\npositive\nform\n675",
                146 => "potential,\nprs,\n1st,\nsg,\npositive\nform\n644",
                147 => "potential,\nprs,\n2nd,\nsg,\npositive\nform\n644",
                44 => "conditional,\nimperfect,\n1st,\nsg,\npositive\nform\n644",
                45 => "conditional,\nimperfect,\n2nd,\nsg,\npositive\nform\n644",
                48 => "conditional,\nimperfect,\n2nd,\npl,\npositive\nform\n644",
                150 => "potential,\nprs,\n2nd,\npl,\npositive\nform\n644",
                47 => "conditional,\nimperfect,\n1st,\npl,\npositive\nform\n644",
                149 => "potential,\nprs,\n1st,\npl,\npositive\nform\n644",
                27 => "indicative,\nprs,\n2nd,\nsg,\npositive\nform\n642",
                51 => "imperative,\n2nd,\nsg,\npositive\nform\n642",
                54 => "imperative,\n2nd,\npl,\npositive\nform\n642",
                29 => "indicative,\nprs,\n1st,\npl,\npositive\nform\n642",
                30 => "indicative,\nprs,\n2nd,\npl,\npositive\nform\n642",
                26 => "indicative,\nprs,\n1st,\nsg,\npositive\nform\n642",
                36 => "indicative,\nimperfect,\n2nd,\npl,\npositive\nform\n641",
                35 => "indicative,\nimperfect,\n1st,\npl,\npositive\nform\n641",
                33 => "indicative,\nimperfect,\n2nd,\nsg,\npositive\nform\n640",
                32 => "indicative,\nimperfect,\n1st,\nsg,\npositive\nform\n640",
                297 => "indicative,\nimperfect,\nsg,\nconneg.\n321",
                298 => "indicative,\nimperfect,\npl,\nconneg.\n320",
                296 => "indicative,\nprs,\npl,\nconneg.\n320",
                295 => "indicative,\nprs,\nsg,\nconneg.\n306",
                310 => "potential,\nprs,\nconneg.\n45",
                311 => "3rd,\npl,\nconneg.\n44",
                53 => "imperative,\n1st,\npl,\npositive\nform\n2",
                ], 
            'other'=>[286 => "indicative,\n1st,\npl\n1",
                      291 => "imperative,\n3rd,\nsg\n1",
                      283 => "indicative,\n1st,\nsg\n1",
                      293 => "imperative,\n2nd,\npl\n1",
                      290 => "imperative,\n2nd,\nsg\n1",
                      288 => "indicative,\n3rd,\npl\n1",
                      285 => "indicative,\n3rd,\nsg\n1",
                      287 => "indicative,\n2nd,\npl\n1",
                      294 => "imperative,\n3rd,\npl\n1",
                      284 => "indicative,\n2nd,\nsg\n1"]];
        $result = SearchByAnalog::groupGramsetNodeList($node_list);
        $this->assertEquals($expected, $result);        
    }  
    
    public function testFirstPositionOfAffix()
    {
        $wordform = 'Alamad';
        $affix = 'mad';
        $result = mb_strpos($wordform, $affix);
        $expected = 3;
        $this->assertEquals($expected, $result);        
    }

}
