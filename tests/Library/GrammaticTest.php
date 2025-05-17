<?php

//use Illuminate\Foundation\Testing\WithoutMiddleware;
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic;
//use App\Library\Grammatic\KarGram;
use App\Models\Dict\Lemma;
// php artisan make:test Library\GrammaticTest
/* 
 ./vendor/bin/phpunit tests/Library/GrammaticTest
 * 
 ./vendor/bin/phpunit tests/Library/Grammatic/KarGramTest
 ./vendor/bin/phpunit tests/Library/Grammatic/KarNameOloTest
 ./vendor/bin/phpunit tests/Library/Grammatic/KarNameTest
 ./vendor/bin/phpunit tests/Library/Grammatic/KarVerbOloTest
 ./vendor/bin/phpunit tests/Library/Grammatic/KarVerbTest
 * 
 ./vendor/bin/phpunit tests/Library/Grammatic/VepsGramTest
 ./vendor/bin/phpunit tests/Library/Grammatic/VepsNameTest
 ./vendor/bin/phpunit tests/Library/Grammatic/VepsVerbTest
 */

class GrammaticTest extends TestCase
{/*
    // Ref: 10. potkiekseh
    public function testWordformsByStemsKarVerbOloPotkiekseh() {
        $template = 'potki|ekseh (-mmos, -h/-hes; -tahes; -h/-hes, -ttihes)';
        $lang_id = 5;
        $pos_id = 11;
        $name_num=null;
        $dialect_id='';
        $is_reflexive = true;
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id, $is_reflexive);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems, $is_reflexive);
        $expected = [26 => 'potkimmos',   27 => 'potkittos',  28 => 'potkih, potkihes',  
                29 => 'potkimmokseh',  30 => 'potkittokseh',  31 => 'potkitahes', 
                295 => 'potkei', 296 => 'potkitahes', 
                70 => 'en potkei',   71 => 'et potkei',  72 => 'ei potkei',  
                73 => 'emmo potkei',  78 => 'etto potkei',  79 => 'ei potkitahes', 
            
                32 => 'potkimmos',   33 => 'potkittos',  34 => 'potkih, potkihes',  
                35 => 'potkimmokseh',  36 => 'potkittokseh',  37 => 'potkittihes', 
                80 => 'en potkinuhes',   81 => 'et potkinuhes',  82 => 'ei potkinuhes',  
                83 => 'emmo potkinuhes',  84 => 'etto potkinuhes',  85 => 'ei potkittuhes', 
            
                86 => 'olen potkinuhes',   87 => 'olet potkinuhes',  88 => 'on potkinuhes',  
                89 => 'olemmo potkinuhes',  90 => 'oletto potkinuhes',  91 => 'ollah potkittuhes, on potkittuhes',  
                92 => 'en ole potkinuhes',  93 => 'et ole potkinuhes',  94 => 'ei ole potkinuhes',  
                95 => 'emmo ole potkinuhes',  96 => 'etto ole potkinuhes',  97 => 'ei olla potkittuhes',
            
                98 => 'olin potkinuhes',   99 => 'olit potkinuhes', 100 => 'oli potkinuhes', 
                101 => 'olimmo potkinuhes', 102 => 'olitto potkinuhes', 103 => 'oldih potkittuhes, oli potkittuhes', 
                104 => 'en olluh potkinuhes', 105 => 'et olluh potkinuhes', 107 => 'ei olluh potkinuhes', 
                108 => 'emmo olluh potkinuhes', 106 => 'etto olluh potkinuhes', 109 => 'ei oldu potkittuhes',
            
                      51 => 'potkei',  52 => 'potkikkahes',  
                53 => 'potkikkuammokseh',  54 => 'potkikkuattokseh',  55 => 'potkittahes',       
                      50 => 'älä potkei',  74 => 'älgäh potkikkahes',  
                75 => '',  76 => 'älgiä potkikkuattokseh',  77 => 'äldähes potkittahes',  
            
//                38 => 'potkizimmos',   39 => 'potkizittos',  40 => 'potkizih, potkizihes',  
                38 => 'potkizimmos',   39 => 'potkizittos',  40 => 'potkizihes',  
                41 => 'potkizimmokseh',  42 => 'potkizittokseh',  43 => 'potkittazihes', 
                110 => 'en potkizihes', 111 => 'et potkizihes', 112 => 'ei potkizihes', 
                113 => 'emmo potkizihes', 114 => 'etto potkizihes', 115 => 'ei potkittazihes',
//                110 => 'en potkizih, en potkizihes', 111 => 'et potkizih, et potkizihes', 112 => 'ei potkizih, ei potkizihes', 
//                113 => 'emmo potkizih, emmo potkizihes', 114 => 'etto potkizih, etto potkizihes', 115 => 'ei potkittazihes',

                44 => 'potkinuzimmos',   45 => 'potkinuzittos',  46 => 'potkinuzihes',  
                47 => 'potkinuzimmokseh',  48 => 'potkinuzittokseh',  49 => 'potkitannuzihes', 
                116 => 'en potkinuzihes', 117 => 'et potkinuzihes', 118 => 'ei potkinuzihes', 
                119 => 'emmo potkinuzihes', 120 => 'etto potkinuzihes', 121 => 'ei potkitannuzihes',
            
                122 => 'olizin potkinuhes', 123 => 'olizit potkinuhes', 124 => 'olis potkinuhes', 
                126 => 'olizimmo potkinuhes', 127 => 'olizitto potkinuhes', 128 => 'oldas potkittuhes', 
                129 => 'en olis potkinuhes', 130 => 'et olis potkinuhes', 131 => 'ei olis potkinuhes', 
                132 => 'emmo olis potkinuhes', 133 => 'etto olis potkinuhes', 134 => 'ei oldas potkittuhes',
            
                135 => 'olluzin potkinuhes', 125 => 'olluzit potkinuhes', 136 => 'ollus potkinuhes', 
                137 => 'olluzimmo potkinuhes', 138 => 'olluzitto potkinuhes', 139 => 'oldanus potkittuhes', 
                140 => 'en ollus potkinuhes', 141 => 'et ollus potkinuhes', 142 => 'ei ollus potkinuhes', 
                143 => 'emmo ollus potkinuhes', 144 => 'etto ollus potkinuhes', 145 => 'ei oldanus potkittuhes',
            
                146 => 'potkinemmos', 147 => 'potkinettos', 148 => 'potkinehes', 
                149 => 'potkinemmokseh', 150 => 'potkinettokseh', 151 => 'potkitannehes', 
                152 => 'en potkinei', 153 => 'et potkinei', 154 => 'ei potkinei', 
                155 => 'emmo potkinei', 156 => 'etto potkinei', 157 => 'ei potkitannehes',
            
                158 => 'ollen potkinuhes', 159 => 'ollet potkinuhes', 160 => 'ollou potkinuhes', 
                161 => 'ollemmo potkinuhes', 162 => 'olletto potkinuhes', 163 => 'oldaneh potkittuhes', 
                164 => 'en olle potkinuhes', 165 => 'et olle potkinuhes', 166 => 'ei olle potkinuhes', 
                167 => 'emmo olle potkinuhes', 168 => 'etto olle potkinuhes', 169 => 'ei oldane potkittuhes',
            
                170 => 'potkiekseh', 171 => '', 172 => '', 
                173 => '', 174 => '', 175 => '', 
                176 => '', 177 => '', 312 => '',
            
                178 => '', 179 => 'potkinuhes', 180 => '', 181 => 'potkittuhes'];
//        $slice = 70;
//        $this->assertEquals(array_slice($expected, 0, $slice, true), array_slice($result, 0, $slice, true));        
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersWithoutLang()
    {
        $word = 'tulow';
        $lang_id = NULL;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'tulow';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersWithLangNotChangable()
    {
        $word = 'tulow';
        $lang_id = 2;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'tulow';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersWtoUWithU()
    {
        $word = 'tulow';
        $lang_id = 5;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'tulou';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersWtoUWithA()
    {
        $word = 'hawgi';
        $lang_id = 5;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'haugi';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLetters2WtoUWithU()
    {
        $word = 'kuwluw';
        $lang_id = 5;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'kuuluu';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersWtoUWithAO()
    {
        $word = 'kaččow';
        $lang_id = 5;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'kaččou';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersWtoUWithO()
    {
        $word = 'liennow';
        $lang_id = 5;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'liennou';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersWtoYWithAuml()
    {
        $word = 'eläw';
        $lang_id = 5;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'eläy';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersWtoYWithAumlY()
    {
        $word = 'kävyw';
        $lang_id = 5;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'kävyy';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersWtoYWithEI()
    {
        $word = 'kergiew';
        $lang_id = 5;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'kergiey';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersWtoYWithAumlEI()
    {
        $word = 'häview';
        $lang_id = 5;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'häviey';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersWtoYWithAumlOuml()
    {
        $word = 'särižöw';
        $lang_id = 5;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'särižöy';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersUumltoYWithAuml()
    {
        $word = 'hüvä';
        $lang_id = 5;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'hyvä';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersUumltoYWithOuml()
    {
        $word = 'müö';
        $lang_id = 5;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'myö';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersUumltoYWithOumlI()
    {
        $word = 'nügöigi';
        $lang_id = 5;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'nygöigi';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersUumlWtoY()
    {
        $word = 'küzüw';
        $lang_id = 5;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'kyzyy';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersUumltoYWithAuml2()
    {
        $word = 'händü';
        $lang_id = 5;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'händy';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testChangeLettersUumltoYWithE()
    {
        $word = 'mennüh';
        $lang_id = 5;
        $result = Grammatic::changeLetters($word,$lang_id);
        
        $expected = 'mennyh';
        $this->assertEquals( $expected, $result);        
    }
       
    // ------------------------------------------------------wordformsByTemplate
    
    public function testNegativeKarelianVerbForInd1Sing() {
        $lang_id = 4;
        $gramset_id = 70; // индикатив, презенс, 1 л., ед. ч., отриц
        $result = Grammatic::negativeForm($gramset_id, $lang_id);
        
        $expected = 'en';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testNegativeFormInd2Sing() {
        $lang_id = 4;
        $gramset_id = 81; // индикатив, имперфект, 2 л., ед. ч., отриц
        $result = Grammatic::negativeForm($gramset_id, $lang_id);
        
        $expected = 'et';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testNegativeVepsVerbForInd1Sing() {
        $lang_id = 1;
        $gramset_id = 70; // индикатив, презенс, 1 л., ед. ч., отриц
        $result = Grammatic::negativeForm($gramset_id, $lang_id);
        
        $expected = 'en';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testVerbWordformByStemsAndazin() {
        $lang_id = 4;
        $stems = [0=>'andua', 1=>'anna', 2=>'anda', 3=>'annoi', 4=>'ando', 5=>'anda', 6=>'anneta', 7=>'annett', 10=>true];
        $gramset_id = 44; //71. кондиционал, имперфект, 1 л., ед. ч., пол.
        $dialect_id=47;
        $result = Grammatic::verbWordformByStems($stems, $gramset_id, $lang_id, $dialect_id);
        
        $expected = 'andazin';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testVerbWordformByStems282Andua() {
        $lang_id = 4;
        $stems = ['andua', 'anna', 'anda', 'annoi', 'ando', 'anda', 'anneta', 'annett', 10=>true];
        $gramset_id = 282; //141. актив, 2-е причастие  (сокращенная форма); перфект (форма основного глагола)
        $dialect_id=47;
        $result = Grammatic::verbWordformByStems($stems, $gramset_id, $lang_id, $dialect_id);
        
        $expected = 'andan';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testVerbWordformByStemsActive1Partic() {
        $lang_id = 4;
        $stems = ['tulla', 'tule', 'tule', 'tuli', 'tuli', 'tul', 'tulla', 'tuld', 10=>true];
        $gramset_id = 178; //139. актив, 1-е причастие 
        $dialect_id=47;
        $result = Grammatic::verbWordformByStems($stems, $gramset_id, $lang_id, $dialect_id);
        
        $expected = 'tulija';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testCondImp3SingPolByStemAndazin() {
        $lang_id = 4;
        $stems = ['andua', 'anna', 'anda', 'annoi', 'ando', 'anda', 'anneta', 'annett', 10=>true];
        $gramset_id = 46; //73. кондиционал, имперфект, 3 л., ед. ч., пол.
        $dialect_id=47;
        $result = Grammatic::verbWordformByStems($stems, $gramset_id, $lang_id, $dialect_id);
        
        $expected = 'andais’';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testPotPres3PlurPolByStemAnnettanneh() {
        $lang_id = 4;
        $stems = ['andua', 'anna', 'anda', 'annoi', 'ando', 'anda', 'anneta', 'annett', 10=>true];
        $gramset_id = 151; //12. потенциал, презенс, 3 л., мн. ч., пол.
        $dialect_id=47;
        $result = Grammatic::verbWordformByStems($stems, $gramset_id, $lang_id, $dialect_id);
        
        $expected = 'annettanneh';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testToRightFormApostroph() {
        $word = "ändäis'";
        $result = Grammatic::toRightForm($word);
        $expected = "ändäis’";
        $this->assertEquals( $expected, $result);        
    }
    
    public function testToRightFormWhitespaces() {
        $word = "elgiä  olgua";
        $result = Grammatic::toRightForm($word);
        $expected = "elgiä olgua";
        $this->assertEquals( $expected, $result);        
    }
    
    public function testToRightFormN() {
        $word = "päivińka";
        $result = Grammatic::phoneticsToLemma($word);
        $expected = "päivin’ka";
        $this->assertEquals( $expected, $result);        
    }
    
    public function testToRightFormNFalse() {
        $word = "päivińka";
        $result = Grammatic::removeSpaces($word); 
        $expected = "päivińka";
        $this->assertEquals( $expected, $result);        
    }
    
    public function testToRightFormPaivuFalse() {
        $word = "päivü";
        $result = Grammatic::removeSpaces($word);
        $expected = "päivü";
        $this->assertEquals( $expected, $result);        
    }
    
    public function testToRightFormRemoveApostroph() {
        $words = [
            'st΄ebeĺ' => 'stebel’',
            'peń g΄ärvut' => 'pen’ gärvut',
            'd΄üuŕ' => 'düur’',
            'g΄üŕ' => 'gür’',
            'ĺeht΄e' => 'lehte',
            'ĺeht΄i' => 'lehti',
            'ĺät΄ik' => 'lätik',
            'g΄öńikeine' => 'gönikeine',
            't΄ähtaińe' => 'tähtaine',
            't΄üńištuda' => 'tüništuda',
            't΄üuniśt΄üö' => 'tüunistüö',
            'd΄ärvenseĺgä'=>'därvenselgä',
            
            'heiеt΄t΄iäččie' => 'heiеttiäččie',
            'artut΄t΄i' => 'artutti',
//            't΄vet΄t΄ii' => 'tvettii',
//          'seĺgitada' => 'sel’gitada',            // так в словаре
            'eht΄t΄ä' => 'ehttä',
            'mustu smorod΄in' => 'mustu smorodin',
            'pi̮aporot΄t΄i' => 'piaporotti',
            'höt΄t΄i' => 'hötti',
            'mut΄t΄i' => 'mutti',
            'lut΄t΄i' => 'lutti',
            'lut΄t΄e' => 'lutte',
            'čid΄ziliusk' => 'čidziliusk',
            'ĺöt΄t΄ä' => 'löttä',
            'löt΄t΄ö' => 'löttö',
            'rubiĺöt΄t΄ä' => 'rubilöttä',
            'rubiĺöt΄t΄ö' => 'rubilöttö',
            'moaĺöt΄t΄ö' => 'moalöttö',
            'čuńź' => 'čun’z’',
            't΄üt΄t΄ö' => 'tüttö',
            'roht΄t΄ä' => 'rohttä',
            'kiit΄t΄iä' => 'kiittiä',
            'kiit΄t΄ii' => 'kiittii',
            'kit΄t΄ä' => 'kittä',
            'mit΄t΄ünäne' => 'mittünäne',
            'mit΄t΄äine' => 'mittäine',
            'mit΄t΄e' => 'mitte',
            'mit΄ńe' => 'mitne',
            'keikut΄t΄e' => 'keikutte',
            't΄üh΄geińe' => 'tühgeine',
            'väjet΄t΄öm' => 'väjettöm',
            'viät΄t΄ömä' => 'viättömä',
            'šüwhüt΄t΄iä' => 'šüwhüttiä',
            'śüwhüt΄t΄ii' => 'süwhüttii',
//            'siĺkt΄ä' => 'sil’ktä',
            'iäńet΄t΄ä' => 'iänettä',
            'hengit΄t΄iä' => 'hengittiä',
            'hengit΄t΄ii' => 'hengittii',
            'peit΄t΄üä' => 'peittüä',
            'peit΄t΄ie' => 'peittie',
            'peit΄t΄iä' => 'peittiä',
            'peit΄t΄öä' => 'peittöä',
            'peit΄t΄ii' => 'peittii',
            'veńüt΄t΄üäkše' => 'venüttüäkše',
            'venüt΄t΄öäčie' => 'venüttöäčie',
            'zavot΄t΄ä' => 'zavottä',
            'revit΄t΄üä' => 'revittüä',
            'revit΄t΄iä' => 'revittiä',
            'revit΄t΄öä' => 'revittöä',
            'kät΄kie' => 'kätkie',
            'kät΄kii' => 'kätkii',
            'it΄et΄t΄äjä' => 'itettäjä',
            'it΄kettaj' => 'itkettaj',
            'it΄kijä' => 'itkijä',
            'it΄kiji' => 'itkiji',
            'iäńeĺĺä it΄kijä' => 'iänellä itkijä',
            'p΄räšk' => 'präšk',
            'šiäńd΄ĺičii' => 'šiändličii',
            'kit΄kie' => 'kitkie',
            'panna küt΄küöh' => 'panna kütküöh',
            'art΄t΄eĺi' => 'artteli',
            'g΄rähk' => 'grähk',
            'pit΄kä' => 'pitkä',
            ];
        $result = [];
        foreach ($words as $word =>$word_exp) {
            $result[] = Grammatic::phoneticsToLemma($word);
        }
        $this->assertEquals(array_values($words), $result);        
    }
    
    public function testToRightFormDZ() {
        $words = [
            'lińdžoi' => 'lindžoi',
            'lindžuoi' => 'lindžuoi',
            'luńd΄žuo' => 'lun’džuo',
            'mańdžikka' => 'mandžikka',
            'mańdžoi' => 'mandžoi',
            'mand΄žoi' => 'mandžoi',
            'mańd΄žuo' => 'man’džuo',
            'mańd΄žuoi' => 'man’džuoi',
            'ĺiäd΄žö' => 'liädžö',
            'lod΄ž' => 'lodž',
            'ud΄žve' => 'udžve',
            'lid΄žu' => 'lidžu',
            'lid΄žut' => 'lidžut',
            'kud΄žahańe' => 'kudžahane',
            'kud΄žoi' => 'kudžoi',
            'kud΄žuo' => 'kudžuo',
            'kud΄žuoi' => 'kudžuoi',
            'čid΄žiliuško' => 'čidžiliuško',
            'čid΄žiliušku' => 'čidžiliušku',
            'čid΄žiliusku' => 'čidžiliusku',
            'šid΄žiĺiuška' => 'šidžiliuška',
            'čid΄žeĺäwšku' => 'čidželäwšku',
            'čud΄žüĺüškö' => 'čudžülüškö',
            'čid΄žilüskä' => 'čidžilüskä',
            'čuńd΄žu' => 'čun’džu',
            'čońd΄ž' => 'čon’dž',
            'čuńd΄ž' => 'čun’dž',
            'küńd΄ži' => 'kündži',
            'künd΄ži' => 'kündži',
            'küńd΄ž' => 'kün’dž',
            'pad΄ža' => 'padža',
            'häd΄žvä' => 'hädžvä',
            ];
        $result = [];
        foreach ($words as $word =>$word_exp) {
            $result[] = Grammatic::phoneticsToLemma($word);
        }
        $this->assertEquals(array_values($words), $result);        
    }
    
    public function testToRightFormDiacretInEnd() {
        $words = ['bukĺ'=>'bukl’', 
            'd΄umalanbembeĺ'=>'d’umalanbembel’',
            'hagartuuĺ' => 'hagartuul’',
            'kaĺĺ' => 'kal’l’',
            'kažĺ' => 'kažl’',
            'keviĺĺin tuuĺ' => 'kevillin tuul’',
            'koĺianbembeĺ' => 'kolianbembel’',
            'nami̮ĺ' => 'namil’',
            'pakkuĺ' => 'pakkul’',
            'pakĺ' => 'pakl’',
            'pihĺ' => 'pihl’',
            'tuuĺ' => 'tuul’',
            'vemmeĺ' => 'vemmel’',
            'ńiiń' => 'niin’',
            'niń' => 'nin’',
            'pakkań' => 'pakkan’',
            'svińć' => 'svin’c’',
            'räbiń' => 'räbin’',
            'd΄uuŕ' => 'd’uur’',
            'hämäŕ' => 'hämär’',
            'juŕ' => 'jur’',
            'juuŕ' => 'juur’',
            'jüŕ' => 'jür’',
            'koŕ' => 'kor’',
            'kaŕ' => 'kar’',
            'kuoŕ' => 'kuor’',
            'muaŕ' => 'muar’',
            'vahtaŕ' => 'vahtar’',
            'vehoŕ' => 'vehor’',
            'viehkuŕ' => 'viehkur’',
            'vihoŕ' => 'vihor’',
            'vihŕ' => 'vihr’',
            'raiś' => 'rais’',
            'kavaź' => 'kavaz’',
            'kuuź' => 'kuuz’',
            'kuź' => 'kuz’',
            'onduź' => 'onduz’',
            'onź' => 'onz’',
            'varź' => 'varz’',
            
            'artut΄t΄' => 'artut’t’',
            'artut΄' => 'artut’',
            'voišieńv' => 'voišien’v',
            'küńź' => 'kün’z’',
            'ri̮śś' => 'ris’s’',
            'ńäńń' => 'nän’n’',
            'keldboĺeźń' => 'keldbolez’n’',
            'šäriśś' => 'šäris’s’',
            'aźź' => 'az’z’',
            'toŕv' => 'tor’v',
            'toŕv΄' => 'tor’v’',
            'miśś' => 'mis’s’'
            ];
        $result = [];
        foreach ($words as $word =>$word_exp) {
            $result[] = Grammatic::phoneticsToLemma($word);
        }
        $this->assertEquals(array_values($words), $result);        
    }
    
    public function testToRightFormBeforeConsonant() {
        $words = [
            'gaĺbuu'=>'gal’buu', 
            'saĺm' => 'sal’m', 
            'koŕb' => 'kor’b',
            'maŕd΄' => 'mar’d’',
            'maŕg΄' => 'mar’g’',
            'mäŕg' => 'mär’g',
            'mäŕg΄' => 'mär’g’',
            'oŕhoi' => 'or’hoi',
            'paŕhan' => 'par’han',
            'muśt΄oi' => 'mus’t’oi',
            'muśt΄uo' => 'mus’t’uo',
            'muśt΄uoi' => 'mus’t’uoi',
            'vaśk' => 'vas’k',
            'vaśk΄' => 'vas’k’',
            'tuuĺhagar' => 'tuul’hagar',
            
            'hiŕpulo' => 'hir’pulo',
            ];
        $result = [];
        foreach ($words as $word =>$word_exp) {
            $result[] = Grammatic::phoneticsToLemma($word);
        }
        $this->assertEquals(array_values($words), $result);        
    }
    
    public function testToRightFormDiacretBeforeA() {
        $words = ['kaižĺa' => 'kaižl’a',
            'kazĺa' => 'kazl’a',
            'koĺakko' => 'kol’akko',
            'pihĺai' => 'pihl’ai',
            'viĺĺa' => 'vil’l’a',
            'perńav' => 'pern’av',
            'kuŕava' => 'kur’ava',
            'kuuŕa' => 'kuur’a',
            'śeŕanka' => 'ser’anka',
            'aśśa' => 'as’s’a',
            'kośśa' => 'kos’s’a',
            'd΄aśśaĺ' => 'd’as’s’al’',
            'nuakovaĺńa' => 'nuakoval’n’a',
            'nakovaĺna' => 'nakoval’na',
            'nakovaĺn' => 'nakoval’n',
            'ni̮akovaĺńja' => 'niakovalnja',
            'sobrańja' => 'sobranja',
            'ĺämmiśranda' => 'lämmis’randa',
            
            'lat΄ta' => 'lat’t’a',
            ];
        $result = [];
        foreach ($words as $word =>$word_exp) {
            $result[] = Grammatic::phoneticsToLemma($word);
        }
        $this->assertEquals(array_values($words), $result);        
    }
    
    public function testToRightFormDiacretBeforeO() {
        $words = ['kaĺĺo' => 'kal’l’o',
            'kĺon' => 'kl’on',
            'kĺona' => 'kl’ona',
            'vaĺožńikk' => 'val’ožnikk',
            'šĺott' => 'šl’ott',
            'vaĺožńikk' => 'val’ožnikk',
            'beŕoga' => 'ber’oga',
            'beŕogaranda' => 'ber’ogaranda',
            'moŕo' => 'mor’o',
            'muuŕoi' => 'muur’oi',
            'muwŕoi' => 'muwr’oi',
            'ŕoun' => 'r’oun',
            'čeŕohm' => 'čer’ohm',
            'suśśod' => 'sus’s’od'];
        $result = [];
        foreach ($words as $word =>$word_exp) {
            $result[] = Grammatic::phoneticsToLemma($word);
        }
        $this->assertEquals(array_values($words), $result);        
    }
    
    public function testToRightFormDiacretBeforeU() {
        $words = ['šĺuakott' => 'šl’uakott',
            'šĺuboi' => 'šl’uboi',
            'šĺubuoi' => 'šl’ubuoi',
            'd΄uońuo' => 'd’uon’uo',
            'ńuaglahut' => 'n’uaglahut',
            'muuŕuo' => 'muur’uo',
            'kut΄kutti̮a' => 'kut’kuttia',
            'raameńńu' => 'raamen’n’u',
            ];
        $result = [];
        foreach ($words as $word =>$word_exp) {
            $result[] = Grammatic::phoneticsToLemma($word);
        }
        $this->assertEquals(array_values($words), $result);        
    }
    
    public function testToRightFormDiacretOther() {
        $words = [ 
            'giĺiηgeińe'=>'gilingeine',
            'hiĺĺeta' => 'hilleta',
            'hiĺĺetä' => 'hilletä',
            'järvenšeĺgä' => 'järvenšelgä',
            'hiĺĺiine' => 'hilliine',
            'kaĺĺivo' => 'kallivo',
            'kevättuuĺe' => 'kevättuule',
            'ĺehmuz' => 'lehmuz',
            'ĺeht' => 'leht',
            'ĺehtez' => 'lehtez',
            'ĺehti' => 'lehti',
            'ĺehtoz' => 'lehtoz',
            'ĺeht΄' => 'leht’',
            'ĺeivät' => 'leivät',
            'ĺep' => 'lep',
            'ĺete' => 'lete',
            'ĺepp' => 'lepp',
            'ĺeppi' => 'leppi',
            'ĺeppä' => 'leppä',
            'ĺeppö' => 'leppö',
            'ĺeppü' => 'leppü',
            'ĺiete' => 'liete',
            'ĺindoi' => 'lindoi',
            'ĺinčikk' => 'linčikk',
            'ĺińčikkä' => 'linčikkä',
            'ĺipo' => 'lipo',
            'ĺiäzö' => 'liäzö',
            'ĺiäzü' => 'liäzü',
            'ĺiäžmü' => 'liäžmü',
            'ĺäg' => 'läg',
            'ĺähte' => 'lähte',
            'ĺüĺü' => 'lülü',
            'piĺve' => 'pilve',
            'piĺves' => 'pilves',
            'piĺvez' => 'pilvez',
            'piĺvikkö' => 'pilvikkö',
            'seĺged' => 'selged',
            'seĺgie' => 'selgie',
            'seĺgiä' => 'selgiä',
            'seĺgä' => 'selgä',
            'seĺitra' => 'selitra',
            'seĺvä' => 'selvä',
            'sĺäc' => 'släc',
            'sĺäč' => 'släč',
            'tuĺjaańe' => 'tuljaane',
            'tuĺĺi' => 'tulli',
            'tuĺĺii' => 'tullii',
            'tuuĺe' => 'tuule',
            'tuuĺi' => 'tuuli',
            'zaĺiv' => 'zaliv',
            'šeĺged' => 'šelged',
            'šeĺgä' => 'šelgä',
            'šĺöttü' => 'šlöttü',
            'šĺöäččä' => 'šlöäččä',
            'šĺöččä' => 'šlöččä',
            'šuaĺist΄uo' => 'šualist’uo',
            'bobaińe' => 'bobaine',
            'cveteińe' => 'cveteine',
            'cveti̬ińe' => 'cvetiine',
            'cvetuińe' => 'cvetuine',
            'cvetuuńe' => 'cvetuune',
            'd΄ońikaińe' => 'd’onikaine',
            'hankińi' => 'hankini',
            'hebocaańe' => 'hebocaane',
            'hebočeińe' => 'hebočeine',
            'hepokkaińe' => 'hepokkaine',
            'hepokkaińi' => 'hepokkaini',
            'hienońe vihma' => 'hienone vihma',
            'hienońe vihmańe' => 'hienone vihmane',
            'himmungańi' => 'himmungani',
            'hämäräińe' => 'hämäräine',
            'höbočaińe' => 'höbočaine',
            'höbočeińe' => 'höbočeine',
            'kńäziččä' => 'knäziččä',
            'jońikeińe' => 'jonikeine',
            'jumalanheboońe' => 'jumalanheboone',
            'juopukkaińe' => 'juopukkaine',
            'kandaińe' => 'kandaine',
            'kannattajańe' => 'kannattajane',
            'kudmaańe' => 'kudmaane',
            'kudmeińe' => 'kudmeine',
            'kuohańi' => 'kuohani',
            'kuudmaińe' => 'kuudmaine',
            'kuuhaańe' => 'kuuhaane',
            'kuvahaańe' => 'kuvahaane',
            'kuvahaińe' => 'kuvahaine',
            'kuvahaińi' => 'kuvahaini',
            'kuvahańe' => 'kuvahane',
            'käžńä' => 'käžnä',
            'leińiž' => 'leiniž',
            'lunźikaińe' => 'lunzikaine',
            'lunźikeińe' => 'lunzikeine',
            'manźikaińe' => 'manzikaine',
            'manźikeińe' => 'manzikeine',
            'mančikkaińi' => 'mančikkaini',
            'mańčingaini' => 'mančingaini',
            'mechaańe' => 'mechaane',
            'mullońe heinä' => 'mullone heinä',
            'murikeińe' => 'murikeine',
            'musket sertrikaańe' => 'musket sertrikaane',
            'mussikkańi' => 'mussikkani',
            'mučuuńe järvut' => 'mučuune järvut',
            'ńemak' => 'nemak',
            'ńem΄' => 'nem’',
            'ńiakośśi' => 'niakossi',
            'ńiińi' => 'niini',
            'nii̯ńi' => 'niini',
            'ńältä' => 'nältä',
            'ńäre' => 'näre',
            'orgi̮ińe' => 'orgiine',
            'pakaańe' => 'pakaane',
            'pakaińe' => 'pakaine',
            'pakeińe' => 'pakeine',
            'pakkańe' => 'pakkane',
            'pakkańi' => 'pakkani',
            'parhaańe' => 'parhaane',
            'parhaińe' => 'parhaine',
            'peiveińe' => 'peiveine',
            'pimedeińe' => 'pimedeine',
            'počkańe' => 'počkane',
            'päivińka' => 'päivin’ka',
            'päiväńe' => 'päiväne',
            'päivääńe' => 'päivääne',
            'päivüöińe' => 'päivüöine',
            'pääväńe' => 'pääväne',
            'räbińä' => 'räbinä',
            'sestrikaańe' => 'sestrikaane',
            'tuĺjaańe' => 'tuljaane',
            'tähtheińe' => 'tähtheine',
            'urbaańe' => 'urbaane',
            'urbuuńe' => 'urbuune',
            'užvańi' => 'užvani',
            'äńik' => 'änik',
            'äńiki̬ita' => 'änikiita',
            'äńikoita' => 'änikoita',
            'äńiköidä' => 'äniköidä',
            'čigičaińe' => 'čigičaine',
            'čigičeińe' => 'čigičeine',
            'kaŕe' => 'kare',
            'maŕji' => 'marji',
            'ŕäbin' => 'räbin',
            'muśśik' => 'mussik',
            'muśśikka' => 'mussikka',
            'śera' => 'sera',
            'luuźik' => 'luuzik',
            'maaźik' => 'maazik',
            'oźer' => 'ozer',
            'veźi' => 'vezi'];
        $result = [];
        foreach ($words as $word =>$word_exp) {
            $result[] = Grammatic::phoneticsToLemma($word);
        }
        $this->assertEquals(array_values($words), $result);        
    }
    
    public function testMaxStem() {
        $stems = ['andua', 'anna', 'anda', 'annoi', 'ando', 'anda', 'anneta', 'annett'];
        $result = Grammatic::maxStem($stems);
        
        $expected = ['an', 'dua'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testMaxStemVepsVerb() {
        $lang_id = 1;
        $pos_id = 11;
        $stems = ['ant', 'anda', 'andoi', 'and', 'anda', 'and', 't', 'a', ''];
        $result = Grammatic::maxStem(array_slice($stems, 0, 5), $lang_id, $pos_id);
        
        $expected = ['an', 't'];
        $this->assertEquals( $expected, $result);        
    }
    
    // stemsFromTemplate()
    public function testStemsFromTemplateIncorrectLang() {
        $lang_id = 3;
        $pos_id = 0;
//        $dialect_id=47;
        $template = "ativo, ativo, ativo, ativu, ativo, ativo";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
        
        $expected = [null, null, $template, null];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateKarelianIncorrectPOS() {
        $lang_id = 4;
        $pos_id = 3; // conjunction
//        $dialect_id=47;
        $template = "ativo, ativo, ativo, ativu, ativo, ativo";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
        
        $expected = [null, null, $template, null];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVepsIncorrectPOS() {
        $lang_id = 1; // veps
        $pos_id = 3; // conjunction
//        $dialect_id=43; // New written Veps
        $template = "{{vep-decl-stems|n=pl|Alama|d|id}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
//dd($result);        
        $expected = [null, null, $template, NULL];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVepsIncorrectTemplate() {
        $lang_id = 1; // veps
        $pos_id = 3; // conjunction
//        $dialect_id=43; // New written Veps
        $template = "{ativo, ativo, ativo, ativu, ativo}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
        
        $expected = [null, null, $template, NULL];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateWithoutBrackets() {
        $lang_id = 4;
        $pos_id = 5;
//        $dialect_id=47;
        $template = "ativo, ativo, ativo, ativu, ativo, ativo";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
        
        $expected = [null, null, $template, NULL];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateIncorrectNumberOfStems() {
        $lang_id = 4;
        $pos_id = 5;
//        $dialect_id=47;
        $template = "{ativo, ativo, ativo, ativu, ativo}";
  
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
//dd($result);                
        $expected = [null, null, $template, null];
        $this->assertEquals( $expected, $result);        
    }
    
    // stemsFromTemplate() karelian nominals
    public function testStemsFromTemplatePieni() {
        $lang_id = 4;
        $pos_id = 1; //adjective
        $dialect_id=47;
        $num = '';
        $template = "{pieni, piene, piene, piendä, pieni, pieni}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [0=>['pieni', 'piene', 'piene', 'piendä', 'pieni', 'pieni', 10=>false],
            1=>null, 2=>'pien', 3=>'i'];
        $this->assertEquals( $expected, $result);        
    }
    
    // stemsFromTemplate() karelian verbs
    public function testStemsFromTemplateTulla() {
        $lang_id = 4;
        $pos_id = 11;
        $dialect_id=47;
        $num = '';
        $template = "{tulla, tule, tule, tuli, tuli, tul, tulla, tuld}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [0=>['tulla', 'tule', 'tule', 'tuli', 'tuli', 'tul', 'tulla', 'tuld', 10=>true],
            1=>null, 2=>'tul', 3=>'la'];
//dd($result);        
        $this->assertEquals( $expected, $result);        
    }
    
    // Olo pezovezi
    public function testStemsFromTemplateOloPezovezi() {
        $lang_id = 5;
        $pos_id = 5; //adjective
        $dialect_id=44;
        $num = '';
        $template = "pezo||v|ezi (-ien, -etty; -ezii/-ezilöi)";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [0=>['pezovezi', 'pezovie', 'pezovede', 'pezovetty', 'pezovezi/pezovezilöi', 'pezovezi/pezovezilöi', 10=>false],
            1=>null, 2=>'pezo||v', 3=>'ezi'];
        $this->assertEquals( $expected, $result);        
    }
    
    // stemsFromTemplate() veps nominals
    public function testStemsFromTemplateVeps() {
        $lang_id = 1;
        $pos_id = 5; // proper noun
//        $dialect_id=43;
        $template = "{{vep-decl-stems|aba|i|jon|jod|joid}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
//dd($result);        
        $expected = [0=>['abai', 'abajo', 'abajo', 'abajod', 'abajoi', ''],
            1=>null, 2=>'aba', 3=>'i'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVepsKoiv() {
        $lang_id = 1;
        $pos_id = 5; // noun
        $dialect_id=43;
        $template = "{{vep-decl-stems|koiv||un|ud|uid}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
//dd($result);        
        $expected = [0=>['koiv', 'koivu', 'koivu', 'koivud', 'koivui', ''],
            1=>null, 2=>'koiv', 3=>''];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVepsSg() {
        $lang_id = 1;
        $pos_id = 14; // proper noun
//        $dialect_id=43;
        $template = "{{vep-decl-stems|n=sg|Amerik||an|ad}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
//dd($result);        
        $expected = [0=>['Amerik', 'Amerika', 'Amerika', 'Amerikad', '', ''],
            1=>'sg', 2=>'Amerik', 3=>''];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVepsPl() {
        $lang_id = 1;
        $pos_id = 14; // proper noun
//        $dialect_id=43;
        $template = "{{vep-decl-stems|n=pl|Alama|d|id}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
//dd($result);        
        $expected = [0=>['Alamad', '', '', '', 'Alamai', ''],
            1=>'pl', 2=>'Alama', 3=>'d'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVepsNeičukaine() {
        $lang_id = 1;
        $pos_id = 5; // noun
//        $dialect_id=43;
        $template = "neičuka|ine (-ižen, -št, -ižid)";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
//dd($result);        
        $expected = [0=>['neičukaine', 'neičukaiže', 'neičukaiže', 'neičukašt', 'neičukaiži', ''],
            1=>null, 2=>'neičuka', 3=>'ine'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVepsČoma() {
        $lang_id = 1;
        $pos_id = 1; 
//        $dialect_id=43;
        $template = "čom|a (-an, -id)";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
//dd($result);        
        $expected = [0=>['čoma', 'čoma', 'čoma', 'čomad', 'čomi', ''],
            1=>null, 2=>'čom', 3=>'a'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateVepsSur() {
        $lang_id = 1;
        $pos_id = 1; 
//        $dialect_id=43;
        $template = "sur|’ (-en, ’t, -id)";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
//dd($result);        
        $expected = [0=>['sur’', 'sure', 'sur', 'sur’t', 'suri', ''],
            1=>null, 2=>'sur', 3=>'’'];
        $this->assertEquals( $expected, $result);        
    }
    
    // stemsFromTemplate() veps verbs
    public function testStemsFromTemplateVepsVerbVoikta() {
        $lang_id = 1;
        $pos_id = 11; // verb
//        $dialect_id=43;
        $template = "{{vep-conj-stems|voik|ta|ab|i}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
//dd($result);        
        $expected = [0=>['voik', 'voika', 'voiki', 'voik', 'voika', 'voik', 't', 'a', ''],
            1=>null, 2=>'voik', 3=>'ta'];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testStemsFromTemplateVepsVerbNullDialect() {
        $lang_id = 1;
        $pos_id = 11; // verb
//        $dialect_id=43;
        $template = "{{vep-conj-stems|töndu|da|b|i}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
//dd($result);        
        $expected = [0=>['töndu', 'töndu', 'töndui', 'töndu', 'töndu', 'töndu', 'd', 'a', ''],
            1=>null, 2=>'töndu', 3=>'da'];
        $this->assertEquals( $expected, $result);        
    }
    public function testStemsFromTemplateVepsVerbAstta() {
        $lang_id = 1;
        $pos_id = 11; // verb
//        $dialect_id=43;
        $template = "{{vep-conj-stems|ast|ta|ub|ui}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
//dd($result);        
        $expected = [0=>['ast', 'astu', 'astui', 'ast', 'astu', 'ast', 't', 'a', ''],
            1=>null, 2=>'ast', 3=>'ta'];
        $this->assertEquals( $expected, $result);        
    }
       
    public function testStemsFromTemplateVepsVerbValita() {
        $lang_id = 1;
        $pos_id = 11; // verb
//        $dialect_id=43;
        $template = "{{vep-conj-stems|vali|ta|čeb|či}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
//dd($result);        
        $expected = [0=>['vali', 'valiče', 'valiči', 'vali', 'valič', 'valiče','t','a', ''],
            1=>null, 2=>'vali', 3=>'ta'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseLemmaField()
    {
        $lemma_field="abei|";
        $data = ['lemma'=>$lemma_field, 'lang_id'=>null, 'pos_id'=>null, 'dialect_id'=>null, 'wordform_dialect_id'=>null];
        $result = Grammatic::parseLemmaField($data);
       
        $expected = ['abei','','abei','', false, null];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateCompound() {
        $template = "abu||ozuteseli|ne (-žen, -št, -žid)";
        $lang_id = 1;
        $pos_id = 1; // noun
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [[0=>'abuozuteseline', 
                      1=>'abuozuteseliže', 
                      2=>'abuozuteseliže', 
                      3=>'abuozuteselišt', 
                      4=>'abuozuteseliži', 
                      5=>''], $num, 'abu||ozuteseli', 'ne'];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testStemsFromTemplateKarSg() {
        $template = "Kariel|a {-a, -ua}";
        $lang_id = 4;
        $pos_id = 1; // noun
        $num = 'sg';
        $dialect_id = 47;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'Kariela', 
                      1=>'Kariela', 
                      2=>'Kariela', 
                      3=>'Karielua', 
                      4=>'', 
                      5=>'',
                      10=>true], $num, 'Kariel', 'a'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVepsWrongPartitiv() {
        $lang_id = 1;
        $pos_id = 5; // noun
//        $dialect_id=43;
        $template = "neičuka|ine (-ižed, -št, -ižid)";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
//dd($result);        
        $expected = [0=>['neičukaine'],
            1=>null, 2=>'neičuka|ine (-ižed, -št, -ižid)', 3=>null];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateCompoundVepsNoun() {
        $template = "abu||ozuteseli|ne";
        $lang_id = 1;
        $pos_id = 1; // noun
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [[0=>'abuozuteseline'], $num, 'abu||ozuteseli', 'ne'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateCompoundVepsNounWrongTemplate() {
        $template = "abu||ozuteseli|ne (";
        $lang_id = 1;
        $pos_id = 1; // noun
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [NULL, $num, 'abu||ozuteseli|ne (', NULL];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateKarNoun() {
        $template = "Kariel|a";
        $lang_id = 4;
        $pos_id = 1; // noun
        $num = 'sg';
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [[0=>'Kariela'], $num, 'Kariel', 'a'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateKarProperNounCompound() {
        $template = "abu||deng|u";
        $lang_id = 4;
        $pos_id = 1; // noun
        $num = null;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [[0=>'abudengu'], $num, 'abu||deng', 'u'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateOloNounCompound() {
        $template = "abu||deng|u";
        $lang_id = 5;
        $pos_id = 1; // noun
        $num = null;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [[0=>'abudengu'], $num, 'abu||deng', 'u'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateKarNounCompoundWrongTemplate() {
        $template = "abu||deng|u (-an,";
        $lang_id = 5;
        $pos_id = 1; // noun
        $num = null;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [NULL, $num, 'abu||deng|u (-an,', NULL];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateOloVerbWithApostroph() {
        $template = "kil’l’u|o (-n, -u; -tah; -i, -ttih)";
        $lang_id = 5;
        $pos_id = 11; 
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [[0=>'kil’l’uo',
                      1=>'kil’l’u',
                      2=>'kil’l’uu',
                      3=>'kil’l’u',
                      4=>'kil’l’ui',
                      5=>'kil’l’ui',
                      6=>'kil’l’uta',
                      7=>'kil’l’utt',
                      8=>'kil’l’u',
                      10=>TRUE
                        ], $num, 'kil’l’u', 'o'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateCompoundVepsVerb() {
        $template = "alle||kirjut|ada";
        $lang_id = 1;
        $pos_id = 11; 
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [[0=>'allekirjutada'], $num, 'alle||kirjut', 'ada'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateCompoundVepsVerbWrongTemplate() {
        $template = "alle||kirjut|ada (";
        $lang_id = 1;
        $pos_id = 11; 
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [NULL, $num, 'alle||kirjut|ada (', NULL];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateKarVerb() {
        $template = "alust|ua";
        $lang_id = 4;
        $pos_id = 11; 
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [[0=>'alustua'], $num, 'alust', 'ua'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateKarVerbCompound() {
        $template = "alle||kirjut|tua";
        $lang_id = 4;
        $pos_id = 11; 
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [[0=>'allekirjuttua'], $num, 'alle||kirjut', 'tua'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateOloVerbCompound() {
        $template = "alle||kirjut|tua";
        $lang_id = 5;
        $pos_id = 11; 
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [[0=>'allekirjuttua'], $num, 'alle||kirjut', 'tua'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateKarVerbCompoundWrongTemplate() {
        $template = "alle||kirjut|tua (";
        $lang_id = 5;
        $pos_id = 11; 
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [NULL, $num, 'alle||kirjut|tua (', NULL];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testToSearchFormDiakret() {
        $word = "t΄üuniśt΄üö";
        $word = Grammatic::phoneticsToLemma($word);
        $result = Grammatic::toSearchForm($word);
        $expected = "tüunistüö";
        $this->assertEquals( $expected, $result);        
    }
    
    public function testGetStemFromWordform()
    {
        $id=3539;
        $lemma = Lemma::findOrFail($id);        
        $base_n = 4;
        $dialect_id=44;
        $result = Grammatic::getStemFromWordform($lemma, $base_n, $lemma->lang_id,  $lemma->pos_id, $dialect_id, $lemma->features ? $lemma->features->is_reflexive : false);
        
        $expected = 'abaji/abajoi';
        $this->assertEquals( $expected, $result);        
    }
  
    public function testWordformsByStemsKarNameOloPezovezi() {
        $template = 'pezo||v|ezi (-ien, -etty; -ezii/-ezilöi)';
        $lang_id = 5;
        $pos_id = 5;
        $name_num=null;
        $dialect_id='44';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'pezovezi',  56=>'pezovezi, pezovien', 3=>'pezovien',  4=>'pezovetty', 
            277=>'pezovienny',  5=>'pezoviekse', 6=>'pezoviettäh', 8=>'pezovies',  
            9=>'pezovies, pezoviespäi', 10=>'pezovedeh',  11=>'pezoviel', 
            12=>'pezoviel, pezovielpäi', 13=>'pezoviele', 14=>'pezovienke', 
            15=>'pezovieči', 17=>'pezoviellyö', 16=>'pezovedessäh',
            
            2=>'pezoviet', 57=>'pezoviet', 24=>'pezovezien, pezovezilöin', 22=>'pezovezii, pezovezilöi', 
            279=>'pezovezilöinny, pezovezinny', 59=>'pezovezikse, pezovezilöikse', 
            64=>'pezovezilöittäh, pezovezittäh', 23=>'pezovezilöis, pezovezis', 
            60=>'pezovezilöis, pezovezilöispäi, pezovezis, pezovezispäi', 61=>'pezovezih, pezovezilöih',  
            25=>'pezovezil, pezovezilöil', 62=>'pezovezil, pezovezilpäi, pezovezilöil, pezovezilöilpäi', 
            63=>'pezovezile, pezovezilöile', 65=>'pezovezienke, pezovezinneh, pezovezilöinke, pezovezilöinneh', 
            66=>'pezovezilöiči, pezoveziči', 281=>'pezovezilöin, pezovezin', 
            18=>'pezoveziellyö, pezovezillyö, pezovezilöillyö', 67=>'pezovezilöissäh, pezovezissäh'];
        $this->assertEquals( $expected, $result);        
    }

    // 3. puoli
    public function testWordformsByStemsKarNameOloPuoli() {
        $template = 'puol|i (-en, -du; -ii)';
        $lang_id = 5;
        $pos_id = 5;
        $name_num=null;
        $dialect_id='44';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'puoli',  56=>'puolen, puoli', 3=>'puolen',  4=>'puoldu', 
            277=>'puolennu',  5=>'puolekse', 6=>'puolettah', 8=>'puoles',  
            9=>'puoles, puolespäi', 10=>'puoleh',  11=>'puolel', 12=>'puolel, puolelpäi', 
            13=>'puolele', 14=>'puolenke', 15=>'puoleči', 17=>'puolelluo', 16=>'puolessah',
            
            2=>'puolet', 57=>'puolet', 24=>'puolien', 22=>'puolii', 279=>'puolinnu', 
            59=>'puolikse', 64=>'puolittah', 23=>'puolis', 60=>'puolis, puolispäi', 
            61=>'puolih',  25=>'puolil', 62=>'puolil, puolilpäi', 63=>'puolile', 
            65=>'puolienke, puolinneh', 66=>'puoliči', 281=>'puolin', 18=>'puolielluo', 67=>'puolissah'];
        $this->assertEquals( $expected, $result);        
    }

    public function testWordformsFromMiniTemplateKarProperNameRanta() {
        $template = 'ran|ta [na]';
        $lang_id = 4;
        $pos_id = 5;
        $name_num=null;
        $dialect_id='46';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
//dd($stems, KarGram::countSyllable($stems[5]), KarGram::countSyllable($stems[6]));  
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'ranta',  56=>'rannan, ranta',  3=>'rannan',  4=>'rantua', 277=>'rantana',  
                     5=>'rannakši', 8=>'rannašša',  9=>'rannašta', 10=>'rantah', 278=>'rannalla', 
                     12=>'rannalta', 6=>'rannatta', 14=>'rantoineh', 15=>'', 
 
            2=>'rannat', 57=>'rannat', 24=>'rantojen', 22=>'rantoja', 279=>'rantoina', 
            59=>'rannoiksi', 23=>'rannoissa', 60=>'rannoista', 61=>'rantoih', 280=>'rannoilla', 
            62=>'rannoilta', 64=>'rannoitta', 65=>'rantoineh', 66=>'', 281=>'rannoin', 17=>'', 18=>''];
        $this->assertEquals( $expected, $result);        
    }

    public function testWordformsFromMiniTemplateKarOloNameRandu() {
        $template = 'ran|du [na]';
        $lang_id = 5;
        $pos_id = 5;
        $name_num=null;
        $dialect_id='44';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'randu',  56=>'randu, rannan', 3=>'rannan',  4=>'randua', 277=>'rannannu',  
                     5=>'rannakse', 6=>'rannattah', 8=>'rannas',  9=>'rannas, rannaspäi', 10=>'randah',  
                     11=>'rannal', 12=>'rannal, rannalpäi', 13=>'rannale', 14=>'rannanke', 15=>'rannači', 17=>'rannalluo', 16=>'randassah',
 
            2=>'rannat', 57=>'rannat', 24=>'rannoin', 22=>'randoi', 279=>'rannoinnu', 
            59=>'rannoikse', 64=>'rannoittah', 23=>'rannois', 60=>'rannois, rannoispäi', 61=>'randoih',  
            25=>'rannoil', 62=>'rannoil, rannoilpäi', 63=>'rannoile', 65=>'rannoinke, rannoinneh', 
            66=>'rannoiči', 281=>'rannoin', 18=>'rannoilluo', 67=>'randoissah'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformsFromMiniTemplateKarProperNamePelto() {
        $template = 'pel|to [lo]';
        $lang_id = 4;
        $pos_id = 5;
        $name_num=null;
        $dialect_id='46';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'pelto',  56=>'pellon, pelto',  3=>'pellon',  4=>'peltuo', 277=>'peltona',  
                     5=>'pellokši', 8=>'pellošša',  9=>'pellošta', 10=>'peltoh', 278=>'pellolla', 
                     12=>'pellolta', 6=>'pellotta', 14=>'peltoloineh', 15=>'', 
 
            2=>'pellot', 57=>'pellot', 24=>'peltojen', 22=>'peltoja', 279=>'peltoloina', 
            59=>'peltoloiksi', 23=>'peltoloissa', 60=>'peltoloista', 61=>'peltoloih', 280=>'peltoloilla', 
            62=>'peltoloilta', 64=>'peltoloitta', 65=>'peltoloineh', 66=>'', 281=>'peltoloin', 17=>'', 18=>''];
        $this->assertEquals( $expected, $result);        
    }

    public function testWordformsFromMiniTemplateKarOloNamePeldo() {
        $template = 'pel|do [lo]';
        $lang_id = 5;
        $pos_id = 5;
        $name_num=null;
        $dialect_id='44';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'peldo',  56=>'peldo, pellon', 3=>'pellon',  4=>'pelduo', 277=>'pellonnu',  
                     5=>'pellokse', 6=>'pellottah', 8=>'pellos',  9=>'pellos, pellospäi', 10=>'peldoh',  
                     11=>'pellol', 12=>'pellol, pellolpäi', 13=>'pellole', 14=>'pellonke', 15=>'pelloči', 17=>'pellolluo', 16=>'peldossah',
 
            2=>'pellot', 57=>'pellot', 24=>'peldoloin', 22=>'peldoloi', 279=>'peldoloinnu', 
            59=>'peldoloikse', 64=>'peldoloittah', 23=>'peldolois', 60=>'peldolois, peldoloispäi', 61=>'peldoloih',  
            25=>'peldoloil', 62=>'peldoloil, peldoloilpäi', 63=>'peldoloile', 
            65=>'peldoloinke, peldoloinneh', 66=>'peldoloiči', 281=>'peldoloin', 18=>'peldoloilluo', 67=>'peldoloissah'];
        $this->assertEquals( $expected, $result);        
    }

    public function testWordformsFromMiniTemplateKarProperNameMua() {
        $template = 'mua []';
        $lang_id = 4;
        $pos_id = 5;
        $name_num=null;
        $dialect_id='46';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'mua',  56=>'mua, muan',  3=>'muan',  4=>'muata', 277=>'muana',  
                     5=>'muakši', 8=>'muašša',  9=>'muašta', 10=>'muah', 278=>'mualla', 
                     12=>'mualta', 6=>'muatta', 14=>'maineh', 15=>'', 
 
            2=>'muat', 57=>'muat', 24=>'maijen', 22=>'maita', 279=>'maina', 
            59=>'maiksi', 23=>'maissa', 60=>'maista', 61=>'maih', 280=>'mailla', 
            62=>'mailta', 64=>'maitta', 65=>'maineh', 66=>'', 281=>'main', 17=>'', 18=>''];
        $this->assertEquals( $expected, $result);        
    }

    public function testWordformsFromMiniTemplateKarOloNameMua() {
        $template = 'mua []';
        $lang_id = 5;
        $pos_id = 5;
        $name_num=null;
        $dialect_id='44';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'mua',  56=>'mua, muan', 3=>'muan',  4=>'muadu', 277=>'muannu',  
                     5=>'muakse', 6=>'muattah', 8=>'muas',  9=>'muas, muaspäi', 10=>'muah',  
                     11=>'mual', 12=>'mual, mualpäi', 13=>'muale', 14=>'muanke', 15=>'muači', 17=>'mualluo', 16=>'muassah',
 
            2=>'muat', 57=>'muat', 24=>'mualoin', 22=>'mualoi', 279=>'mualoinnu', 
            59=>'mualoikse', 64=>'mualoittah', 23=>'mualois', 60=>'mualois, mualoispäi', 61=>'mualoih',  
            25=>'mualoil', 62=>'mualoil, mualoilpäi', 63=>'mualoile', 65=>'mualoinke, mualoinneh', 
            66=>'mualoiči', 281=>'mualoin', 18=>'mualoilluo', 67=>'mualoissah'];
        $this->assertEquals( $expected, $result);        
    }

    public function testWordformsFromMiniTemplateKarProperNameNuori() {
        $template = 'nuor|i [e, ]';
        $lang_id = 4;
        $pos_id = 1;
        $name_num=null;
        $dialect_id='46';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
//dd($stems);        
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'nuori',  56=>'nuoren, nuori',  3=>'nuoren',  4=>'nuorta', 277=>'nuorena',  
                     5=>'nuorekši', 8=>'nuorešša',  9=>'nuorešta', 10=>'nuoreh', 278=>'nuorella', 
                     12=>'nuorelta', 6=>'nuoretta', 14=>'nuorineh', 15=>'', 
 
            2=>'nuoret', 57=>'nuoret', 24=>'nuorien', 22=>'nuorie', 279=>'nuorina', 
            59=>'nuoriksi', 23=>'nuorissa', 60=>'nuorista', 61=>'nuorih', 280=>'nuorilla', 
            62=>'nuorilta', 64=>'nuoritta', 65=>'nuorineh', 66=>'', 281=>'nuorin', 17=>'', 18=>''];
        $this->assertEquals( $expected, $result);        
    }

    public function testWordformsFromMiniTemplateKarOloNameNuori() {
        $template = 'nuor|i [e, ]';
        $lang_id = 5;
        $pos_id = 1;
        $name_num=null;
        $dialect_id='44';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'nuori',  56=>'nuoren, nuori', 3=>'nuoren',  4=>'nuordu', 277=>'nuorennu',  
                     5=>'nuorekse', 6=>'nuorettah', 8=>'nuores',  9=>'nuores, nuorespäi', 10=>'nuoreh',  
                     11=>'nuorel', 12=>'nuorel, nuorelpäi', 13=>'nuorele', 14=>'nuorenke', 15=>'nuoreči', 17=>'nuorelluo', 16=>'nuoressah',
 
            2=>'nuoret', 57=>'nuoret', 24=>'nuorien', 22=>'nuorii', 279=>'nuorinnu', 
            59=>'nuorikse', 64=>'nuorittah', 23=>'nuoris', 60=>'nuoris, nuorispäi', 61=>'nuorih',  
            25=>'nuoril', 62=>'nuoril, nuorilpäi', 63=>'nuorile', 65=>'nuorienke, nuorinneh', 66=>'nuoriči', 281=>'nuorin', 18=>'nuorielluo', 67=>'nuorissah'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformsFromMiniTemplateKarProperNameLyhyt() {
        $template = 'lyhy|t [ö, t]';
        $lang_id = 4;
        $pos_id = 5;
        $name_num=null;
        $dialect_id='46';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'lyhyt', 56=>'lyhyt, lyhyön', 3=>'lyhyön',  4=>'lyhyttä', 277=>'lyhyönä',  
                     5=>'lyhyökši', 8=>'lyhyöššä',  9=>'lyhyöštä', 10=>'lyhyöh', 278=>'lyhyöllä', 
                     12=>'lyhyöltä', 6=>'lyhyöttä', 14=>'lyhyineh', 15=>'', 
 
            2=>'lyhyöt', 57=>'lyhyöt', 24=>'lyhyijen', 22=>'lyhyitä', 279=>'lyhyinä', 
            59=>'lyhyiksi', 23=>'lyhyissä', 60=>'lyhyistä', 61=>'lyhyih', 280=>'lyhyillä', 
            62=>'lyhyiltä', 64=>'lyhyittä', 65=>'lyhyineh', 66=>'', 281=>'lyhyin', 17=>'', 18=>''];
        $this->assertEquals( $expected, $result);        
    }

    public function testWordformsFromMiniTemplateKarOloNameLyhyt() {
        $template = 'lyhy|t [ö, t]';
        $lang_id = 5;
        $pos_id = 5;
        $name_num=null;
        $dialect_id=44;
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'lyhyt', 56=>'lyhyt, lyhyön', 3=>'lyhyön',  4=>'lyhytty', 277=>'lyhyönny',  
                     5=>'lyhyökse', 6=>'lyhyöttäh', 8=>'lyhyös',  9=>'lyhyös, lyhyöspäi', 10=>'lyhyöh',  
                     11=>'lyhyöl', 12=>'lyhyöl, lyhyölpäi', 13=>'lyhyöle', 14=>'lyhyönke', 15=>'lyhyöči', 17=>'lyhyöllyö', 16=>'lyhyössäh',
 
            2=>'lyhyöt', 57=>'lyhyöt', 24=>'lyhyzien', 22=>'lyhyzii', 279=>'lyhyzinny', 
            59=>'lyhyzikse', 64=>'lyhyzittäh', 23=>'lyhyzis', 60=>'lyhyzis, lyhyzispäi', 61=>'lyhyzih',  
            25=>'lyhyzil', 62=>'lyhyzil, lyhyzilpäi', 63=>'lyhyzile', 
            65=>'lyhyzienke, lyhyzinneh', 66=>'lyhyziči', 281=>'lyhyzin', 18=>'lyhyziellyö', 67=>'lyhyzissäh'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformsFromMiniTemplateKarProperNameVesi() {
        $template = 've|si [je/te, t]';
        $lang_id = 4;
        $pos_id = 5;
        $name_num=null;
        $dialect_id='46';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'vesi', 56=>'vejen, vesi', 3=>'vejen',  4=>'vettä', 277=>'vetenä',  
                     5=>'vejekši', 8=>'veješšä',  9=>'veještä', 10=>'veteh', 278=>'vejellä', 
                     12=>'vejeltä', 6=>'vejettä', 14=>'vesineh', 15=>'', 
 
            2=>'vejet', 57=>'vejet', 24=>'vesien', 22=>'vesie', 279=>'vesinä', 
            59=>'vesiksi', 23=>'vesissä', 60=>'vesistä', 61=>'vesih', 280=>'vesillä', 
            62=>'vesiltä', 64=>'vesittä', 65=>'vesineh', 66=>'', 281=>'vesin', 17=>'', 18=>''];
        $this->assertEquals( $expected, $result);        
    }

    public function testWordformsFromMiniTemplateKarOloNameVezi() {
        $template = 'v|ezi [ie/ede, et]';
        $lang_id = 5;
        $pos_id = 5;
        $name_num=null;
        $dialect_id='44';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'vezi',  56=>'vezi, vien', 3=>'vien',  4=>'vetty', 277=>'vienny',  
                     5=>'viekse', 6=>'viettäh', 8=>'vies',  9=>'vies, viespäi', 10=>'vedeh',  
                     11=>'viel', 12=>'viel, vielpäi', 13=>'viele', 14=>'vienke', 15=>'vieči', 17=>'viellyö', 16=>'vedessäh',
 
            2=>'viet', 57=>'viet', 24=>'vezien', 22=>'vezii', 279=>'vezinny', 
            59=>'vezikse', 64=>'vezittäh', 23=>'vezis', 60=>'vezis, vezispäi', 61=>'vezih',  
            25=>'vezil', 62=>'vezil, vezilpäi', 63=>'vezile', 
            65=>'vezienke, vezinneh', 66=>'veziči', 281=>'vezin', 18=>'veziellyö', 67=>'vezissäh'];
        $this->assertEquals( $expected, $result);        
    }
    
    // 17. puhketa
    public function testWordformsByStemsKarVerbOloPuhketa() {
        $template = 'puhk|eta (-ien/-enen, -ieu/-enou; -etah; -ei/-eni, -ettih)';
        $lang_id = 5;
        $pos_id = 11;
        $name_num=null;
        $dialect_id='';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [26 => 'puhkien, puhkenen',   27 => 'puhkiet, puhkenet',  28 => 'puhkieu, puhkenou',  
                29 => 'puhkiemmo, puhkenemmo',  30 => 'puhkietto, puhkenetto',  31 => 'puhketah', 295 => 'puhkie, puhkene', 296 => 'puhketa', 
                70 => 'en puhkie, en puhkene',   71 => 'et puhkie, et puhkene',  72 => 'ei puhkie, ei puhkene',  
                73 => 'emmo puhkie, emmo puhkene',  78 => 'etto puhkie, etto puhkene',  79 => 'ei puhketa', 
                32 => 'puhkein, puhkenin',   33 => 'puhkeit, puhkenit',  34 => 'puhkei, puhkeni',  
                35 => 'puhkeimmo, puhkenimmo',  36 => 'puhkeitto, puhkenitto',  37 => 'puhkettih', 
                80 => 'en puhkennuh',   81 => 'et puhkennuh',  82 => 'ei puhkennuh',  
                83 => 'emmo puhkennuh',  84 => 'etto puhkennuh',  85 => 'ei puhkettu', 
                86 => 'olen puhkennuh',   87 => 'olet puhkennuh',  88 => 'on puhkennuh',  89 => 'olemmo puhkennuh',  90 => 'oletto puhkennuh',  91 => 'ollah puhkettu, on puhkettu',  
                92 => 'en ole puhkennuh',  93 => 'et ole puhkennuh',  94 => 'ei ole puhkennuh',  95 => 'emmo ole puhkennuh',  96 => 'etto ole puhkennuh',  97 => 'ei olla puhkettu',
                98 => 'olin puhkennuh',   99 => 'olit puhkennuh', 100 => 'oli puhkennuh', 101 => 'olimmo puhkennuh', 102 => 'olitto puhkennuh', 103 => 'oldih puhkettu, oli puhkettu', 
                104 => 'en olluh puhkennuh', 105 => 'et olluh puhkennuh', 107 => 'ei olluh puhkennuh', 108 => 'emmo olluh puhkennuh', 106 => 'etto olluh puhkennuh', 109 => 'ei oldu puhkettu',
                      51 => 'puhkie, puhkene',  52 => 'puhkekkah',  53 => 'puhkekkuammo',  54 => 'puhkekkua, puhkekkuatto',  55 => 'puhkettahes',       
                      50 => 'älä puhkie, älä puhkene',  74 => 'älgäh puhkekkah',  75 => 'älgiämmö puhkekkuammo',  76 => 'älgiä puhkekkua',  77 => 'äldähes puhkettahes',  
                38 => 'puhkiezin, puhkenizin',   39 => 'puhkiezit, puhkenizit',  40 => 'puhkies, puhkenis',  
                41 => 'puhkiezimmo, puhkenizimmo',  42 => 'puhkiezitto, puhkenizitto',  43 => 'puhkettas', 
                110 => 'en puhkies, en puhkenis', 111 => 'et puhkies, et puhkenis', 112 => 'ei puhkies, ei puhkenis', 
                113 => 'emmo puhkies, emmo puhkenis', 114 => 'etto puhkies, etto puhkenis', 115 => 'ei puhkettas',
                44 => 'puhkennuzin',   45 => 'puhkennuzit',  46 => 'puhkennus',  47 => 'puhkennuzimmo',  48 => 'puhkennuzitto',  49 => 'puhketannus', 
                116 => 'en puhkennus', 117 => 'et puhkennus', 118 => 'ei puhkennus', 119 => 'emmo puhkennus', 120 => 'etto puhkennus', 121 => 'ei puhketannus',
                122 => 'olizin puhkennuh', 123 => 'olizit puhkennuh', 124 => 'olis puhkennuh', 126 => 'olizimmo puhkennuh', 127 => 'olizitto puhkennuh', 128 => 'oldas puhkettu', 
                129 => 'en olis puhkennuh', 130 => 'et olis puhkennuh', 131 => 'ei olis puhkennuh', 132 => 'emmo olis puhkennuh', 133 => 'etto olis puhkennuh', 134 => 'ei oldas puhkettu',
                135 => 'olluzin puhkennuh', 125 => 'olluzit puhkennuh', 136 => 'ollus puhkennuh', 137 => 'olluzimmo puhkennuh', 138 => 'olluzitto puhkennuh', 139 => 'oldanus puhkettu', 
                140 => 'en ollus puhkennuh', 141 => 'et ollus puhkennuh', 142 => 'ei ollus puhkennuh', 143 => 'emmo ollus puhkennuh', 144 => 'etto ollus puhkennuh', 145 => 'ei oldanus puhkettu',
                146 => 'puhkennen', 147 => 'puhkennet', 148 => 'puhkennou', 149 => 'puhkennemmo', 150 => 'puhkennetto', 151 => 'puhketanneh', 
                152 => 'en puhkenne', 153 => 'et puhkenne', 154 => 'ei puhkenne', 155 => 'emmo puhkenne', 156 => 'etto puhkenne', 157 => 'ei puhketanne',
                158 => 'ollen puhkennuh', 159 => 'ollet puhkennuh', 160 => 'ollou puhkennuh', 161 => 'ollemmo puhkennuh', 162 => 'olletto puhkennuh', 163 => 'oldaneh puhkettu', 
                164 => 'en olle puhkennuh', 165 => 'et olle puhkennuh', 166 => 'ei olle puhkennuh', 167 => 'emmo olle puhkennuh', 168 => 'etto olle puhkennuh', 169 => 'ei oldane puhkettu',
                170 => 'puhketa', 171 => 'puhketes', 172 => 'puhketen', 173 => 'puhkiemal, puhkenemal', 174 => 'puhkiemah, puhkenemah', 175 => 'puhkiemas, puhkenemas', 176 => 'puhkiemaspäi, puhkenemaspäi', 177 => 'puhkiemattah, puhkenemattah', 312 => 'puhkiemua, puhkenemua',
                178 => 'puhkieju, puhkenii, puhkeniju', 179 => 'puhkennuh', 180 => 'puhkettavu', 181 => 'puhkettu'];
//        $slice = 50;
//        $this->assertEquals(array_slice($expected, 0, $slice, true), array_slice($result, 0, $slice, true));        
        $this->assertEquals( $expected, $result);        
    }
    
    // Ref: 1. pačkahtellakseh
    public function testWordformsByStemsKarVerbOloRefPackahtellakseh() {
        $template = 'pačkahtel|lakseh (-emmos, -eh/-ehes; -lаhes; -ih/-ihes, -tihes)';
        $lang_id = 5;
        $pos_id = 11;
        $name_num=null;
        $dialect_id='';
        $is_reflexive = true;
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id, $is_reflexive);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems, $is_reflexive);
        $expected = [26 => 'pačkahtelemmos',   27 => 'pačkahtelettos',  28 => 'pačkahteleh, pačkahtelehes',  
                29 => 'pačkahtelemmokseh',  30 => 'pačkahtelettokseh',  31 => 'pačkahtellаhes', 295 => 'pačkahtelei', 296 => 'pačkahtellаhes', 
                70 => 'en pačkahtelei',   71 => 'et pačkahtelei',  72 => 'ei pačkahtelei',  
                73 => 'emmo pačkahtelei',  78 => 'etto pačkahtelei',  79 => 'ei pačkahtellаhes', 
            
                32 => 'pačkahtelimmos',   33 => 'pačkahtelittos',  34 => 'pačkahtelih, pačkahtelihes',  
                35 => 'pačkahtelimmokseh',  36 => 'pačkahtelittokseh',  37 => 'pačkahteltihes', 
                80 => 'en pačkahtelluhes',   81 => 'et pačkahtelluhes',  82 => 'ei pačkahtelluhes',  
                83 => 'emmo pačkahtelluhes',  84 => 'etto pačkahtelluhes',  85 => 'ei pačkahteltuhes', 
            
                86 => 'olen pačkahtelluhes',   87 => 'olet pačkahtelluhes',  88 => 'on pačkahtelluhes',  
                89 => 'olemmo pačkahtelluhes',  90 => 'oletto pačkahtelluhes',  91 => 'ollah pačkahteltuhes, on pačkahteltuhes',  
                92 => 'en ole pačkahtelluhes',  93 => 'et ole pačkahtelluhes',  94 => 'ei ole pačkahtelluhes',  95 => 'emmo ole pačkahtelluhes',  
                96 => 'etto ole pačkahtelluhes',  97 => 'ei olla pačkahteltuhes',
                98 => 'olin pačkahtelluhes',   99 => 'olit pačkahtelluhes', 100 => 'oli pačkahtelluhes', 
                101 => 'olimmo pačkahtelluhes', 102 => 'olitto pačkahtelluhes', 103 => 'oldih pačkahteltuhes, oli pačkahteltuhes', 
                104 => 'en olluh pačkahtelluhes', 105 => 'et olluh pačkahtelluhes', 107 => 'ei olluh pačkahtelluhes', 
                108 => 'emmo olluh pačkahtelluhes', 106 => 'etto olluh pačkahtelluhes', 109 => 'ei oldu pačkahteltuhes',
            
                      51 => 'pačkahtelei',  52 => 'pačkahtelkahes',  
                53 => 'pačkahtelkuammokseh',  54 => 'pačkahtelkuattokseh',  55 => 'pačkahteltahes',       
                      50 => 'älä pačkahtelei',  74 => 'älgäh pačkahtelkahes',  
                      75 => '',  76 => 'älgiä pačkahtelkuattokseh',  77 => 'äldähes pačkahteltahes',  
            
                38 => 'pačkahtelizimmos',   39 => 'pačkahtelizittos',  40 => 'pačkahtelizihes',  
                41 => 'pačkahtelizimmokseh',  42 => 'pačkahtelizittokseh',  43 => 'pačkahteltazihes', 
                110 => 'en pačkahtelizihes', 111 => 'et pačkahtelizihes', 112 => 'ei pačkahtelizihes', 
                113 => 'emmo pačkahtelizihes', 114 => 'etto pačkahtelizihes', 115 => 'ei pačkahteltazihes',
            
                44 => 'pačkahtelluzimmos',   45 => 'pačkahtelluzittos',  46 => 'pačkahtelluzihes',  
                47 => 'pačkahtelluzimmokseh',  48 => 'pačkahtelluzittokseh',  49 => 'pačkahteltanuzihes', 
                116 => 'en pačkahtelluzihes', 117 => 'et pačkahtelluzihes', 118 => 'ei pačkahtelluzihes', 
                119 => 'emmo pačkahtelluzihes', 120 => 'etto pačkahtelluzihes', 121 => 'ei pačkahteltanuzihes',
            
                122 => 'olizin pačkahtelluhes', 123 => 'olizit pačkahtelluhes', 124 => 'olis pačkahtelluhes', 
                126 => 'olizimmo pačkahtelluhes', 127 => 'olizitto pačkahtelluhes', 128 => 'oldas pačkahteltuhes', 
                129 => 'en olis pačkahtelluhes', 130 => 'et olis pačkahtelluhes', 131 => 'ei olis pačkahtelluhes', 
                132 => 'emmo olis pačkahtelluhes', 133 => 'etto olis pačkahtelluhes', 134 => 'ei oldas pačkahteltuhes',
            
                135 => 'olluzin pačkahtelluhes', 125 => 'olluzit pačkahtelluhes', 136 => 'ollus pačkahtelluhes', 
                137 => 'olluzimmo pačkahtelluhes', 138 => 'olluzitto pačkahtelluhes', 139 => 'oldanus pačkahteltuhes', 
                140 => 'en ollus pačkahtelluhes', 141 => 'et ollus pačkahtelluhes', 142 => 'ei ollus pačkahtelluhes', 
                143 => 'emmo ollus pačkahtelluhes', 144 => 'etto ollus pačkahtelluhes', 145 => 'ei oldanus pačkahteltuhes',

                146 => 'pačkahtellemmos', 147 => 'pačkahtellettos', 148 => 'pačkahtellehes', 
                149 => 'pačkahtellemmokseh', 150 => 'pačkahtellettokseh', 151 => 'pačkahteltanehes', 
                152 => 'en pačkahtellei', 153 => 'et pačkahtellei', 154 => 'ei pačkahtellei', 
                155 => 'emmo pačkahtellei', 156 => 'etto pačkahtellei', 157 => 'ei pačkahteltanehes',
            
                158 => 'ollen pačkahtelluhes', 159 => 'ollet pačkahtelluhes', 160 => 'ollou pačkahtelluhes', 
                161 => 'ollemmo pačkahtelluhes', 162 => 'olletto pačkahtelluhes', 163 => 'oldaneh pačkahteltuhes', 
                164 => 'en olle pačkahtelluhes', 165 => 'et olle pačkahtelluhes', 166 => 'ei olle pačkahtelluhes', 
                167 => 'emmo olle pačkahtelluhes', 168 => 'etto olle pačkahtelluhes', 169 => 'ei oldane pačkahteltuhes',
            
                170 => 'pačkahtellakseh', 171 => '', 172 => '', 
                173 => '', 174 => '', 175 => '', 
                176 => '', 177 => '', 312 => '',
            
                178 => '', 179 => 'pačkahtelluhes', 180 => '', 181 => 'pačkahteltuhes'];
        $this->assertEquals( $expected, $result);        
    }
  
    // Ref&Def 4. pakastuakseh
    public function testWordformsByStemsKarVerbOloRefDef() {
        $template = 'pakast|uakseh (-ah/-ahes; -ih/-ihes)';
        $lang_id = 5;
        $pos_id = 11;
        $name_num=null;
        $dialect_id='';
        $is_reflexive = true;
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id, $is_reflexive);
//dd("\nname_num:".$name_num."\n");        
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems, $is_reflexive);
        $expected = [26 => '',   27 => '',  28 => 'pakastah, pakastahes',  29 => '',  30 => '',  31 => '', 
                295 => 'pakastai', 296 => '', 
//                295 => '', 296 => '', 
                70 => '',   71 => '',  72 => 'ei pakastai',  73 => '',  78 => '',  79 => '', 
            
                32 => '',   33 => '',  34 => 'pakastih, pakastihes',  
                35 => '',  36 => '',  37 => '', 
                80 => '',   81 => '',  82 => 'ei pakastannuhes',  
                83 => '',  84 => '',  85 => '', 
//                80 => '',   81 => '',  82 => '',  83 => '',  84 => '',  85 => '', 
            
                86 => '',   87 => '',  88 => 'on pakastannuhes',  
                89 => '',  90 => '',  91 => '',  
                92 => '',  93 => '',  94 => 'ei ole pakastannuhes',  
                95 => '',  96 => '',  97 => '',
//                86 => '',   87 => '',  88 => '',  89 => '',  90 => '',  91 => '',  92 => '',  93 => '',  94 => '',  95 => '',  96 => '',  97 => '',
            
                98 => '',   99 => '', 100 => 'oli pakastannuhes', 
                101 => '', 102 => '', 103 => '', 
                104 => '', 105 => '', 107 => 'ei olluh pakastannuhes', 
                108 => '', 106 => '', 109 => '',
//                98 => '',   99 => '', 100 => '', 101 => '', 102 => '', 103 => '', 104 => '', 105 => '', 107 => '', 108 => '', 106 => '', 109 => '',
                      51 => '',  52 => 'pakastakkahes',  53 => '',  54 => '',  55 => '',       
                      50 => '',  74 => 'älgäh pakastakkahes',  75 => '',  76 => '',  77 => '',  
            
                38 => '',   39 => '',  40 => 'pakastazihes',  
                41 => '',  42 => '',  43 => '', 
                110 => '', 111 => '', 112 => 'ei pakastazihes', 
                113 => '', 114 => '', 115 => '',

                44 => '',   45 => '',  46 => 'pakastannuzihes',  
                47 => '',  48 => '',  49 => '', 
                116 => '', 117 => '', 118 => 'ei pakastannuzihes', 
                119 => '', 120 => '', 121 => '',
//                44 => '',   45 => '',  46 => '',  47 => '',  48 => '',  49 => '', 116 => '', 117 => '', 118 => '', 119 => '', 120 => '', 121 => '',
            
                122 => '', 123 => '', 124 => 'olis pakastannuhes', 
                126 => '', 127 => '', 128 => '', 
                129 => '', 130 => '', 131 => 'ei olis pakastannuhes', 
                132 => '', 133 => '', 134 => '',
//                122 => '', 123 => '', 124 => '', 126 => '', 127 => '', 128 => '', 129 => '', 130 => '', 131 => '', 132 => '', 133 => '', 134 => '',
            
                135 => '', 125 => '', 136 => 'ollus pakastannuhes', 
                137 => '', 138 => '', 139 => '', 
                140 => '', 141 => '', 142 => 'ei ollus pakastannuhes', 
                143 => '', 144 => '', 145 => '',
//                135 => '', 125 => '', 136 => '', 137 => '', 138 => '', 139 => '', 140 => '', 141 => '', 142 => '', 143 => '', 144 => '', 145 => '',
            
                146 => '', 147 => '', 148 => 'pakastannehes', 
                149 => '', 150 => '', 151 => '', 
                152 => '', 153 => '', 154 => 'ei pakastannei', 
                155 => '', 156 => '', 157 => '',
//                146 => '', 147 => '', 148 => '', 149 => '', 150 => '', 151 => '', 152 => '', 153 => '', 154 => '', 155 => '', 156 => '', 157 => '',
            
                158 => '', 159 => '', 160 => 'ollou pakastannuhes', 
                161 => '', 162 => '', 163 => '', 
                164 => '', 165 => '', 166 => 'ei olle pakastannuhes', 
                167 => '', 168 => '', 169 => '',
//                158 => '', 159 => '', 160 => '', 161 => '', 162 => '', 163 => '', 164 => '', 165 => '', 166 => '', 167 => '', 168 => '', 169 => '',
            
                170 => 'pakastuakseh', 171 => '', 172 => '', 173 => '', 174 => '', 175 => '', 176 => '', 177 => '', 312 => '',
                178 => '', 179 => 'pakastannuhes', 180 => '', 181 => ''];
        $this->assertEquals( $expected, $result);        
    }

    // Def: 1. bluaznittua
    public function testWordformsByStemsKarVerbOloBluaznittua() {
        $template = 'bluaznit|tua (-tau; -ti)';
        $lang_id = 5;
        $pos_id = 11;
        $name_num=null;
        $dialect_id='';
        $is_reflexive = false;
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id, $is_reflexive);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [26 => '',   27 => '',  28 => 'bluaznittau',  
                29 => '',  30 => '',  31 => '', 295 => 'bluaznita', 296 => '', 
                70 => '',   71 => '',  72 => 'ei bluaznita',  
                73 => '',  78 => '',  79 => '', 
            
                32 => '',   33 => '',  34 => 'bluaznitti',  
                35 => '',  36 => '',  37 => '', 
                80 => '',   81 => '',  82 => 'ei bluaznitannuh',  
                83 => '',  84 => '',  85 => '', 
            
                86 => '',   87 => '',  88 => 'on bluaznitannuh',  
                89 => '',  90 => '',  91 => '',  
                92 => '',  93 => '',  94 => 'ei ole bluaznitannuh',  
                95 => '',  96 => '',  97 => '',
            
                98 => '',   99 => '', 100 => 'oli bluaznitannuh', 
                101 => '', 102 => '', 103 => '', 
                104 => '', 105 => '', 107 => 'ei olluh bluaznitannuh', 
                108 => '', 106 => '', 109 => '',
            
                      51 => '',  52 => 'bluaznittakkah',  
                53 => '',  54 => '',  55 => '',       
                      50 => '',  74 => 'älgäh bluaznittakkah',  
                75 => '',  76 => '',  77 => '',  
            
                38 => '',   39 => '',  40 => 'bluaznittas',  
                41 => '',  42 => '',  43 => '', 
                110 => '', 111 => '', 112 => 'ei bluaznittas', 
                113 => '', 114 => '', 115 => '',

                44 => '',   45 => '',  46 => 'bluaznitannus',  
                47 => '',  48 => '',  49 => '', 
                116 => '', 117 => '', 118 => 'ei bluaznitannus', 
                119 => '', 120 => '', 121 => '',
            
                122 => '', 123 => '', 124 => 'olis bluaznitannuh', 
                126 => '', 127 => '', 128 => '', 
                129 => '', 130 => '', 131 => 'ei olis bluaznitannuh', 
                132 => '', 133 => '', 134 => '',
            
                135 => '', 125 => '', 136 => 'ollus bluaznitannuh', 
                137 => '', 138 => '', 139 => '', 
                140 => '', 141 => '', 142 => 'ei ollus bluaznitannuh', 
                143 => '', 144 => '', 145 => '',
            
                146 => '', 147 => '', 148 => 'bluaznitannou', 
                149 => '', 150 => '', 151 => '', 
                152 => '', 153 => '', 154 => 'ei bluaznitanne', 
                155 => '', 156 => '', 157 => '',
            
                158 => '', 159 => '', 160 => 'ollou bluaznitannuh', 
                161 => '', 162 => '', 163 => '', 
                164 => '', 165 => '', 166 => 'ei olle bluaznitannuh', 
                167 => '', 168 => '', 169 => '',
            
                170 => 'bluaznittua', 171 => '', 172 => '', 
                173 => '', 174 => '', 175 => '', 
                176 => '', 177 => '', 312 => '',
            
//                178 => 'bluaznitai, bluaznittaju', 179 => 'bluaznitannuh', 180 => '', 181 => ''];
                178 => '', 179 => 'bluaznitannuh', 180 => '', 181 => ''];
        $this->assertEquals( $expected, $result);        
    }
    
    // Ref: 6. palkatakseh
    public function testWordformsByStemsKarVerbOloRefPalkatakseh() {
        $template = 'palk|atakseh (-uammos, -uahes; -atahes; -aihes, -attihes)';
        $lang_id = 5;
        $pos_id = 11;
        $name_num=null;
        $dialect_id='';
        $is_reflexive = true;
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id, $is_reflexive);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems, $is_reflexive);
        $expected = [26 => 'palkuammos',   27 => 'palkuattos',  28 => 'palkuahes',  
                29 => 'palkuammokseh',  30 => 'palkuattokseh',  31 => 'palkatahes', 
                295 => 'palkai', 296 => 'palkatahes', 
//                295 => 'palkai, palkaihes', 296 => 'palkatahes', 
                70 => 'en palkai',   71 => 'et palkai',  72 => 'ei palkai',  
                73 => 'emmo palkai',  78 => 'etto palkai',  79 => 'ei palkatahes', 
//                70 => 'en palkai, en palkaihes',   71 => 'et palkai, et palkaihes',  72 => 'ei palkai, ei palkaihes',  
//                73 => 'emmo palkai, emmo palkaihes',  78 => 'etto palkai, etto palkaihes',  79 => 'ei palkatahes', 
            
                32 => 'palkaimmos',   33 => 'palkaittos',  34 => 'palkaihes',  
                35 => 'palkaimmokseh',  36 => 'palkaittokseh',  37 => 'palkattihes', 
                80 => 'en palkannuhes',   81 => 'et palkannuhes',  82 => 'ei palkannuhes',  
                83 => 'emmo palkannuhes',  84 => 'etto palkannuhes',  85 => 'ei palkattuhes', 
            
                86 => 'olen palkannuhes',   87 => 'olet palkannuhes',  88 => 'on palkannuhes',  
                89 => 'olemmo palkannuhes',  90 => 'oletto palkannuhes',  91 => 'ollah palkattuhes, on palkattuhes',  
                92 => 'en ole palkannuhes',  93 => 'et ole palkannuhes',  94 => 'ei ole palkannuhes',  
                95 => 'emmo ole palkannuhes',  96 => 'etto ole palkannuhes',  97 => 'ei olla palkattuhes',
            
                98 => 'olin palkannuhes',   99 => 'olit palkannuhes', 100 => 'oli palkannuhes', 
                101 => 'olimmo palkannuhes', 102 => 'olitto palkannuhes', 103 => 'oldih palkattuhes, oli palkattuhes', 
                104 => 'en olluh palkannuhes', 105 => 'et olluh palkannuhes', 107 => 'ei olluh palkannuhes', 
                108 => 'emmo olluh palkannuhes', 106 => 'etto olluh palkannuhes', 109 => 'ei oldu palkattuhes',
            
                      51 => 'palkai',  52 => 'palkakkahes',  
                53 => 'palkakkuammokseh',  54 => 'palkakkuattokseh',  55 => 'palkattahes',       
                      50 => 'älä palkai',  74 => 'älgäh palkakkahes',  
                75 => '',  76 => 'älgiä palkakkuattokseh',  77 => 'äldähes palkattahes',  
            
                38 => 'palkuazimmos',   39 => 'palkuazittos',  40 => 'palkuazihes',  
                41 => 'palkuazimmokseh',  42 => 'palkuazittokseh',  43 => 'palkattazihes', 
                110 => 'en palkuazihes', 111 => 'et palkuazihes', 112 => 'ei palkuazihes', 
                113 => 'emmo palkuazihes', 114 => 'etto palkuazihes', 115 => 'ei palkattazihes',

                44 => 'palkannuzimmos',   45 => 'palkannuzittos',  46 => 'palkannuzihes',  
                47 => 'palkannuzimmokseh',  48 => 'palkannuzittokseh',  49 => 'palkatannuzihes', 
                116 => 'en palkannuzihes', 117 => 'et palkannuzihes', 118 => 'ei palkannuzihes', 
                119 => 'emmo palkannuzihes', 120 => 'etto palkannuzihes', 121 => 'ei palkatannuzihes',
            
                122 => 'olizin palkannuhes', 123 => 'olizit palkannuhes', 124 => 'olis palkannuhes', 
                126 => 'olizimmo palkannuhes', 127 => 'olizitto palkannuhes', 128 => 'oldas palkattuhes', 
                129 => 'en olis palkannuhes', 130 => 'et olis palkannuhes', 131 => 'ei olis palkannuhes', 
                132 => 'emmo olis palkannuhes', 133 => 'etto olis palkannuhes', 134 => 'ei oldas palkattuhes',
            
                135 => 'olluzin palkannuhes', 125 => 'olluzit palkannuhes', 136 => 'ollus palkannuhes', 
                137 => 'olluzimmo palkannuhes', 138 => 'olluzitto palkannuhes', 139 => 'oldanus palkattuhes', 
                140 => 'en ollus palkannuhes', 141 => 'et ollus palkannuhes', 142 => 'ei ollus palkannuhes', 
                143 => 'emmo ollus palkannuhes', 144 => 'etto ollus palkannuhes', 145 => 'ei oldanus palkattuhes',
            
                146 => 'palkannemmos', 147 => 'palkannettos', 148 => 'palkannehes', 
                149 => 'palkannemmokseh', 150 => 'palkannettokseh', 151 => 'palkatannehes', 
                152 => 'en palkannei', 153 => 'et palkannei', 154 => 'ei palkannei', 
                155 => 'emmo palkannei', 156 => 'etto palkannei', 157 => 'ei palkatannehes',
            
                158 => 'ollen palkannuhes', 159 => 'ollet palkannuhes', 160 => 'ollou palkannuhes', 
                161 => 'ollemmo palkannuhes', 162 => 'olletto palkannuhes', 163 => 'oldaneh palkattuhes', 
                164 => 'en olle palkannuhes', 165 => 'et olle palkannuhes', 166 => 'ei olle palkannuhes', 
                167 => 'emmo olle palkannuhes', 168 => 'etto olle palkannuhes', 169 => 'ei oldane palkattuhes',
            
                170 => 'palkatakseh', 171 => '', 172 => '', 
                173 => '', 174 => '', 175 => '', 
                176 => '', 177 => '', 312 => '',
            
                178 => '', 179 => 'palkannuhes', 180 => '', 181 => 'palkattuhes'];
//        $slice = 50;
//        $this->assertEquals(array_slice($expected, 0, $slice, true), array_slice($result, 0, $slice, true));        
        $this->assertEquals( $expected, $result);        
    }

    // ---------------- stems for proper karelian verbs from mini templates
    
    public function testStemsFromMiniTemplateForProperVerbItkie() {
        $template = "it|kie [e]";
        $lang_id = 4;
        $dialect_id = 46;
        $pos_id = 11; // verb
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'itkie', 
                      1=>'ite', 
                      2=>'itke', 
                      3=>'iti', 
                      4=>'itki', 
                      5=>'itke',
                      6=>'itetä',
                      7=>'itett',
                      10=>FALSE
            ], $num, 'it', 'kie'];
        $this->assertEquals( $expected, $result);        
    }
       
    public function testStemsFromMiniTemplateForProperVerbAn() {
        $template = "an|tua [na]";
        $lang_id = 4;
        $dialect_id = 46;
        $pos_id = 11; // verb
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'antua', 
                      1=>'anna', 
                      2=>'anta', 
                      3=>'annoi', 
                      4=>'anto', 
                      5=>'anta',
                      6=>'anneta',
                      7=>'annett',
                      10=>TRUE
            ], $num, 'an', 'tua'];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testStemsFromMiniTemplateForProperVerbJuuvva() {
        $template = "ju|uvva [o]";
        $lang_id = 4;
        $dialect_id = 46;
        $pos_id = 11; // verb
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'juuvva', 
                      1=>'juo', 
                      2=>'juo', 
                      3=>'joi', 
                      4=>'joi', 
                      5=>'juo',
                      6=>'juuvva',
                      7=>'juot',
                      10=>TRUE
            ], $num, 'ju', 'uvva'];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testStemsFromMiniTemplateForProperVerbKapaloija() {
        $template = "kapaloi|ja [če]";
        $lang_id = 4;
        $dialect_id = 46;
        $pos_id = 11; // verb
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'kapaloija', 
                      1=>'kapaloiče', 
                      2=>'kapaloičče', 
                      3=>'kapaloiči', 
                      4=>'kapaloičči', 
                      5=>'kapaloi',
                      6=>'kapaloija',
                      7=>'kapaloit',
                      10=>TRUE
            ], $num, 'kapaloi', 'ja'];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testStemsFromMiniTemplateForProperVerbTul() {
        $template = "tul|la [e]";
        $lang_id = 4;
        $dialect_id = 46;
        $pos_id = 11; // verb
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'tulla', 
                      1=>'tule', 
                      2=>'tule', 
                      3=>'tuli', 
                      4=>'tuli', 
                      5=>'tul',
                      6=>'tulla',
                      7=>'tult',
                      10=>TRUE
            ], $num, 'tul', 'la'];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testStemsFromMiniTemplateForProperVerbRuveta() {
        $template = "ru|veta [pie]";
        $lang_id = 4;
        $dialect_id = 46;
        $pos_id = 11; // verb
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'ruveta', 
                      1=>'rupie', 
                      2=>'rupie', 
                      3=>'rupei/rupesi', 
                      4=>'rupei/rupesi', 
                      5=>'ruvet',
                      6=>'ruveta',
                      7=>'ruvett',
                      10=>TRUE
            ], $num, 'ru', 'veta'];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testStemsFromMiniTemplateForProperVerbTarita() {
        $template = "tari|ta [če]";
        $lang_id = 4;
        $dialect_id = 46;
        $pos_id = 11; // verb
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'tarita', 
                      1=>'tariče', 
                      2=>'taričče', 
                      3=>'tariči', 
                      4=>'taričči', 
                      5=>'tarit',
                      6=>'tarita',
                      7=>'taritt',
                      10=>TRUE
            ], $num, 'tari', 'ta'];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testStemsFromMiniTemplateForProperVerbKaccuo() {
        $template = "kač|čuo [o]";
        $lang_id = 4;
        $dialect_id = 46;
        $pos_id = 11; // verb
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'kaččuo', 
                      1=>'kačo', 
                      2=>'kaččo', 
                      3=>'kačoi', 
                      4=>'kaččo', 
                      5=>'kaččo',
                      6=>'kačota',
                      7=>'kačott',
                      10=>TRUE
            ], $num, 'kač', 'čuo'];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testStemsFromMiniTemplateForProperVerbEccie() {
        $template = "eč|čie [i]";
        $lang_id = 4;
        $dialect_id = 46;
        $pos_id = 11; // verb
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'eččie', 
                      1=>'eči', 
                      2=>'ečči', 
                      3=>'eči', 
                      4=>'ečči', 
                      5=>'ečči',
                      6=>'ečitä',
                      7=>'ečitt',
                      10=>FALSE
            ], $num, 'eč', 'čie'];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testStemsFromMiniTemplateForProperVerbVarata() {
        $template = "vara|ta [ja]";
        $lang_id = 4;
        $dialect_id = 46;
        $pos_id = 11; // verb
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'varata', 
                      1=>'varaja', 
                      2=>'varaja', 
                      3=>'varasi', 
                      4=>'varasi', 
                      5=>'varat',
                      6=>'varata',
                      7=>'varatt',
                      10=>TRUE
            ], $num, 'vara', 'ta'];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testStemsFromMiniTemplateForProperVerbKiantia() {
        $template = "kiän|tiä [nä]";
        $lang_id = 4;
        $dialect_id = 46;
        $pos_id = 11; // verb
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'kiäntiä', 
                      1=>'kiännä', 
                      2=>'kiäntä', 
                      3=>'kiänni', 
                      4=>'kiänti', 
                      5=>'kiäntä',
                      6=>'kiännetä',
                      7=>'kiännett',
                      10=>FALSE
            ], $num, 'kiän', 'tiä'];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testStemsFromMiniTemplateForProperVerbOttua() {
        $template = "ot|tua [a]";
        $lang_id = 4;
        $dialect_id = 46;
        $pos_id = 11; // verb
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'ottua', 
                      1=>'ota', 
                      2=>'otta', 
                      3=>'oti', 
                      4=>'otti', 
                      5=>'otta',
                      6=>'oteta',
                      7=>'otett',
                      10=>TRUE
            ], $num, 'ot', 'tua'];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testStemsFromMiniTemplateForProperVerbRuatua() {
        $template = "rua|tua []";
        $lang_id = 4;
        $dialect_id = 46;
        $pos_id = 11; // verb
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'ruatua', 
                      1=>'rua', 
                      2=>'ruata', 
                      3=>'ruavoi', 
                      4=>'ruato', 
                      5=>'ruata',
                      6=>'ruata',
                      7=>'ruatt',
                      10=>TRUE
            ], $num, 'rua', 'tua'];
        $this->assertEquals( $expected, $result);        
    }
  
    public function testWordformsByStemsKarVerbProper() {
        $template = 'it|kie [e]';
        $lang_id = 4;
        $pos_id = 11;
        $name_num=null;
        $dialect_id='';
        $is_reflexive = false;
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id, $is_reflexive);

        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems, $is_reflexive);
        $expected = [26 => 'iten',   27 => 'itet',  28 => 'itköy',  
                29 => 'itemmä',  30 => 'itettä',  31 => 'itetäh', 295 => 'ite', 296 => 'itetä', 
                70 => 'en ite',   71 => 'et ite',  72 => 'ei ite',  
                73 => 'emmä ite',  78 => 'että ite',  79 => 'ei itetä', 
            
                32 => 'itin',   33 => 'itit',  34 => 'itki',  
                35 => 'itkimä',  36 => 'itkijä',  37 => 'itettih', 297 => 'itken', 298 => 'itetty', 
                80 => 'en itken',   81 => 'et itken',  82 => 'ei itken',  
                83 => 'emmä itken',  84 => 'että itken',  85 => 'ei itetty', 
            
                86 => 'olen itken',   87 => 'olet itken',  88 => 'on itken',  
                89 => 'olemma itken',  90 => 'oletta itken',  91 => 'on itetty',  
                92 => 'en ole itken',  93 => 'et ole itken',  94 => 'ei ole itken',  
                95 => 'emmä ole itken',  96 => 'että ole itken',  97 => 'ei ole itetty',
            
                98 => 'olin itken',   99 => 'olit itken', 100 => 'oli itken', 
                101 => 'olima itken', 102 => 'olija itken', 103 => 'oli itetty', 
                104 => 'en ollun itken', 105 => 'et ollun itken', 107 => 'ei ollun itken', 
                108 => 'emmä ollun itken', 106 => 'että ollun itken', 109 => 'ei oltu itetty',
            
                      51 => 'ite',  52 => 'itkekkäh',  
                53 => 'itkekkä',  54 => 'itkekkyä',  55 => 'itkekkäh',       
                      50 => 'elä ite',  74 => 'elkäh itkekkäh',  
                75 => 'elkä itkekkä',  76 => 'elkyä itkekkyä',  77 => 'elkäh itkekkäh',  
            
                44 => 'itkisin',   45 => 'itkisit',  46 => 'itkis',  
                47 => 'itkisimä',  48 => 'itkisijä',  49 => 'itettäis', 
                116 => 'en itkis', 117 => 'et itkis', 118 => 'ei itkis', 
                119 => 'emmä itkis', 120 => 'että itkis', 121 => 'ei itettäis',
            
                135 => 'olisin itken', 125 => 'olisit itken', 136 => 'olis itken', 
                137 => 'olisima itken', 138 => 'olisija itken', 139 => 'olis itetty', 
                140 => 'en olis itken', 141 => 'et olis itken', 142 => 'ei olis itken', 
                143 => 'emmä olis itken', 144 => 'että olis itken', 145 => 'ei olis itetty',
            
                146 => 'itkenen', 147 => 'itkenet', 148 => 'itkenöy',
                149 => 'itkenemmä', 150 => 'itkenettä', 151 => 'itettäneh', 310 => 'itkene', 311 => 'itettäne', 
                152 => 'en itkene', 153 => 'et itkene', 154 => 'ei itkene', 
                155 => 'emmä itkene', 156 => 'että itkene', 157 => 'ei itettäne',
            
                158 => 'lienen itken', 159 => 'lienet itken', 160 => 'lienöy itken', 
                161 => 'lienemmä itken', 162 => 'lienettä itken', 163 => 'lienöy itetty', 
                164 => 'en liene itken', 165 => 'et liene itken', 166 => 'ei liene itken', 
                167 => 'emmä liene itken', 168 => 'että liene itken', 169 => 'ei liene itetty',
            
                170 => 'itkie', 171 => 'itkieššä', 172 => 'itkien', 
                173 => 'itkömällä', 174 => 'itkömäh', 175 => 'itkömäššä', 
                176 => 'itkömäštä', 177 => 'itkömättä', 
            
                178 => 'itkijä', 179 => 'itkenyt', 282=>'itken', 180 => 'itettävä', 181 => 'itetty'];
//        $slice = 50;
//        $this->assertEquals(array_slice($expected, 0, $slice, true), array_slice($result, 0, $slice, true));        
        $this->assertEquals( $expected, $result);        
    }
    
    public function testRemoveSoftening()
    {
        $words = ['ivual’en', 'ivual’ou', 'ivual’l’a', 'ivual’i', 'ivual’it', 'ivual’l’en'];
        $result = [];
        foreach ($words as $word) {
            $result[] = Grammatic::removeSoftening($word);
        }
        $expected = ['ivualen', 'ivual’ou', 'ivual’l’a', 'ivuali', 'ivualit', 'ivual’l’en'];
        $this->assertEquals( $expected, $result);        
    }
        
    public function testStemsFromTemplateLudicNames() {
        $lang_id = 6;
        $pos_id=5;
        $num = NULL;
	$templates = [
	    14295 => 'mua []',
	    37909 => 'diä []',
	    62004 => 'halg|o [o]',
	    13424 => 'kirik|kö [ö]',
	    38637 => 'lindu []',
	    50254 => 'kydy []',
	    37234 => 'pu|u [u]',
	    47289 => 'leib|e [ä]',
//	    50308 => 'emänd|e [ä]',
	    29962 => 'ak|ke [a]',
	    66666 => 'uk|ko [o]',
	    47713 => 'nuot|te [a]',
	    70274 => 'huondekselli|ne [že, š]',
	    70199 => 'ala|ine [iže, š]',
	    21396 => 'd’og|i [e]',
	    37342 => 'tuoh|i [e, ]',
	    69940 => 'ast|ii [’ai]',
	    62160 => 'hi|ili [ile, il]',
	    36683 => 'lu|mi [me, n]',
	    21425 => 'pien|i [e, ]',
	    30743 => 'hiir|i [e, ]',
	    62360 => 'y|ksi [hte, h]',
	    13463 => 'la|psi [pse, s]',
	    28722 => 've|zi [de, t]',
	    48744 => 'par|ži [de, ]',
	    46747 => 'poč|či [i]',
	    70271 => 'd’algat|oi [toma, on]',
	    70272 => 'iänet|öi [tömä, ön]',
	    67102 => 'dänö|i [i]',
	    69938 => 'dänyö|i [i]',
	    69883 => 'tiä|i [i]',
	    3540 => 'pedä|i [jä]',
	    51763 => 'lyhy|d [dä, t]',
	    18330 => 'pereh [e, ]',
	    49174 => 'petkel [e, ]',
	    46942 => 'paimen [e, ]',
	    70254 => 'härki|n [me, n]',
	    28825 => 'tyt|är [täre, är]',
	    70269 => 'lapsu|t [de, t]',
	    40672 => 'barba|z [ha, s]',
	    70262 => 'mät|äz [tähä, äs]',
	    47157 => 'kirve|z [he, s]',
	    46865 => 'vere|z [kse, s]',
	    46862 => 'vere|s [kse, s]',
	    28730 => 'kaglu|s [kse, s]',
	    40633 => 'kynäbry|s [kse, s]',
	    70267 => 'vahnu|z [de, t]',
	    66796 => 'hyvy|z [de, t]',
            40704 => 'd’alg|e [a]',
	];

        foreach ($templates as $lemma_id=>$template) {
            $result[$lemma_id] = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
        }

	$expected = [
	    14295 => [0 => [0 => 'mua', 1 => 'mua', 2 => 'mua', 3 => 'muad', 4 => 'mualuoi', 5 => 'mualuoi', 6 => 'mua', 10 => true], 1 => null, 2 => 'mua', 3 => ''],
	    37909 => [0 => [0 => 'diä', 1 => 'diä', 2 => 'diä', 3 => 'diäd', 4 => 'diälyöi', 5 => 'diälyöi', 6 => 'diä', 10 => false], 1 => null, 2 => 'diä', 3 => ''],
            62004 => [0 => [0 => 'halgo', 1 => 'halgo', 2 => 'halgo', 3 => 'halgod', 4 => 'halgoi/halguoi', 5 => 'halgoi/halguoi', 6 => 'halgo', 10 => true], 1 => null, 2 => 'halg', 3 => 'o'],
	    13424 => [0 => [0 => 'kirikkö', 1 => 'kirikö', 2 => 'kirikkö', 3 => 'kirikköd', 4 => 'kiriköi/kirikyöi', 5 => 'kirikköi/kirikkyöi', 6 => 'kirikkö', 10 => false], 1 => null, 2 => 'kirik', 3 => 'kö'],
	    38637 => [0 => [0 => 'lindu', 1 => 'lindu', 2 => 'lindu', 3 => 'lindud', 4 => 'lindui', 5 => 'lindui', 6 => 'lindu', 10 => true], 1 => null, 2 => 'lindu', 3 => ''],
	    50254 => [0 => [0 => 'kydy', 1 => 'kydy', 2 => 'kydy', 3 => 'kydyd', 4 => 'kydyi', 5 => 'kydyi', 6 => 'kydy', 10 => false], 1 => null, 2 => 'kydy', 3 => ''],
	    37234 => [0 => [0 => 'puu', 1 => 'puu', 2 => 'puu', 3 => 'puud', 4 => 'puuluoi', 5 => 'puuluoi', 6 => 'puu', 10 => true], 1 => null, 2 => 'pu', 3 => 'u'],
	    47289 => [0 => [0 => 'leibe', 1 => 'leibä', 2 => 'leibä', 3 => 'leibäd', 4 => 'leibi', 5 => 'leibi', 6 => 'leibä', 10 => false], 1 => null, 2 => 'leib', 3 => 'e'],
//	    50308 => [0 => [0 => 'emände', 1 => 'emändä', 2 => 'emändä', 3 => 'emändäd', 4 => 'emändi', 5 => 'emändi', 6 => 'emändä', 10 => false], 1 => null, 2 => 'emänd', 3 => 'e'],
	    29962 => [0 => [0 => 'akke', 1 => 'aka', 2 => 'akka', 3 => 'akkad', 4 => 'akoi/akuoi', 5 => 'akkoi/akkuoi', 6 => 'akka', 10 => true], 1 => null, 2 => 'ak', 3 => 'ke'],
	    66666 => [0 => [0 => 'ukko', 1 => 'uko', 2 => 'ukko', 3 => 'ukkod', 4 => 'ukoi/ukuoi', 5 => 'ukkoi/ukkuoi', 6 => 'ukko', 10 => true], 1 => null, 2 => 'uk', 3 => 'ko'],
	    47713 => [0 => [0 => 'nuotte', 1 => 'nuota', 2 => 'nuotta', 3 => 'nuottad', 4 => 'nuoti', 5 => 'nuotti', 6 => 'nuotta', 10 => true], 1 => null, 2 => 'nuot', 3 => 'te'],
	    70274 => [0 => [0 => 'huondekselline', 1 => 'huondekselliže', 2 => 'huondekselliže', 3 => 'huondeksellište', 4 => 'huondekselliži', 5 => 'huondekselliži', 6 => 'huondekselliš', 10 => true], 1 => null, 2 => 'huondekselli', 3 => 'ne'],
	    70199 => [0 => [0 => 'alaine', 1 => 'alaiže', 2 => 'alaiže', 3 => 'alašte', 4 => 'alaiži', 5 => 'alaiži', 6 => 'alaš', 10 => true], 1 => null, 2 => 'ala', 3 => 'ine'],
	    21396 => [0 => [0 => 'd’ogi', 1 => 'd’oge', 2 => 'd’oge', 3 => 'd’oged', 4 => 'd’ogi', 5 => 'd’ogi', 6 => 'd’oge', 10 => true], 1 => null, 2 => 'd’og', 3 => 'i'],
	    37342 => [0 => [0 => 'tuohi', 1 => 'tuohe', 2 => 'tuohe', 3 => 'tuohte', 4 => 'tuohi', 5 => 'tuohi', 6 => 'tuoh', 10 => true], 1 => null, 2 => 'tuoh', 3 => 'i'],
	    69940 => [0 => [0 => 'astii', 1 => 'ast’ai', 2 => 'ast’ai', 3 => 'ast’aid', 4 => 'ast’oi/ast’uoi', 5 => 'ast’oi/ast’uoi', 6 => 'ast’ai', 10 => true], 1 => null, 2 => 'ast', 3 => 'ii'],
	    62160 => [0 => [0 => 'hiili', 1 => 'hiile', 2 => 'hiile', 3 => 'hiilte', 4 => 'hiili', 5 => 'hiili', 6 => 'hiil', 10 => false], 1 => null, 2 => 'hi', 3 => 'ili'],
	    36683 => [0 => [0 => 'lumi', 1 => 'lume', 2 => 'lume', 3 => 'lunte', 4 => 'lumi', 5 => 'lumi', 6 => 'lun', 10 => true], 1 => null, 2 => 'lu', 3 => 'mi'],
	    21425 => [0 => [0 => 'pieni', 1 => 'piene', 2 => 'piene', 3 => 'piente', 4 => 'pieni', 5 => 'pieni', 6 => 'pien', 10 => false], 1 => null, 2 => 'pien', 3 => 'i'],
	    30743 => [0 => [0 => 'hiiri', 1 => 'hiire', 2 => 'hiire', 3 => 'hiirte', 4 => 'hiiri', 5 => 'hiiri', 6 => 'hiir', 10 => false], 1 => null, 2 => 'hiir', 3 => 'i'],
	    62360 => [0 => [0 => 'yksi', 1 => 'yhte', 2 => 'yhte', 3 => 'yhte', 4 => 'yksi', 5 => 'yksi', 6 => 'yh', 10 => false], 1 => null, 2 => 'y', 3 => 'ksi'],
	    13463 => [0 => [0 => 'lapsi', 1 => 'lapse', 2 => 'lapse', 3 => 'laste', 4 => 'lapsi', 5 => 'lapsi', 6 => 'las', 10 => true], 1 => null, 2 => 'la', 3 => 'psi'],
	    28722 => [0 => [0 => 'vezi', 1 => 'vede', 2 => 'vede', 3 => 'vette', 4 => 'vezi', 5 => 'vezi', 6 => 'vet', 10 => false], 1 => null, 2 => 've', 3 => 'zi'],
	    48744 => [0 => [0 => 'parži', 1 => 'parde', 2 => 'parde', 3 => 'parte', 4 => 'parži', 5 => 'parži', 6 => 'par', 10 => true], 1 => null, 2 => 'par', 3 => 'ži'],
            46747 => [0 => [0 => 'počči', 1 => 'poči', 2 => 'počči', 3 => 'poččid', 4 => 'počii', 5 => 'poččii', 6 => 'počči', 10 => true], 1 => null, 2 => 'poč', 3 => 'či'],
	    70271 => [0 => [0 => 'd’algatoi', 1 => 'd’algattoma', 2 => 'd’algattoma', 3 => 'd’algatonte', 4 => 'd’algattomi', 5 => 'd’algattomi', 6 => 'd’algaton', 10 => true], 1 => null, 2 => 'd’algat', 3 => 'oi'],
            70272 => [0 => [0 => 'iänetöi', 1 => 'iänettömä', 2 => 'iänettömä', 3 => 'iänetönte', 4 => 'iänettömi', 5 => 'iänettömi', 6 => 'iänetön', 10 => false], 1 => null, 2 => 'iänet', 3 => 'öi'],
	    67102 => [0 => [0 => 'dänöi', 1 => 'dänöi', 2 => 'dänöi', 3 => 'dänöid', 4 => 'dänölöi/dänölyöi', 5 => 'dänölöi/dänölyöi', 6 => 'dänöi', 10 => false], 1 => null, 2 => 'dänö', 3 => 'i'],
	    69938 => [0 => [0 => 'dänyöi', 1 => 'dänyöi', 2 => 'dänyöi', 3 => 'dänyöid', 4 => 'dänyölöi/dänyölyöi', 5 => 'dänyölöi/dänyölyöi', 6 => 'dänyöi', 10 => false], 1 => null, 2 => 'dänyö', 3 => 'i'],
	    69883 => [0 => [0 => 'tiäi', 1 => 'tiäi', 2 => 'tiäi', 3 => 'tiäid', 4 => 'tiäilyöi', 5 => 'tiäilyöi', 6 => 'tiäi', 10 => false], 1 => null, 2 => 'tiä', 3 => 'i'],
	    3540 =>  [0 => [0 => 'pedäi', 1 => 'pedäjä', 2 => 'pedäjä', 3 => 'pedäjäd', 4 => 'pedäji', 5 => 'pedäji', 6 => 'pedäjä', 10 => false], 1 => null, 2 => 'pedä', 3 => 'i'],
	    51763 => [0 => [0 => 'lyhyd', 1 => 'lyhydä', 2 => 'lyhydä', 3 => 'lyhytte', 4 => 'lyhydi', 5 => 'lyhydi', 6 => 'lyhyt', 10 => false], 1 => null, 2 => 'lyhy', 3 => 'd'],
	    18330 => [0 => [0 => 'pereh', 1 => 'perehe', 2 => 'perehe', 3 => 'perehte', 4 => 'perehi', 5 => 'perehi', 6 => 'pereh', 10 => false], 1 => null, 2 => 'pereh', 3 => ''],
	    49174 => [0 => [0 => 'petkel', 1 => 'petkele', 2 => 'petkele', 3 => 'petkelte', 4 => 'petkeli', 5 => 'petkeli', 6 => 'petkel', 10 => false], 1 => null, 2 => 'petkel', 3 => ''],
	    46942 => [0 => [0 => 'paimen', 1 => 'paimene', 2 => 'paimene', 3 => 'paimente', 4 => 'paimeni', 5 => 'paimeni', 6 => 'paimen', 10 => true], 1 => null, 2 => 'paimen', 3 => ''],
            70254 => [0 => [0 => 'härkin', 1 => 'härkime', 2 => 'härkime', 3 => 'härkinte', 4 => 'härkimi', 5 => 'härkimi', 6 => 'härkin', 10 => false], 1 => null, 2 => 'härki', 3 => 'n'],
	    28825 => [0 => [0 => 'tytär', 1 => 'tyttäre', 2 => 'tyttäre', 3 => 'tytärte', 4 => 'tyttäri', 5 => 'tyttäri', 6 => 'tytär', 10 => false], 1 => null, 2 => 'tyt', 3 => 'är'],
	    70269 => [0 => [0 => 'lapsut', 1 => 'lapsude', 2 => 'lapsude', 3 => 'lapsutte', 4 => 'lapsuzi', 5 => 'lapsuzi', 6 => 'lapsut', 10 => true], 1 => null, 2 => 'lapsu', 3 => 't'],
	    40672 => [0 => [0 => 'barbaz', 1 => 'barbaha', 2 => 'barbaha', 3 => 'barbaste', 4 => 'barbahi', 5 => 'barbahi', 6 => 'barbas', 10 => true], 1 => null, 2 => 'barba', 3 => 'z'],
	    70262 => [0 => [0 => 'mätäz', 1 => 'mättähä', 2 => 'mättähä', 3 => 'mätäste', 4 => 'mättähi', 5 => 'mättähi', 6 => 'mätäs', 10 => false], 1 => null, 2 => 'mät', 3 => 'äz'],
            47157 => [0 => [0 => 'kirvez', 1 => 'kirvehe', 2 => 'kirvehe', 3 => 'kirveste', 4 => 'kirvehi', 5 => 'kirvehi', 6 => 'kirves', 10 => false], 1 => null, 2 => 'kirve', 3 => 'z'],
	    46865 => [0 => [0 => 'verez', 1 => 'verekse', 2 => 'verekse', 3 => 'vereste', 4 => 'vereksi', 5 => 'vereksi', 6 => 'veres', 10 => false], 1 => null, 2 => 'vere', 3 => 'z'],
	    46862 => [0 => [0 => 'veres', 1 => 'verekse', 2 => 'verekse', 3 => 'vereste', 4 => 'vereksi', 5 => 'vereksi', 6 => 'veres', 10 => false], 1 => null, 2 => 'vere', 3 => 's'],
  	    28730 => [0 => [0 => 'kaglus', 1 => 'kaglukse', 2 => 'kaglukse', 3 => 'kagluste', 4 => 'kagluksi', 5 => 'kagluksi', 6 => 'kaglus', 10 => true], 1 => null, 2 => 'kaglu', 3 => 's'],
	    40633 => [0 => [0 => 'kynäbrys', 1 => 'kynäbrykse', 2 => 'kynäbrykse', 3 => 'kynäbryste', 4 => 'kynäbryksi', 5 => 'kynäbryksi', 6 => 'kynäbrys', 10 => false], 1 => null, 2 => 'kynäbry', 3 => 's'],
	    70267 => [0 => [0 => 'vahnuz', 1 => 'vahnude', 2 => 'vahnude', 3 => 'vahnutte', 4 => 'vahnuzi', 5 => 'vahnuzi', 6 => 'vahnut', 10 => true], 1 => null, 2 => 'vahnu', 3 => 'z'],
	    66796 => [0 => [0 => 'hyvyz', 1 => 'hyvyde', 2 => 'hyvyde', 3 => 'hyvytte', 4 => 'hyvyzi', 5 => 'hyvyzi', 6 => 'hyvyt', 10 => false], 1 => null, 2 => 'hyvy', 3 => 'z'],
	    40704 => [0 => [0 => 'd’alge', 1 => 'd’alga', 2 => 'd’alga', 3 => 'd’algad', 4 => 'd’algoi/d’alguoi', 5 => 'd’algoi/d’alguoi', 6 => 'd’alga', 10 => true], 1 => null, 2 => 'd’alg', 3 => 'e'],
	];
        $this->assertEquals( $expected, $result);        
    }

    public function testWordformsLudicNames() {
        $lang_id = 6;
        $pos_id = 5;
        $name_num=null;
        $dialect_id='42';
	$templates = [
	    14295 => 'mua []',
	    37909 => 'diä []',
	    62004 => 'halg|o [o]',
	    13424 => 'kirik|kö [ö]',
	    38637 => 'lindu []',
	    50254 => 'kydy []',
	    37234 => 'pu|u [u]',
	    47289 => 'leib|e [ä]',
//	    50308 => 'emänd|e [ä]',
	    29962 => 'ak|ke [a]',
	    66666 => 'uk|ko [o]',
	    47713 => 'nuot|te [a]',
	    70274 => 'huondekselli|ne [že, š]',
	    70199 => 'ala|ine [iže, š]',
	    21396 => 'd’og|i [e]',
	    37342 => 'tuoh|i [e, ]',
	    69940 => 'ast|ii [’ai]',
	    62160 => 'hi|ili [ile, il]',
	    36683 => 'lu|mi [me, n]',
	    21425 => 'pien|i [e, ]',
	    30743 => 'hiir|i [e, ]',
	    62360 => 'y|ksi [hte, h]',
	    13463 => 'la|psi [pse, s]',
	    28722 => 've|zi [de, t]',
	    48744 => 'par|ži [de, ]',
	    46747 => 'poč|či [i]',
	    70271 => 'd’algat|oi [toma, on]',
	    70272 => 'iänet|öi [tömä, ön]',
	    67102 => 'dänö|i [i]',
	    69938 => 'dänyö|i [i]',
	    69883 => 'tiä|i [i]',
	    3540 => 'pedä|i [jä]',
	    51763 => 'lyhy|d [dä, t]',
	    18330 => 'pereh [e, ]',
	    49174 => 'petkel [e, ]',
	    46942 => 'paimen [e, ]',
	    70254 => 'härki|n [me, n]',
	    28825 => 'tyt|är [täre, är]',
	    70269 => 'lapsu|t [de, t]',
	    40672 => 'barba|z [ha, s]',
	    70262 => 'mät|äz [tähä, äs]',
	    47157 => 'kirve|z [he, s]',
	    46865 => 'vere|z [kse, s]',
	    46862 => 'vere|s [kse, s]',
	    28730 => 'kaglu|s [kse, s]',
	    40633 => 'kynäbry|s [kse, s]',
	    70267 => 'vahnu|z [de, t]',
	    66796 => 'hyvy|z [de, t]',
	];
        foreach ($templates as $lemma_id=>$template) {
            list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
            $result[$lemma_id] = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        }
	$expected = [
	    14295 => [1=>'mua', 56=>'mua, muan', 3=>'muan', 4=>'muad', 277=>'muan, muannu', 5=>'muaks, muakse', 6=>'muata', 8=>'muas', 9=>'muas, muaspiäi', 10=>'muah', 11=>'mual', 12=>'mual, mualpiäi', 13=>'mual, muale', 14=>'muanke', 15=>'muači', 17=>'mualloh, mualloo, mualluo', 16=>'muassuai', 19=>'muahpiäi', 2=>'muad, muat', 57=>'muad, muat', 24=>'mualuoiden', 22=>'mualuoid', 279=>'mualuoin, mualuoinnu', 59=>'mualuoiks, mualuoikse', 64=>'mualuoita', 23=>'mualuois', 60=>'mualuois, mualuoispiäi', 61=>'mualuoih', 25=>'mualuoil', 62=>'mualuoil, mualuoilpiäi', 63=>'mualuoil, mualuoile', 65=>'mualuoineh', 66=>'mualuoiči', 281=>'mualuoin', 18=>'mualuoilloh, mualuoilloo, mualuoilluo', 67=>'mualuoissuai', 68=>'mualuoihpiäi', ],
	    37909 => [1=>'diä', 56=>'diä, diän', 3=>'diän', 4=>'diäd', 277=>'diän, diänny', 5=>'diäks, diäkse', 6=>'diätä', 8=>'diäs', 9=>'diäs, diäspiäi', 10=>'diäh', 11=>'diäl', 12=>'diäl, diälpiäi', 13=>'diäl, diäle', 14=>'diänke', 15=>'diäči', 17=>'diällyö, diällöh, diällöö', 16=>'diässuai', 19=>'diähpiäi', 2=>'diäd, diät', 57=>'diäd, diät', 24=>'diälyöiden', 22=>'diälyöid', 279=>'diälyöin, diälyöinny', 59=>'diälyöiks, diälyöikse', 64=>'diälyöitä', 23=>'diälyöis', 60=>'diälyöis, diälyöispiäi', 61=>'diälyöih', 25=>'diälyöil', 62=>'diälyöil, diälyöilpiäi', 63=>'diälyöil, diälyöile', 65=>'diälyöineh', 66=>'diälyöiči', 281=>'diälyöin', 18=>'diälyöillyö, diälyöillöh, diälyöillöö', 67=>'diälyöissuai', 68=>'diälyöihpiäi', ],
	    62004 => [1=>'halgo', 56=>'halgo, halgon', 3=>'halgon', 4=>'halgod', 277=>'halgon, halgonnu', 5=>'halgoks, halgokse', 6=>'halgota', 8=>'halgos', 9=>'halgos, halgospiäi', 10=>'halgoh', 11=>'halgol', 12=>'halgol, halgolpiäi', 13=>'halgol, halgole', 14=>'halgonke', 15=>'halgoči', 17=>'halgolloh, halgolloo, halgolluo', 16=>'halgossuai', 19=>'halgohpiäi', 2=>'halgod, halgot', 57=>'halgod, halgot', 24=>'halgoiden, halguoiden', 22=>'halgoid, halguoid', 279=>'halgoin, halgoinnu, halguoin, halguoinnu', 59=>'halgoiks, halgoikse, halguoiks, halguoikse', 64=>'halgoita, halguoita', 23=>'halgois, halguois', 60=>'halgois, halgoispiäi, halguois, halguoispiäi', 61=>'halgoih, halguoih', 25=>'halgoil, halguoil', 62=>'halgoil, halgoilpiäi, halguoil, halguoilpiäi', 63=>'halgoil, halgoile, halguoil, halguoile', 65=>'halgoineh, halguoineh', 66=>'halgoiči, halguoiči', 281=>'halgoin, halguoin', 18=>'halgoilloh, halgoilloo, halgoilluo, halguoilloh, halguoilloo, halguoilluo', 67=>'halgoissuai, halguoissuai', 68=>'halgoihpiäi, halguoihpiäi', ],
	    13424 => [1=>'kirikkö', 56=>'kirikkö, kirikön', 3=>'kirikön', 4=>'kirikköd', 277=>'kirikön, kirikönny', 5=>'kiriköks, kirikökse', 6=>'kirikötä', 8=>'kirikös', 9=>'kirikös, kiriköspiäi', 10=>'kirikköh', 11=>'kiriköl', 12=>'kiriköl, kirikölpiäi', 13=>'kiriköl, kiriköle', 14=>'kirikönke', 15=>'kiriköči', 17=>'kiriköllyö, kiriköllöh, kiriköllöö', 16=>'kirikkössuai', 19=>'kirikköhpiäi', 2=>'kiriköd, kiriköt', 57=>'kiriköd, kiriköt', 24=>'kirikyöiden, kiriköiden', 22=>'kirikkyöid, kirikköid', 279=>'kirikyöin, kirikyöinny, kiriköin, kiriköinny', 59=>'kirikyöiks, kirikyöikse, kiriköiks, kiriköikse', 64=>'kirikyöitä, kiriköitä', 23=>'kirikyöis, kiriköis', 60=>'kirikyöis, kirikyöispiäi, kiriköis, kiriköispiäi', 61=>'kirikkyöih, kirikköih', 25=>'kirikyöil, kiriköil', 62=>'kirikyöil, kirikyöilpiäi, kiriköil, kiriköilpiäi', 63=>'kirikyöil, kirikyöile, kiriköil, kiriköile', 65=>'kirikyöineh, kiriköineh', 66=>'kirikyöiči, kiriköiči', 281=>'kirikyöin, kiriköin', 18=>'kirikyöillyö, kirikyöillöh, kirikyöillöö, kiriköillyö, kiriköillöh, kiriköillöö', 67=>'kirikkyöissuai, kirikköissuai', 68=>'kirikkyöihpiäi, kirikköihpiäi', ],
	    38637 => [1=>'lindu', 56=>'lindu, lindun', 3=>'lindun', 4=>'lindud', 277=>'lindun, lindunnu', 5=>'linduks, lindukse', 6=>'linduta', 8=>'lindus', 9=>'lindus, linduspiäi', 10=>'linduh', 11=>'lindul', 12=>'lindul, lindulpiäi', 13=>'lindul, lindule', 14=>'lindunke', 15=>'linduči', 17=>'lindulloh, lindulloo, lindulluo', 16=>'lindussuai', 19=>'linduhpiäi', 2=>'lindud, lindut', 57=>'lindud, lindut', 24=>'linduiden', 22=>'linduid', 279=>'linduin, linduinnu', 59=>'linduiks, linduikse', 64=>'linduita', 23=>'linduis', 60=>'linduis, linduispiäi', 61=>'linduih', 25=>'linduil', 62=>'linduil, linduilpiäi', 63=>'linduil, linduile', 65=>'linduineh', 66=>'linduiči', 281=>'linduin', 18=>'linduilloh, linduilloo, linduilluo', 67=>'linduissuai', 68=>'linduihpiäi', ],
	    50254 => [1=>'kydy', 56=>'kydy, kydyn', 3=>'kydyn', 4=>'kydyd', 277=>'kydyn, kydynny', 5=>'kydyks, kydykse', 6=>'kydytä', 8=>'kydys', 9=>'kydys, kydyspiäi', 10=>'kydyh', 11=>'kydyl', 12=>'kydyl, kydylpiäi', 13=>'kydyl, kydyle', 14=>'kydynke', 15=>'kydyči', 17=>'kydyllyö, kydyllöh, kydyllöö', 16=>'kydyssuai', 19=>'kydyhpiäi', 2=>'kydyd, kydyt', 57=>'kydyd, kydyt', 24=>'kydyiden', 22=>'kydyid', 279=>'kydyin, kydyinny', 59=>'kydyiks, kydyikse', 64=>'kydyitä', 23=>'kydyis', 60=>'kydyis, kydyispiäi', 61=>'kydyih', 25=>'kydyil', 62=>'kydyil, kydyilpiäi', 63=>'kydyil, kydyile', 65=>'kydyineh', 66=>'kydyiči', 281=>'kydyin', 18=>'kydyillyö, kydyillöh, kydyillöö', 67=>'kydyissuai', 68=>'kydyihpiäi', ],
	    37234 => [1=>'puu', 56=>'puu, puun', 3=>'puun', 4=>'puud', 277=>'puun, puunnu', 5=>'puuks, puukse', 6=>'puuta', 8=>'puus', 9=>'puus, puuspiäi', 10=>'puuh', 11=>'puul', 12=>'puul, puulpiäi', 13=>'puul, puule', 14=>'puunke', 15=>'puuči', 17=>'puulloh, puulloo, puulluo', 16=>'puussuai', 19=>'puuhpiäi', 2=>'puud, puut', 57=>'puud, puut', 24=>'puuluoiden', 22=>'puuluoid', 279=>'puuluoin, puuluoinnu', 59=>'puuluoiks, puuluoikse', 64=>'puuluoita', 23=>'puuluois', 60=>'puuluois, puuluoispiäi', 61=>'puuluoih', 25=>'puuluoil', 62=>'puuluoil, puuluoilpiäi', 63=>'puuluoil, puuluoile', 65=>'puuluoineh', 66=>'puuluoiči', 281=>'puuluoin', 18=>'puuluoilloh, puuluoilloo, puuluoilluo', 67=>'puuluoissuai', 68=>'puuluoihpiäi', ],
	    47289 => [1=>'leibe', 56=>'leibe, leibän', 3=>'leibän', 4=>'leibäd', 277=>'leibän, leibänny', 5=>'leibäks, leibäkse', 6=>'leibätä', 8=>'leibäs', 9=>'leibäs, leibäspiäi', 10=>'leibäh', 11=>'leibäl', 12=>'leibäl, leibälpiäi', 13=>'leibäl, leibäle', 14=>'leibänke', 15=>'leibäči', 17=>'leibällyö, leibällöh, leibällöö', 16=>'leibässuai', 19=>'leibähpiäi', 2=>'leibäd, leibät', 57=>'leibäd, leibät', 24=>'leibiden', 22=>'leibid', 279=>'leibin, leibinny', 59=>'leibiks, leibikse', 64=>'leibitä', 23=>'leibis', 60=>'leibis, leibispiäi', 61=>'leibih', 25=>'leibil', 62=>'leibil, leibilpiäi', 63=>'leibil, leibile', 65=>'leibineh', 66=>'leibiči', 281=>'leibin', 18=>'leibillyö, leibillöh, leibillöö', 67=>'leibissuai', 68=>'leibihpiäi', ],
//	    50308 => [1=>'emände', 56=>'emände, emändän', 3=>'emändän', 4=>'emändäd', 277=>'emändän, emändänny', 5=>'emändäks, emändäkse', 6=>'emändätä', 8=>'emändäs', 9=>'emändäs, emändäspiäi', 10=>'emändäh', 11=>'emändäl', 12=>'emändäl, emändälpiäi', 13=>'emändäl, emändäle', 14=>'emändänke', 15=>'emändäči', 17=>'emändällyö, emändällöh, emändällöö', 16=>'emändässuai', 19=>'emändähpiäi', 2=>'emändäd, emändät', 57=>'emändäd, emändät', 24=>'emändiden', 22=>'emändid', 279=>'emändin, emändinny', 59=>'emändiks, emändikse', 64=>'emänditä', 23=>'emändis', 60=>'emändis, emändispiäi', 61=>'emändih', 25=>'emändil', 62=>'emändil, emändilpiäi', 63=>'emändil, emändile', 65=>'emändineh', 66=>'emändiči', 281=>'emändin', 18=>'emändillyö, emändillöh, emändillöö', 67=>'emändissuai', 68=>'emändihpiäi', ],
	    29962 => [1=>'akke', 56=>'akan, akke', 3=>'akan', 4=>'akkad', 277=>'akan, akannu', 5=>'akaks, akakse', 6=>'akata', 8=>'akas', 9=>'akas, akaspiäi', 10=>'akkah', 11=>'akal', 12=>'akal, akalpiäi', 13=>'akal, akale', 14=>'akanke', 15=>'akači', 17=>'akalloh, akalloo, akalluo', 16=>'akkassuai', 19=>'akkahpiäi', 2=>'akad, akat', 57=>'akad, akat', 24=>'akoiden, akuoiden', 22=>'akkoid, akkuoid', 279=>'akoin, akoinnu, akuoin, akuoinnu', 59=>'akoiks, akoikse, akuoiks, akuoikse', 64=>'akoita, akuoita', 23=>'akois, akuois', 60=>'akois, akoispiäi, akuois, akuoispiäi', 61=>'akkoih, akkuoih', 25=>'akoil, akuoil', 62=>'akoil, akoilpiäi, akuoil, akuoilpiäi', 63=>'akoil, akoile, akuoil, akuoile', 65=>'akoineh, akuoineh', 66=>'akoiči, akuoiči', 281=>'akoin, akuoin', 18=>'akoilloh, akoilloo, akoilluo, akuoilloh, akuoilloo, akuoilluo', 67=>'akkoissuai, akkuoissuai', 68=>'akkoihpiäi, akkuoihpiäi', ],
            66666 => [1=>'ukko', 56=>'ukko, ukon', 3=>'ukon', 4=>'ukkod', 277=>'ukon, ukonnu', 5=>'ukoks, ukokse', 6=>'ukota', 8=>'ukos', 9=>'ukos, ukospiäi', 10=>'ukkoh', 11=>'ukol', 12=>'ukol, ukolpiäi', 13=>'ukol, ukole', 14=>'ukonke', 15=>'ukoči', 17=>'ukolloh, ukolloo, ukolluo', 16=>'ukkossuai', 19=>'ukkohpiäi', 2=>'ukod, ukot', 57=>'ukod, ukot', 24=>'ukoiden, ukuoiden', 22=>'ukkoid, ukkuoid', 279=>'ukoin, ukoinnu, ukuoin, ukuoinnu', 59=>'ukoiks, ukoikse, ukuoiks, ukuoikse', 64=>'ukoita, ukuoita', 23=>'ukois, ukuois', 60=>'ukois, ukoispiäi, ukuois, ukuoispiäi', 61=>'ukkoih, ukkuoih', 25=>'ukoil, ukuoil', 62=>'ukoil, ukoilpiäi, ukuoil, ukuoilpiäi', 63=>'ukoil, ukoile, ukuoil, ukuoile', 65=>'ukoineh, ukuoineh', 66=>'ukoiči, ukuoiči', 281=>'ukoin, ukuoin', 18=>'ukoilloh, ukoilloo, ukoilluo, ukuoilloh, ukuoilloo, ukuoilluo', 67=>'ukkoissuai, ukkuoissuai', 68=>'ukkoihpiäi, ukkuoihpiäi', ],
	    47713 => [1=>'nuotte', 56=>'nuotan, nuotte', 3=>'nuotan', 4=>'nuottad', 277=>'nuotan, nuotannu', 5=>'nuotaks, nuotakse', 6=>'nuotata', 8=>'nuotas', 9=>'nuotas, nuotaspiäi', 10=>'nuottah', 11=>'nuotal', 12=>'nuotal, nuotalpiäi', 13=>'nuotal, nuotale', 14=>'nuotanke', 15=>'nuotači', 17=>'nuotalloh, nuotalloo, nuotalluo', 16=>'nuottassuai', 19=>'nuottahpiäi', 2=>'nuotad, nuotat', 57=>'nuotad, nuotat', 24=>'nuotiden', 22=>'nuottid', 279=>'nuotin, nuotinnu', 59=>'nuotiks, nuotikse', 64=>'nuotita', 23=>'nuotis', 60=>'nuotis, nuotispiäi', 61=>'nuottih', 25=>'nuotil', 62=>'nuotil, nuotilpiäi', 63=>'nuotil, nuotile', 65=>'nuotineh', 66=>'nuotiči', 281=>'nuotin', 18=>'nuotilloh, nuotilloo, nuotilluo', 67=>'nuottissuai', 68=>'nuottihpiäi', ],
	    70274 => [1=>'huondekselline', 56=>'huondekselline, huondekselližen', 3=>'huondekselližen', 4=>'huondeksellište', 277=>'huondekselližen, huondekselližennu', 5=>'huondekselližeks, huondekselližekse', 6=>'huondekselližeta', 8=>'huondekselližes', 9=>'huondekselližes, huondekselližespiäi', 10=>'huondekselližeh', 11=>'huondekselližel', 12=>'huondekselližel, huondekselliželpiäi', 13=>'huondekselližel, huondekselližele', 14=>'huondekselliženke', 15=>'huondekselližeči', 17=>'huondekselliželloh, huondekselliželloo, huondekselliželluo', 16=>'huondekselližessuai', 19=>'huondekselližehpiäi', 2=>'huondekselližed, huondekselližet', 57=>'huondekselližed, huondekselližet', 24=>'huondekselližiden', 22=>'huondekselližid', 279=>'huondekselližin, huondekselližinnu', 59=>'huondekselližiks, huondekselližikse', 64=>'huondekselližita', 23=>'huondekselližis', 60=>'huondekselližis, huondekselližispiäi', 61=>'huondekselližih', 25=>'huondekselližil', 62=>'huondekselližil, huondekselližilpiäi', 63=>'huondekselližil, huondekselližile', 65=>'huondekselližineh', 66=>'huondekselližiči', 281=>'huondekselližin', 18=>'huondekselližilloh, huondekselližilloo, huondekselližilluo', 67=>'huondekselližissuai', 68=>'huondekselližihpiäi', ],
	    70199 => [1=>'alaine', 56=>'alaine, alaižen', 3=>'alaižen', 4=>'alašte', 277=>'alaižen, alaižennu', 5=>'alaižeks, alaižekse', 6=>'alaižeta', 8=>'alaižes', 9=>'alaižes, alaižespiäi', 10=>'alaižeh', 11=>'alaižel', 12=>'alaižel, alaiželpiäi', 13=>'alaižel, alaižele', 14=>'alaiženke', 15=>'alaižeči', 17=>'alaiželloh, alaiželloo, alaiželluo', 16=>'alaižessuai', 19=>'alaižehpiäi', 2=>'alaižed, alaižet', 57=>'alaižed, alaižet', 24=>'alaižiden', 22=>'alaižid', 279=>'alaižin, alaižinnu', 59=>'alaižiks, alaižikse', 64=>'alaižita', 23=>'alaižis', 60=>'alaižis, alaižispiäi', 61=>'alaižih', 25=>'alaižil', 62=>'alaižil, alaižilpiäi', 63=>'alaižil, alaižile', 65=>'alaižineh', 66=>'alaižiči', 281=>'alaižin', 18=>'alaižilloh, alaižilloo, alaižilluo', 67=>'alaižissuai', 68=>'alaižihpiäi', ],
	    21396 => [1=>'d’ogi', 56=>'d’ogen, d’ogi', 3=>'d’ogen', 4=>'d’oged', 277=>'d’ogen, d’ogennu', 5=>'d’ogeks, d’ogekse', 6=>'d’ogeta', 8=>'d’oges', 9=>'d’oges, d’ogespiäi', 10=>'d’ogeh', 11=>'d’ogel', 12=>'d’ogel, d’ogelpiäi', 13=>'d’ogel, d’ogele', 14=>'d’ogenke', 15=>'d’ogeči', 17=>'d’ogelloh, d’ogelloo, d’ogelluo', 16=>'d’ogessuai', 19=>'d’ogehpiäi', 2=>'d’oged, d’oget', 57=>'d’oged, d’oget', 24=>'d’ogiden', 22=>'d’ogid', 279=>'d’ogin, d’oginnu', 59=>'d’ogiks, d’ogikse', 64=>'d’ogita', 23=>'d’ogis', 60=>'d’ogis, d’ogispiäi', 61=>'d’ogih', 25=>'d’ogil', 62=>'d’ogil, d’ogilpiäi', 63=>'d’ogil, d’ogile', 65=>'d’ogineh', 66=>'d’ogiči', 281=>'d’ogin', 18=>'d’ogilloh, d’ogilloo, d’ogilluo', 67=>'d’ogissuai', 68=>'d’ogihpiäi', ],
	    37342 => [1=>'tuohi', 56=>'tuohen, tuohi', 3=>'tuohen', 4=>'tuohte', 277=>'tuohen, tuohennu', 5=>'tuoheks, tuohekse', 6=>'tuoheta', 8=>'tuohes', 9=>'tuohes, tuohespiäi', 10=>'tuoheh', 11=>'tuohel', 12=>'tuohel, tuohelpiäi', 13=>'tuohel, tuohele', 14=>'tuohenke', 15=>'tuoheči', 17=>'tuohelloh, tuohelloo, tuohelluo', 16=>'tuohessuai', 19=>'tuohehpiäi', 2=>'tuohed, tuohet', 57=>'tuohed, tuohet', 24=>'tuohiden', 22=>'tuohid', 279=>'tuohin, tuohinnu', 59=>'tuohiks, tuohikse', 64=>'tuohita', 23=>'tuohis', 60=>'tuohis, tuohispiäi', 61=>'tuohih', 25=>'tuohil', 62=>'tuohil, tuohilpiäi', 63=>'tuohil, tuohile', 65=>'tuohineh', 66=>'tuohiči', 281=>'tuohin', 18=>'tuohilloh, tuohilloo, tuohilluo', 67=>'tuohissuai', 68=>'tuohihpiäi', ],
	    69940 => [1=>'astii', 56=>'astii, ast’ain', 3=>'ast’ain', 4=>'ast’aid', 277=>'ast’ain, ast’ainnu', 5=>'ast’aiks, ast’aikse', 6=>'ast’aita', 8=>'ast’ais', 9=>'ast’ais, ast’aispiäi', 10=>'ast’aih', 11=>'ast’ail', 12=>'ast’ail, ast’ailpiäi', 13=>'ast’ail, ast’aile', 14=>'ast’ainke', 15=>'ast’aiči', 17=>'ast’ailloh, ast’ailloo, ast’ailluo', 16=>'ast’aissuai', 19=>'ast’aihpiäi', 2=>'ast’aid, ast’ait', 57=>'ast’aid, ast’ait', 24=>'ast’oiden, ast’uoiden', 22=>'ast’oid, ast’uoid', 279=>'ast’oin, ast’oinnu, ast’uoin, ast’uoinnu', 59=>'ast’oiks, ast’oikse, ast’uoiks, ast’uoikse', 64=>'ast’oita, ast’uoita', 23=>'ast’ois, ast’uois', 60=>'ast’ois, ast’oispiäi, ast’uois, ast’uoispiäi', 61=>'ast’oih, ast’uoih', 25=>'ast’oil, ast’uoil', 62=>'ast’oil, ast’oilpiäi, ast’uoil, ast’uoilpiäi', 63=>'ast’oil, ast’oile, ast’uoil, ast’uoile', 65=>'ast’oineh, ast’uoineh', 66=>'ast’oiči, ast’uoiči', 281=>'ast’oin, ast’uoin', 18=>'ast’oilloh, ast’oilloo, ast’oilluo, ast’uoilloh, ast’uoilloo, ast’uoilluo', 67=>'ast’oissuai, ast’uoissuai', 68=>'ast’oihpiäi, ast’uoihpiäi', ],
	    62160 => [1=>'hiili', 56=>'hiilen, hiili', 3=>'hiilen', 4=>'hiilte', 277=>'hiilen, hiilenny', 5=>'hiileks, hiilekse', 6=>'hiiletä', 8=>'hiiles', 9=>'hiiles, hiilespiäi', 10=>'hiileh', 11=>'hiilel', 12=>'hiilel, hiilelpiäi', 13=>'hiilel, hiilele', 14=>'hiilenke', 15=>'hiileči', 17=>'hiilellyö, hiilellöh, hiilellöö', 16=>'hiilessuai', 19=>'hiilehpiäi', 2=>'hiiled, hiilet', 57=>'hiiled, hiilet', 24=>'hiiliden', 22=>'hiilid', 279=>'hiilin, hiilinny', 59=>'hiiliks, hiilikse', 64=>'hiilitä', 23=>'hiilis', 60=>'hiilis, hiilispiäi', 61=>'hiilih', 25=>'hiilil', 62=>'hiilil, hiililpiäi', 63=>'hiilil, hiilile', 65=>'hiilineh', 66=>'hiiliči', 281=>'hiilin', 18=>'hiilillyö, hiilillöh, hiilillöö', 67=>'hiilissuai', 68=>'hiilihpiäi', ],
	    36683 => [1=>'lumi', 56=>'lumen, lumi', 3=>'lumen', 4=>'lunte', 277=>'lumen, lumennu', 5=>'lumeks, lumekse', 6=>'lumeta', 8=>'lumes', 9=>'lumes, lumespiäi', 10=>'lumeh', 11=>'lumel', 12=>'lumel, lumelpiäi', 13=>'lumel, lumele', 14=>'lumenke', 15=>'lumeči', 17=>'lumelloh, lumelloo, lumelluo', 16=>'lumessuai', 19=>'lumehpiäi', 2=>'lumed, lumet', 57=>'lumed, lumet', 24=>'lumiden', 22=>'lumid', 279=>'lumin, luminnu', 59=>'lumiks, lumikse', 64=>'lumita', 23=>'lumis', 60=>'lumis, lumispiäi', 61=>'lumih', 25=>'lumil', 62=>'lumil, lumilpiäi', 63=>'lumil, lumile', 65=>'lumineh', 66=>'lumiči', 281=>'lumin', 18=>'lumilloh, lumilloo, lumilluo', 67=>'lumissuai', 68=>'lumihpiäi', ],
	    21425 => [1=>'pieni', 56=>'pienen, pieni', 3=>'pienen', 4=>'piente', 277=>'pienen, pienenny', 5=>'pieneks, pienekse', 6=>'pienetä', 8=>'pienes', 9=>'pienes, pienespiäi', 10=>'pieneh', 11=>'pienel', 12=>'pienel, pienelpiäi', 13=>'pienel, pienele', 14=>'pienenke', 15=>'pieneči', 17=>'pienellyö, pienellöh, pienellöö', 16=>'pienessuai', 19=>'pienehpiäi', 2=>'piened, pienet', 57=>'piened, pienet', 24=>'pieniden', 22=>'pienid', 279=>'pienin, pieninny', 59=>'pieniks, pienikse', 64=>'pienitä', 23=>'pienis', 60=>'pienis, pienispiäi', 61=>'pienih', 25=>'pienil', 62=>'pienil, pienilpiäi', 63=>'pienil, pienile', 65=>'pienineh', 66=>'pieniči', 281=>'pienin', 18=>'pienillyö, pienillöh, pienillöö', 67=>'pienissuai', 68=>'pienihpiäi', ],
	    30743 => [1=>'hiiri', 56=>'hiiren, hiiri', 3=>'hiiren', 4=>'hiirte', 277=>'hiiren, hiirenny', 5=>'hiireks, hiirekse', 6=>'hiiretä', 8=>'hiires', 9=>'hiires, hiirespiäi', 10=>'hiireh', 11=>'hiirel', 12=>'hiirel, hiirelpiäi', 13=>'hiirel, hiirele', 14=>'hiirenke', 15=>'hiireči', 17=>'hiirellyö, hiirellöh, hiirellöö', 16=>'hiiressuai', 19=>'hiirehpiäi', 2=>'hiired, hiiret', 57=>'hiired, hiiret', 24=>'hiiriden', 22=>'hiirid', 279=>'hiirin, hiirinny', 59=>'hiiriks, hiirikse', 64=>'hiiritä', 23=>'hiiris', 60=>'hiiris, hiirispiäi', 61=>'hiirih', 25=>'hiiril', 62=>'hiiril, hiirilpiäi', 63=>'hiiril, hiirile', 65=>'hiirineh', 66=>'hiiriči', 281=>'hiirin', 18=>'hiirillyö, hiirillöh, hiirillöö', 67=>'hiirissuai', 68=>'hiirihpiäi', ],
	    62360 => [1=>'yksi', 56=>'yhten, yksi', 3=>'yhten', 4=>'yhte', 277=>'yhten, yhtenny', 5=>'yhteks, yhtekse', 6=>'yhtetä', 8=>'yhtes', 9=>'yhtes, yhtespiäi', 10=>'yhteh', 11=>'yhtel', 12=>'yhtel, yhtelpiäi', 13=>'yhtel, yhtele', 14=>'yhtenke', 15=>'yhteči', 17=>'yhtellyö, yhtellöh, yhtellöö', 16=>'yhtessuai', 19=>'yhtehpiäi', 2=>'yhted, yhtet', 57=>'yhted, yhtet', 24=>'yksiden', 22=>'yksid', 279=>'yksin, yksinny', 59=>'yksiks, yksikse', 64=>'yksitä', 23=>'yksis', 60=>'yksis, yksispiäi', 61=>'yksih', 25=>'yksil', 62=>'yksil, yksilpiäi', 63=>'yksil, yksile', 65=>'yksineh', 66=>'yksiči', 281=>'yksin', 18=>'yksillyö, yksillöh, yksillöö', 67=>'yksissuai', 68=>'yksihpiäi', ],
	    13463 => [1=>'lapsi', 56=>'lapsen, lapsi', 3=>'lapsen', 4=>'laste', 277=>'lapsen, lapsennu', 5=>'lapseks, lapsekse', 6=>'lapseta', 8=>'lapses', 9=>'lapses, lapsespiäi', 10=>'lapseh', 11=>'lapsel', 12=>'lapsel, lapselpiäi', 13=>'lapsel, lapsele', 14=>'lapsenke', 15=>'lapseči', 17=>'lapselloh, lapselloo, lapselluo', 16=>'lapsessuai', 19=>'lapsehpiäi', 2=>'lapsed, lapset', 57=>'lapsed, lapset', 24=>'lapsiden', 22=>'lapsid', 279=>'lapsin, lapsinnu', 59=>'lapsiks, lapsikse', 64=>'lapsita', 23=>'lapsis', 60=>'lapsis, lapsispiäi', 61=>'lapsih', 25=>'lapsil', 62=>'lapsil, lapsilpiäi', 63=>'lapsil, lapsile', 65=>'lapsineh', 66=>'lapsiči', 281=>'lapsin', 18=>'lapsilloh, lapsilloo, lapsilluo', 67=>'lapsissuai', 68=>'lapsihpiäi', ],
	    28722 => [1=>'vezi', 56=>'veden, vezi', 3=>'veden', 4=>'vette', 277=>'veden, vedenny', 5=>'vedeks, vedekse', 6=>'vedetä', 8=>'vedes', 9=>'vedes, vedespiäi', 10=>'vedeh', 11=>'vedel', 12=>'vedel, vedelpiäi', 13=>'vedel, vedele', 14=>'vedenke', 15=>'vedeči', 17=>'vedellyö, vedellöh, vedellöö', 16=>'vedessuai', 19=>'vedehpiäi', 2=>'veded, vedet', 57=>'veded, vedet', 24=>'veziden', 22=>'vezid', 279=>'vezin, vezinny', 59=>'veziks, vezikse', 64=>'vezitä', 23=>'vezis', 60=>'vezis, vezispiäi', 61=>'vezih', 25=>'vezil', 62=>'vezil, vezilpiäi', 63=>'vezil, vezile', 65=>'vezineh', 66=>'veziči', 281=>'vezin', 18=>'vezillyö, vezillöh, vezillöö', 67=>'vezissuai', 68=>'vezihpiäi', ],
	    48744 => [1=>'parži', 56=>'parden, parži', 3=>'parden', 4=>'parte', 277=>'parden, pardennu', 5=>'pardeks, pardekse', 6=>'pardeta', 8=>'pardes', 9=>'pardes, pardespiäi', 10=>'pardeh', 11=>'pardel', 12=>'pardel, pardelpiäi', 13=>'pardel, pardele', 14=>'pardenke', 15=>'pardeči', 17=>'pardelloh, pardelloo, pardelluo', 16=>'pardessuai', 19=>'pardehpiäi', 2=>'parded, pardet', 57=>'parded, pardet', 24=>'paržiden', 22=>'paržid', 279=>'paržin, paržinnu', 59=>'paržiks, paržikse', 64=>'paržita', 23=>'paržis', 60=>'paržis, paržispiäi', 61=>'paržih', 25=>'paržil', 62=>'paržil, paržilpiäi', 63=>'paržil, paržile', 65=>'paržineh', 66=>'paržiči', 281=>'paržin', 18=>'paržilloh, paržilloo, paržilluo', 67=>'paržissuai', 68=>'paržihpiäi', ],
	    46747 => [1=>'počči', 56=>'počin, počči', 3=>'počin', 4=>'poččid', 277=>'počin, počinnu', 5=>'počiks, počikse', 6=>'počita', 8=>'počis', 9=>'počis, počispiäi', 10=>'poččih', 11=>'počil', 12=>'počil, počilpiäi', 13=>'počil, počile', 14=>'počinke', 15=>'počiči', 17=>'počilloh, počilloo, počilluo', 16=>'poččissuai', 19=>'poččihpiäi', 2=>'počid, počit', 57=>'počid, počit', 24=>'počiiden', 22=>'poččiid', 279=>'počiin, počiinnu', 59=>'počiiks, počiikse', 64=>'počiita', 23=>'počiis', 60=>'počiis, počiispiäi', 61=>'poččiih', 25=>'počiil', 62=>'počiil, počiilpiäi', 63=>'počiil, počiile', 65=>'počiineh', 66=>'počiiči', 281=>'počiin', 18=>'počiilloh, počiilloo, počiilluo', 67=>'poččiissuai', 68=>'poččiihpiäi', ],
	    70271 => [1=>'d’algatoi', 56=>'d’algatoi, d’algattoman', 3=>'d’algattoman', 4=>'d’algatonte', 277=>'d’algattoman, d’algattomannu', 5=>'d’algattomaks, d’algattomakse', 6=>'d’algattomata', 8=>'d’algattomas', 9=>'d’algattomas, d’algattomaspiäi', 10=>'d’algattomah', 11=>'d’algattomal', 12=>'d’algattomal, d’algattomalpiäi', 13=>'d’algattomal, d’algattomale', 14=>'d’algattomanke', 15=>'d’algattomači', 17=>'d’algattomalloh, d’algattomalloo, d’algattomalluo', 16=>'d’algattomassuai', 19=>'d’algattomahpiäi', 2=>'d’algattomad, d’algattomat', 57=>'d’algattomad, d’algattomat', 24=>'d’algattomiden', 22=>'d’algattomid', 279=>'d’algattomin, d’algattominnu', 59=>'d’algattomiks, d’algattomikse', 64=>'d’algattomita', 23=>'d’algattomis', 60=>'d’algattomis, d’algattomispiäi', 61=>'d’algattomih', 25=>'d’algattomil', 62=>'d’algattomil, d’algattomilpiäi', 63=>'d’algattomil, d’algattomile', 65=>'d’algattomineh', 66=>'d’algattomiči', 281=>'d’algattomin', 18=>'d’algattomilloh, d’algattomilloo, d’algattomilluo', 67=>'d’algattomissuai', 68=>'d’algattomihpiäi', ],
	    70272 => [1=>'iänetöi', 56=>'iänettömän, iänetöi', 3=>'iänettömän', 4=>'iänetönte', 277=>'iänettömän, iänettömänny', 5=>'iänettömäks, iänettömäkse', 6=>'iänettömätä', 8=>'iänettömäs', 9=>'iänettömäs, iänettömäspiäi', 10=>'iänettömäh', 11=>'iänettömäl', 12=>'iänettömäl, iänettömälpiäi', 13=>'iänettömäl, iänettömäle', 14=>'iänettömänke', 15=>'iänettömäči', 17=>'iänettömällyö, iänettömällöh, iänettömällöö', 16=>'iänettömässuai', 19=>'iänettömähpiäi', 2=>'iänettömäd, iänettömät', 57=>'iänettömäd, iänettömät', 24=>'iänettömiden', 22=>'iänettömid', 279=>'iänettömin, iänettöminny', 59=>'iänettömiks, iänettömikse', 64=>'iänettömitä', 23=>'iänettömis', 60=>'iänettömis, iänettömispiäi', 61=>'iänettömih', 25=>'iänettömil', 62=>'iänettömil, iänettömilpiäi', 63=>'iänettömil, iänettömile', 65=>'iänettömineh', 66=>'iänettömiči', 281=>'iänettömin', 18=>'iänettömillyö, iänettömillöh, iänettömillöö', 67=>'iänettömissuai', 68=>'iänettömihpiäi', ],
	    67102 => [1=>'dänöi', 56=>'dänöi, dänöin', 3=>'dänöin', 4=>'dänöid', 277=>'dänöin, dänöinny', 5=>'dänöiks, dänöikse', 6=>'dänöitä', 8=>'dänöis', 9=>'dänöis, dänöispiäi', 10=>'dänöih', 11=>'dänöil', 12=>'dänöil, dänöilpiäi', 13=>'dänöil, dänöile', 14=>'dänöinke', 15=>'dänöiči', 17=>'dänöillyö, dänöillöh, dänöillöö', 16=>'dänöissuai', 19=>'dänöihpiäi', 2=>'dänöid, dänöit', 57=>'dänöid, dänöit', 24=>'dänölyöiden, dänölöiden', 22=>'dänölyöid, dänölöid', 279=>'dänölyöin, dänölyöinny, dänölöin, dänölöinny', 59=>'dänölyöiks, dänölyöikse, dänölöiks, dänölöikse', 64=>'dänölyöitä, dänölöitä', 23=>'dänölyöis, dänölöis', 60=>'dänölyöis, dänölyöispiäi, dänölöis, dänölöispiäi', 61=>'dänölyöih, dänölöih', 25=>'dänölyöil, dänölöil', 62=>'dänölyöil, dänölyöilpiäi, dänölöil, dänölöilpiäi', 63=>'dänölyöil, dänölyöile, dänölöil, dänölöile', 65=>'dänölyöineh, dänölöineh', 66=>'dänölyöiči, dänölöiči', 281=>'dänölyöin, dänölöin', 18=>'dänölyöillyö, dänölyöillöh, dänölyöillöö, dänölöillyö, dänölöillöh, dänölöillöö', 67=>'dänölyöissuai, dänölöissuai', 68=>'dänölyöihpiäi, dänölöihpiäi', ],
	    69938 => [1=>'dänyöi', 56=>'dänyöi, dänyöin', 3=>'dänyöin', 4=>'dänyöid', 277=>'dänyöin, dänyöinny', 5=>'dänyöiks, dänyöikse', 6=>'dänyöitä', 8=>'dänyöis', 9=>'dänyöis, dänyöispiäi', 10=>'dänyöih', 11=>'dänyöil', 12=>'dänyöil, dänyöilpiäi', 13=>'dänyöil, dänyöile', 14=>'dänyöinke', 15=>'dänyöiči', 17=>'dänyöillyö, dänyöillöh, dänyöillöö', 16=>'dänyöissuai', 19=>'dänyöihpiäi', 2=>'dänyöid, dänyöit', 57=>'dänyöid, dänyöit', 24=>'dänyölyöiden, dänyölöiden', 22=>'dänyölyöid, dänyölöid', 279=>'dänyölyöin, dänyölyöinny, dänyölöin, dänyölöinny', 59=>'dänyölyöiks, dänyölyöikse, dänyölöiks, dänyölöikse', 64=>'dänyölyöitä, dänyölöitä', 23=>'dänyölyöis, dänyölöis', 60=>'dänyölyöis, dänyölyöispiäi, dänyölöis, dänyölöispiäi', 61=>'dänyölyöih, dänyölöih', 25=>'dänyölyöil, dänyölöil', 62=>'dänyölyöil, dänyölyöilpiäi, dänyölöil, dänyölöilpiäi', 63=>'dänyölyöil, dänyölyöile, dänyölöil, dänyölöile', 65=>'dänyölyöineh, dänyölöineh', 66=>'dänyölyöiči, dänyölöiči', 281=>'dänyölyöin, dänyölöin', 18=>'dänyölyöillyö, dänyölyöillöh, dänyölyöillöö, dänyölöillyö, dänyölöillöh, dänyölöillöö', 67=>'dänyölyöissuai, dänyölöissuai', 68=>'dänyölyöihpiäi, dänyölöihpiäi', ],
	    69883 => [1=>'tiäi', 56=>'tiäi, tiäin', 3=>'tiäin', 4=>'tiäid', 277=>'tiäin, tiäinny', 5=>'tiäiks, tiäikse', 6=>'tiäitä', 8=>'tiäis', 9=>'tiäis, tiäispiäi', 10=>'tiäih', 11=>'tiäil', 12=>'tiäil, tiäilpiäi', 13=>'tiäil, tiäile', 14=>'tiäinke', 15=>'tiäiči', 17=>'tiäillyö, tiäillöh, tiäillöö', 16=>'tiäissuai', 19=>'tiäihpiäi', 2=>'tiäid, tiäit', 57=>'tiäid, tiäit', 24=>'tiäilyöiden', 22=>'tiäilyöid', 279=>'tiäilyöin, tiäilyöinny', 59=>'tiäilyöiks, tiäilyöikse', 64=>'tiäilyöitä', 23=>'tiäilyöis', 60=>'tiäilyöis, tiäilyöispiäi', 61=>'tiäilyöih', 25=>'tiäilyöil', 62=>'tiäilyöil, tiäilyöilpiäi', 63=>'tiäilyöil, tiäilyöile', 65=>'tiäilyöineh', 66=>'tiäilyöiči', 281=>'tiäilyöin', 18=>'tiäilyöillyö, tiäilyöillöh, tiäilyöillöö', 67=>'tiäilyöissuai', 68=>'tiäilyöihpiäi', ],
	    3540 => [1=>'pedäi', 56=>'pedäi, pedäjän', 3=>'pedäjän', 4=>'pedäjäd', 277=>'pedäjän, pedäjänny', 5=>'pedäjäks, pedäjäkse', 6=>'pedäjätä', 8=>'pedäjäs', 9=>'pedäjäs, pedäjäspiäi', 10=>'pedäjäh', 11=>'pedäjäl', 12=>'pedäjäl, pedäjälpiäi', 13=>'pedäjäl, pedäjäle', 14=>'pedäjänke', 15=>'pedäjäči', 17=>'pedäjällyö, pedäjällöh, pedäjällöö', 16=>'pedäjässuai', 19=>'pedäjähpiäi', 2=>'pedäjäd, pedäjät', 57=>'pedäjäd, pedäjät', 24=>'pedäjiden', 22=>'pedäjid', 279=>'pedäjin, pedäjinny', 59=>'pedäjiks, pedäjikse', 64=>'pedäjitä', 23=>'pedäjis', 60=>'pedäjis, pedäjispiäi', 61=>'pedäjih', 25=>'pedäjil', 62=>'pedäjil, pedäjilpiäi', 63=>'pedäjil, pedäjile', 65=>'pedäjineh', 66=>'pedäjiči', 281=>'pedäjin', 18=>'pedäjillyö, pedäjillöh, pedäjillöö', 67=>'pedäjissuai', 68=>'pedäjihpiäi', ],
	    51763 => [1=>'lyhyd', 56=>'lyhyd, lyhydän', 3=>'lyhydän', 4=>'lyhytte', 277=>'lyhydän, lyhydänny', 5=>'lyhydäks, lyhydäkse', 6=>'lyhydätä', 8=>'lyhydäs', 9=>'lyhydäs, lyhydäspiäi', 10=>'lyhydäh', 11=>'lyhydäl', 12=>'lyhydäl, lyhydälpiäi', 13=>'lyhydäl, lyhydäle', 14=>'lyhydänke', 15=>'lyhydäči', 17=>'lyhydällyö, lyhydällöh, lyhydällöö', 16=>'lyhydässuai', 19=>'lyhydähpiäi', 2=>'lyhydäd, lyhydät', 57=>'lyhydäd, lyhydät', 24=>'lyhydiden', 22=>'lyhydid', 279=>'lyhydin, lyhydinny', 59=>'lyhydiks, lyhydikse', 64=>'lyhyditä', 23=>'lyhydis', 60=>'lyhydis, lyhydispiäi', 61=>'lyhydih', 25=>'lyhydil', 62=>'lyhydil, lyhydilpiäi', 63=>'lyhydil, lyhydile', 65=>'lyhydineh', 66=>'lyhydiči', 281=>'lyhydin', 18=>'lyhydillyö, lyhydillöh, lyhydillöö', 67=>'lyhydissuai', 68=>'lyhydihpiäi', ],
	    18330 => [1=>'pereh', 56=>'pereh, perehen', 3=>'perehen', 4=>'perehte', 277=>'perehen, perehenny', 5=>'pereheks, perehekse', 6=>'perehetä', 8=>'perehes', 9=>'perehes, perehespiäi', 10=>'pereheh', 11=>'perehel', 12=>'perehel, perehelpiäi', 13=>'perehel, perehele', 14=>'perehenke', 15=>'pereheči', 17=>'perehellyö, perehellöh, perehellöö', 16=>'perehessuai', 19=>'perehehpiäi', 2=>'perehed, perehet', 57=>'perehed, perehet', 24=>'perehiden', 22=>'perehid', 279=>'perehin, perehinny', 59=>'perehiks, perehikse', 64=>'perehitä', 23=>'perehis', 60=>'perehis, perehispiäi', 61=>'perehih', 25=>'perehil', 62=>'perehil, perehilpiäi', 63=>'perehil, perehile', 65=>'perehineh', 66=>'perehiči', 281=>'perehin', 18=>'perehillyö, perehillöh, perehillöö', 67=>'perehissuai', 68=>'perehihpiäi', ],
	    49174 => [1=>'petkel', 56=>'petkel, petkelen', 3=>'petkelen', 4=>'petkelte', 277=>'petkelen, petkelenny', 5=>'petkeleks, petkelekse', 6=>'petkeletä', 8=>'petkeles', 9=>'petkeles, petkelespiäi', 10=>'petkeleh', 11=>'petkelel', 12=>'petkelel, petkelelpiäi', 13=>'petkelel, petkelele', 14=>'petkelenke', 15=>'petkeleči', 17=>'petkelellyö, petkelellöh, petkelellöö', 16=>'petkelessuai', 19=>'petkelehpiäi', 2=>'petkeled, petkelet', 57=>'petkeled, petkelet', 24=>'petkeliden', 22=>'petkelid', 279=>'petkelin, petkelinny', 59=>'petkeliks, petkelikse', 64=>'petkelitä', 23=>'petkelis', 60=>'petkelis, petkelispiäi', 61=>'petkelih', 25=>'petkelil', 62=>'petkelil, petkelilpiäi', 63=>'petkelil, petkelile', 65=>'petkelineh', 66=>'petkeliči', 281=>'petkelin', 18=>'petkelillyö, petkelillöh, petkelillöö', 67=>'petkelissuai', 68=>'petkelihpiäi', ],
	    46942 => [1=>'paimen', 56=>'paimen, paimenen', 3=>'paimenen', 4=>'paimente', 277=>'paimenen, paimenennu', 5=>'paimeneks, paimenekse', 6=>'paimeneta', 8=>'paimenes', 9=>'paimenes, paimenespiäi', 10=>'paimeneh', 11=>'paimenel', 12=>'paimenel, paimenelpiäi', 13=>'paimenel, paimenele', 14=>'paimenenke', 15=>'paimeneči', 17=>'paimenelloh, paimenelloo, paimenelluo', 16=>'paimenessuai', 19=>'paimenehpiäi', 2=>'paimened, paimenet', 57=>'paimened, paimenet', 24=>'paimeniden', 22=>'paimenid', 279=>'paimenin, paimeninnu', 59=>'paimeniks, paimenikse', 64=>'paimenita', 23=>'paimenis', 60=>'paimenis, paimenispiäi', 61=>'paimenih', 25=>'paimenil', 62=>'paimenil, paimenilpiäi', 63=>'paimenil, paimenile', 65=>'paimenineh', 66=>'paimeniči', 281=>'paimenin', 18=>'paimenilloh, paimenilloo, paimenilluo', 67=>'paimenissuai', 68=>'paimenihpiäi', ],
	    70254 => [1=>'härkin', 56=>'härkimen, härkin', 3=>'härkimen', 4=>'härkinte', 277=>'härkimen, härkimenny', 5=>'härkimeks, härkimekse', 6=>'härkimetä', 8=>'härkimes', 9=>'härkimes, härkimespiäi', 10=>'härkimeh', 11=>'härkimel', 12=>'härkimel, härkimelpiäi', 13=>'härkimel, härkimele', 14=>'härkimenke', 15=>'härkimeči', 17=>'härkimellyö, härkimellöh, härkimellöö', 16=>'härkimessuai', 19=>'härkimehpiäi', 2=>'härkimed, härkimet', 57=>'härkimed, härkimet', 24=>'härkimiden', 22=>'härkimid', 279=>'härkimin, härkiminny', 59=>'härkimiks, härkimikse', 64=>'härkimitä', 23=>'härkimis', 60=>'härkimis, härkimispiäi', 61=>'härkimih', 25=>'härkimil', 62=>'härkimil, härkimilpiäi', 63=>'härkimil, härkimile', 65=>'härkimineh', 66=>'härkimiči', 281=>'härkimin', 18=>'härkimillyö, härkimillöh, härkimillöö', 67=>'härkimissuai', 68=>'härkimihpiäi', ],
	    28825 => [1=>'tytär', 56=>'tyttären, tytär', 3=>'tyttären', 4=>'tytärte', 277=>'tyttären, tyttärenny', 5=>'tyttäreks, tyttärekse', 6=>'tyttäretä', 8=>'tyttäres', 9=>'tyttäres, tyttärespiäi', 10=>'tyttäreh', 11=>'tyttärel', 12=>'tyttärel, tyttärelpiäi', 13=>'tyttärel, tyttärele', 14=>'tyttärenke', 15=>'tyttäreči', 17=>'tyttärellyö, tyttärellöh, tyttärellöö', 16=>'tyttäressuai', 19=>'tyttärehpiäi', 2=>'tyttäred, tyttäret', 57=>'tyttäred, tyttäret', 24=>'tyttäriden', 22=>'tyttärid', 279=>'tyttärin, tyttärinny', 59=>'tyttäriks, tyttärikse', 64=>'tyttäritä', 23=>'tyttäris', 60=>'tyttäris, tyttärispiäi', 61=>'tyttärih', 25=>'tyttäril', 62=>'tyttäril, tyttärilpiäi', 63=>'tyttäril, tyttärile', 65=>'tyttärineh', 66=>'tyttäriči', 281=>'tyttärin', 18=>'tyttärillyö, tyttärillöh, tyttärillöö', 67=>'tyttärissuai', 68=>'tyttärihpiäi', ],
	    70269 => [1=>'lapsut', 56=>'lapsuden, lapsut', 3=>'lapsuden', 4=>'lapsutte', 277=>'lapsuden, lapsudennu', 5=>'lapsudeks, lapsudekse', 6=>'lapsudeta', 8=>'lapsudes', 9=>'lapsudes, lapsudespiäi', 10=>'lapsudeh', 11=>'lapsudel', 12=>'lapsudel, lapsudelpiäi', 13=>'lapsudel, lapsudele', 14=>'lapsudenke', 15=>'lapsudeči', 17=>'lapsudelloh, lapsudelloo, lapsudelluo', 16=>'lapsudessuai', 19=>'lapsudehpiäi', 2=>'lapsuded, lapsudet', 57=>'lapsuded, lapsudet', 24=>'lapsuziden', 22=>'lapsuzid', 279=>'lapsuzin, lapsuzinnu', 59=>'lapsuziks, lapsuzikse', 64=>'lapsuzita', 23=>'lapsuzis', 60=>'lapsuzis, lapsuzispiäi', 61=>'lapsuzih', 25=>'lapsuzil', 62=>'lapsuzil, lapsuzilpiäi', 63=>'lapsuzil, lapsuzile', 65=>'lapsuzineh', 66=>'lapsuziči', 281=>'lapsuzin', 18=>'lapsuzilloh, lapsuzilloo, lapsuzilluo', 67=>'lapsuzissuai', 68=>'lapsuzihpiäi', ],
	    40672 => [1=>'barbaz', 56=>'barbahan, barbaz', 3=>'barbahan', 4=>'barbaste', 277=>'barbahan, barbahannu', 5=>'barbahaks, barbahakse', 6=>'barbahata', 8=>'barbahas', 9=>'barbahas, barbahaspiäi', 10=>'barbahah', 11=>'barbahal', 12=>'barbahal, barbahalpiäi', 13=>'barbahal, barbahale', 14=>'barbahanke', 15=>'barbahači', 17=>'barbahalloh, barbahalloo, barbahalluo', 16=>'barbahassuai', 19=>'barbahahpiäi', 2=>'barbahad, barbahat', 57=>'barbahad, barbahat', 24=>'barbahiden', 22=>'barbahid', 279=>'barbahin, barbahinnu', 59=>'barbahiks, barbahikse', 64=>'barbahita', 23=>'barbahis', 60=>'barbahis, barbahispiäi', 61=>'barbahih', 25=>'barbahil', 62=>'barbahil, barbahilpiäi', 63=>'barbahil, barbahile', 65=>'barbahineh', 66=>'barbahiči', 281=>'barbahin', 18=>'barbahilloh, barbahilloo, barbahilluo', 67=>'barbahissuai', 68=>'barbahihpiäi', ],
	    70262 => [1=>'mätäz', 56=>'mättähän, mätäz', 3=>'mättähän', 4=>'mätäste', 277=>'mättähän, mättähänny', 5=>'mättähäks, mättähäkse', 6=>'mättähätä', 8=>'mättähäs', 9=>'mättähäs, mättähäspiäi', 10=>'mättähäh', 11=>'mättähäl', 12=>'mättähäl, mättähälpiäi', 13=>'mättähäl, mättähäle', 14=>'mättähänke', 15=>'mättähäči', 17=>'mättähällyö, mättähällöh, mättähällöö', 16=>'mättähässuai', 19=>'mättähähpiäi', 2=>'mättähäd, mättähät', 57=>'mättähäd, mättähät', 24=>'mättähiden', 22=>'mättähid', 279=>'mättähin, mättähinny', 59=>'mättähiks, mättähikse', 64=>'mättähitä', 23=>'mättähis', 60=>'mättähis, mättähispiäi', 61=>'mättähih', 25=>'mättähil', 62=>'mättähil, mättähilpiäi', 63=>'mättähil, mättähile', 65=>'mättähineh', 66=>'mättähiči', 281=>'mättähin', 18=>'mättähillyö, mättähillöh, mättähillöö', 67=>'mättähissuai', 68=>'mättähihpiäi', ],
	    47157 => [1=>'kirvez', 56=>'kirvehen, kirvez', 3=>'kirvehen', 4=>'kirveste', 277=>'kirvehen, kirvehenny', 5=>'kirveheks, kirvehekse', 6=>'kirvehetä', 8=>'kirvehes', 9=>'kirvehes, kirvehespiäi', 10=>'kirveheh', 11=>'kirvehel', 12=>'kirvehel, kirvehelpiäi', 13=>'kirvehel, kirvehele', 14=>'kirvehenke', 15=>'kirveheči', 17=>'kirvehellyö, kirvehellöh, kirvehellöö', 16=>'kirvehessuai', 19=>'kirvehehpiäi', 2=>'kirvehed, kirvehet', 57=>'kirvehed, kirvehet', 24=>'kirvehiden', 22=>'kirvehid', 279=>'kirvehin, kirvehinny', 59=>'kirvehiks, kirvehikse', 64=>'kirvehitä', 23=>'kirvehis', 60=>'kirvehis, kirvehispiäi', 61=>'kirvehih', 25=>'kirvehil', 62=>'kirvehil, kirvehilpiäi', 63=>'kirvehil, kirvehile', 65=>'kirvehineh', 66=>'kirvehiči', 281=>'kirvehin', 18=>'kirvehillyö, kirvehillöh, kirvehillöö', 67=>'kirvehissuai', 68=>'kirvehihpiäi', ],
	    46865 => [1=>'verez', 56=>'vereksen, verez', 3=>'vereksen', 4=>'vereste', 277=>'vereksen, vereksenny', 5=>'verekseks, vereksekse', 6=>'vereksetä', 8=>'verekses', 9=>'verekses, vereksespiäi', 10=>'verekseh', 11=>'vereksel', 12=>'vereksel, verekselpiäi', 13=>'vereksel, vereksele', 14=>'vereksenke', 15=>'verekseči', 17=>'vereksellyö, vereksellöh, vereksellöö', 16=>'vereksessuai', 19=>'vereksehpiäi', 2=>'vereksed, verekset', 57=>'vereksed, verekset', 24=>'vereksiden', 22=>'vereksid', 279=>'vereksin, vereksinny', 59=>'vereksiks, vereksikse', 64=>'vereksitä', 23=>'vereksis', 60=>'vereksis, vereksispiäi', 61=>'vereksih', 25=>'vereksil', 62=>'vereksil, vereksilpiäi', 63=>'vereksil, vereksile', 65=>'vereksineh', 66=>'vereksiči', 281=>'vereksin', 18=>'vereksillyö, vereksillöh, vereksillöö', 67=>'vereksissuai', 68=>'vereksihpiäi', ],
	    46862 => [1=>'veres', 56=>'vereksen, veres', 3=>'vereksen', 4=>'vereste', 277=>'vereksen, vereksenny', 5=>'verekseks, vereksekse', 6=>'vereksetä', 8=>'verekses', 9=>'verekses, vereksespiäi', 10=>'verekseh', 11=>'vereksel', 12=>'vereksel, verekselpiäi', 13=>'vereksel, vereksele', 14=>'vereksenke', 15=>'verekseči', 17=>'vereksellyö, vereksellöh, vereksellöö', 16=>'vereksessuai', 19=>'vereksehpiäi', 2=>'vereksed, verekset', 57=>'vereksed, verekset', 24=>'vereksiden', 22=>'vereksid', 279=>'vereksin, vereksinny', 59=>'vereksiks, vereksikse', 64=>'vereksitä', 23=>'vereksis', 60=>'vereksis, vereksispiäi', 61=>'vereksih', 25=>'vereksil', 62=>'vereksil, vereksilpiäi', 63=>'vereksil, vereksile', 65=>'vereksineh', 66=>'vereksiči', 281=>'vereksin', 18=>'vereksillyö, vereksillöh, vereksillöö', 67=>'vereksissuai', 68=>'vereksihpiäi', ],
	    28730 => [1=>'kaglus', 56=>'kagluksen, kaglus', 3=>'kagluksen', 4=>'kagluste', 277=>'kagluksen, kagluksennu', 5=>'kaglukseks, kagluksekse', 6=>'kaglukseta', 8=>'kaglukses', 9=>'kaglukses, kagluksespiäi', 10=>'kaglukseh', 11=>'kagluksel', 12=>'kagluksel, kaglukselpiäi', 13=>'kagluksel, kagluksele', 14=>'kagluksenke', 15=>'kaglukseči', 17=>'kaglukselloh, kaglukselloo, kaglukselluo', 16=>'kagluksessuai', 19=>'kagluksehpiäi', 2=>'kagluksed, kaglukset', 57=>'kagluksed, kaglukset', 24=>'kagluksiden', 22=>'kagluksid', 279=>'kagluksin, kagluksinnu', 59=>'kagluksiks, kagluksikse', 64=>'kagluksita', 23=>'kagluksis', 60=>'kagluksis, kagluksispiäi', 61=>'kagluksih', 25=>'kagluksil', 62=>'kagluksil, kagluksilpiäi', 63=>'kagluksil, kagluksile', 65=>'kagluksineh', 66=>'kagluksiči', 281=>'kagluksin', 18=>'kagluksilloh, kagluksilloo, kagluksilluo', 67=>'kagluksissuai', 68=>'kagluksihpiäi', ],
	    40633 => [1=>'kynäbrys', 56=>'kynäbryksen, kynäbrys', 3=>'kynäbryksen', 4=>'kynäbryste', 277=>'kynäbryksen, kynäbryksenny', 5=>'kynäbrykseks, kynäbryksekse', 6=>'kynäbryksetä', 8=>'kynäbrykses', 9=>'kynäbrykses, kynäbryksespiäi', 10=>'kynäbrykseh', 11=>'kynäbryksel', 12=>'kynäbryksel, kynäbrykselpiäi', 13=>'kynäbryksel, kynäbryksele', 14=>'kynäbryksenke', 15=>'kynäbrykseči', 17=>'kynäbryksellyö, kynäbryksellöh, kynäbryksellöö', 16=>'kynäbryksessuai', 19=>'kynäbryksehpiäi', 2=>'kynäbryksed, kynäbrykset', 57=>'kynäbryksed, kynäbrykset', 24=>'kynäbryksiden', 22=>'kynäbryksid', 279=>'kynäbryksin, kynäbryksinny', 59=>'kynäbryksiks, kynäbryksikse', 64=>'kynäbryksitä', 23=>'kynäbryksis', 60=>'kynäbryksis, kynäbryksispiäi', 61=>'kynäbryksih', 25=>'kynäbryksil', 62=>'kynäbryksil, kynäbryksilpiäi', 63=>'kynäbryksil, kynäbryksile', 65=>'kynäbryksineh', 66=>'kynäbryksiči', 281=>'kynäbryksin', 18=>'kynäbryksillyö, kynäbryksillöh, kynäbryksillöö', 67=>'kynäbryksissuai', 68=>'kynäbryksihpiäi', ],
	    70267 => [1=>'vahnuz', 56=>'vahnuden, vahnuz', 3=>'vahnuden', 4=>'vahnutte', 277=>'vahnuden, vahnudennu', 5=>'vahnudeks, vahnudekse', 6=>'vahnudeta', 8=>'vahnudes', 9=>'vahnudes, vahnudespiäi', 10=>'vahnudeh', 11=>'vahnudel', 12=>'vahnudel, vahnudelpiäi', 13=>'vahnudel, vahnudele', 14=>'vahnudenke', 15=>'vahnudeči', 17=>'vahnudelloh, vahnudelloo, vahnudelluo', 16=>'vahnudessuai', 19=>'vahnudehpiäi', 2=>'vahnuded, vahnudet', 57=>'vahnuded, vahnudet', 24=>'vahnuziden', 22=>'vahnuzid', 279=>'vahnuzin, vahnuzinnu', 59=>'vahnuziks, vahnuzikse', 64=>'vahnuzita', 23=>'vahnuzis', 60=>'vahnuzis, vahnuzispiäi', 61=>'vahnuzih', 25=>'vahnuzil', 62=>'vahnuzil, vahnuzilpiäi', 63=>'vahnuzil, vahnuzile', 65=>'vahnuzineh', 66=>'vahnuziči', 281=>'vahnuzin', 18=>'vahnuzilloh, vahnuzilloo, vahnuzilluo', 67=>'vahnuzissuai', 68=>'vahnuzihpiäi', ],
	    66796 => [1=>'hyvyz', 56=>'hyvyden, hyvyz', 3=>'hyvyden', 4=>'hyvytte', 277=>'hyvyden, hyvydenny', 5=>'hyvydeks, hyvydekse', 6=>'hyvydetä', 8=>'hyvydes', 9=>'hyvydes, hyvydespiäi', 10=>'hyvydeh', 11=>'hyvydel', 12=>'hyvydel, hyvydelpiäi', 13=>'hyvydel, hyvydele', 14=>'hyvydenke', 15=>'hyvydeči', 17=>'hyvydellyö, hyvydellöh, hyvydellöö', 16=>'hyvydessuai', 19=>'hyvydehpiäi', 2=>'hyvyded, hyvydet', 57=>'hyvyded, hyvydet', 24=>'hyvyziden', 22=>'hyvyzid', 279=>'hyvyzin, hyvyzinny', 59=>'hyvyziks, hyvyzikse', 64=>'hyvyzitä', 23=>'hyvyzis', 60=>'hyvyzis, hyvyzispiäi', 61=>'hyvyzih', 25=>'hyvyzil', 62=>'hyvyzil, hyvyzilpiäi', 63=>'hyvyzil, hyvyzile', 65=>'hyvyzineh', 66=>'hyvyziči', 281=>'hyvyzin', 18=>'hyvyzillyö, hyvyzillöh, hyvyzillöö', 67=>'hyvyzissuai', 68=>'hyvyzihpiäi', ],
	];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateTverNames() {
        $lang_id = 4;
        $pos_id = 5;
        $dialect_id = 47;
        $num = NULL;
	$templates = [
	    1 => 'mua []',
	    2 => 'lyhy|t [ö, t]',
	    3 => 'ai|ga [ja]',
	    4 => 've|zi [je/de, t]',
	    5 => 'kat|e [tie, et]',
	    6 => 'ahkiv|o [o]',
	];

        foreach ($templates as $lemma_id=>$template) {
            $result[$lemma_id] = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
        }

	$expected = [
	    1 => [0 => [0 => 'mua', 1 => 'mua', 2 => 'mua', 3 => 'muada', 4 => 'mualoi', 5 => 'mualoi', 6 => 'mua', 10 => true], 1 => null, 2 => 'mua', 3 => ''],
	    2 => [0 => [0 => 'lyhyt', 1 => 'lyhyö', 2 => 'lyhyö', 3 => 'lyhyttä', 4 => 'lyhyzi', 5 => 'lyhyzi', 6 => 'lyhyt', 10 => false], 1 => null, 2 => 'lyhy', 3 => 't'],
            3 => [0 => [0 => 'aiga', 1 => 'aija', 2 => 'aiga', 3 => 'aigua', 4 => 'aijoi', 5 => 'aigoi', 6 => 'aiga', 10 => true], 1 => null, 2 => 'ai', 3 => 'ga'],
	    4 => [0 => [0 => 'vezi', 1 => 'veje', 2 => 'vede', 3 => 'vettä', 4 => 'vezilöi', 5 => 'vezilöi', 6 => 'vet', 10 => false], 1 => null, 2 => 've', 3 => 'zi'],
	    5 => [0 => [0 => 'kate', 1 => 'kattie', 2 => 'kattie', 3 => 'katetta', 4 => 'kattieloi', 5 => 'kattieloi', 6 => 'katet', 10 => true], 1 => null, 2 => 'kat', 3 => 'e'],
	    6 => [0 => [0 => 'ahkivo', 1 => 'ahkivo', 2 => 'ahkivo', 3 => 'ahkivuo', 4 => 'ahkivoloi', 5 => 'ahkivoloi', 6 => 'ahkivo', 10 => true], 1 => null, 2 => 'ahkiv', 3 => 'o'],
	];
        $this->assertEquals( $expected, $result);        
    }
*/
    public function testStemsFromMiniTemplateForLud() {
        $lang_id = 6;
        $dialect_id = 42;
        $pos_id = 11; // verb
        $num = NULL;
        $templates = [
	    3461 => 'kač|čoda [o]',
/*	    42494 => 'kuč|čuda [u]',
	    50380 => 'kyzy|dä []',
	    41301 => 'eč|čidä [i]',
	    22172 => 'an|dada [da]',
	    14596 => 'ot|tada [a]',
	    14594 => 'el|ädä [ä]',
	    45142 => 'itk|ei [e]',
	    43596 => 'särb|äi [ä]',
	    29444 => 'd’|uoda [uo]',
	    62863 => 'v|iedä [ie]',
	    41336 => 'sua|da []',
	    29594 => 'tul|da [e]',
	    3525 => 'män|dä [e]',
	    67094 => 'pur|da [e]',
	    22260 => 'p|agišta [agiže]',
	    44615 => 'pe|stä [ze]',
	    43235 => 'maga|ta [da]',
	    41869 => 'rube|ta [da]',
	    70904 => 'haravoi|ta [če]',
	    62330 => 'suvai|ta [če]',*/
	];        
        $result = [];
        foreach ($templates as $lemma_id=>$template) {
            $result[$lemma_id] = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num, $dialect_id);
        }
	$expected = [
	    3461 => [0 => [0 => 'kaččoda', 1 => 'kačo', 2 => 'kaččo', 3 => 'kačoin, kačuoi', 4 => 'kaččoi/kaččuoi', 5 => 'kaččo', 6 => 'kačota', 10 => true], 1 => null, 2 => 'kač', 3 => 'čoda'],
/*	    42494 => [0 => [0 => 'kuččuda', 1 => 'kuču', 2 => 'kučču', 3 => 'kučui', 4 => 'kuččui', 5 => 'kučču', 6 => 'kučuta', 10 => true], 1 => null, 2 => 'kuč', 3 => 'čuda'],
	    50380 => [0 => [0 => 'kyzydä', 1 => 'kyzy', 2 => 'kyzy', 3 => 'kyzyi', 4 => 'kyzyi', 5 => 'kyzy', 6 => 'kyzytä', 10 => false], 1 => null, 2 => 'kyzy', 3 => 'dä'],
	    41301 => [0 => [0 => 'eččidä', 1 => 'eči', 2 => 'ečči', 3 => 'ečii', 4 => 'eččii', 5 => 'ečči', 6 => 'ečitä', 10 => false], 1 => null, 2 => 'eč', 3 => 'čidä'],
	    22172 => [0 => [0 => 'andada', 1 => 'anda', 2 => 'anda', 3 => 'andoin, anduoi', 4 => 'andoi/anduoi', 5 => 'anda', 6 => 'andeta', 10 => true], 1 => null, 2 => 'an', 3 => 'dada'],
	    14596 => [0 => [0 => 'ottada', 1 => 'ota', 2 => 'otta', 3 => 'oti', 4 => 'otti', 5 => 'otta', 6 => 'oteta', 10 => true], 1 => null, 2 => 'ot', 3 => 'tada'],
	    14594 => [0 => [0 => 'dä', 1 => 'elä', 2 => 'elä', 3 => 'eli', 4 => 'eli', 5 => 'elä', 6 => 'eletä', 10 => false], 1 => null, 2 => 'el', 3 => 'ädä'],
	    45142 => [0 => [0 => 'itkei', 1 => 'itke', 2 => 'itke', 3 => 'itki', 4 => 'itki', 5 => 'itke', 6 => 'itketä', 10 => false], 1 => null, 2 => 'itk', 3 => 'ei'],
	    43596 => [0 => [0 => 'särbäi', 1 => 'särbä', 2 => 'särbä', 3 => 'särbi', 4 => 'särbi', 5 => 'särbä', 6 => 'särbetä', 10 => false], 1 => null, 2 => 'särb', 3 => 'äi'],
	    29444 => [0 => [0 => 'd’uoda', 1 => 'd’uo', 2 => 'd’uo', 3 => 'd’oin, d’uoi', 4 => 'd’oi/d’uoi', 5 => 'd’uo', 6 => 'd’uoda', 10 => true], 1 => null, 2 => 'd’', 3 => 'uoda'],
	    62863 => [0 => [0 => 'viedä', 1 => 'vie', 2 => 'vie', 3 => 'vein, viei', 4 => 'vei/viei', 5 => 'vie', 6 => 'viedä', 10 => false], 1 => null, 2 => 'v', 3 => 'iedä'],
	    41336 => [0 => [0 => 'suada', 1 => 'sua', 2 => 'sua', 3 => 'sain, suai', 4 => 'sai/suai', 5 => 'sua', 6 => 'suada', 10 => true], 1 => null, 2 => 'sua', 3 => 'da'],
	    29594 => [0 => [0 => 'tulda', 1 => 'tule', 2 => 'tule', 3 => 'tuli', 4 => 'tuli', 5 => 'tul', 6 => 'tulda', 10 => true], 1 => null, 2 => 'tul', 3 => 'da'],
	    3525 => [0 => [0 => 'mändä', 1 => 'mäne', 2 => 'mäne', 3 => 'mäni', 4 => 'mäni', 5 => 'män', 6 => 'mändä', 10 => false], 1 => null, 2 => 'män', 3 => 'dä'],
	    67094 => [0 => [0 => 'purda', 1 => 'pure', 2 => 'pure', 3 => 'puri', 4 => 'puri', 5 => 'pur', 6 => 'purda', 10 => true], 1 => null, 2 => 'pur', 3 => 'da'],
	    22260 => [0 => [0 => 'pagišta', 1 => 'pagiže', 2 => 'pagiže', 3 => 'pagiži', 4 => 'pagiži', 5 => 'pagiš', 6 => 'pagišta', 10 => true], 1 => null, 2 => 'p', 3 => 'agišta'],
	    44615 => [0 => [0 => 'pestä', 1 => 'peze', 2 => 'peze', 3 => 'pezi', 4 => 'pezi', 5 => 'pes', 6 => 'pestä', 10 => false], 1 => null, 2 => 'pe', 3 => 'stä'],
	    43235 => [0 => [0 => 'magata', 1 => 'magada', 2 => 'magada', 3 => 'magaži', 4 => 'magaži', 5 => 'magan', 6 => 'magata', 10 => true], 1 => null, 2 => 'maga', 3 => 'ta'],
	    41869 => [0 => [0 => 'rubeta', 1 => 'rubeda', 2 => 'rubeda', 3 => 'rubeži', 4 => 'rubeži', 5 => 'ruben', 6 => 'rubeta', 10 => true], 1 => null, 2 => 'rube', 3 => 'ta'],
	    70904 => [0 => [0 => 'haravoita', 1 => 'haravoiče', 2 => 'haravoičče', 3 => 'haravoiči', 4 => 'haravoičči', 5 => 'haravoin', 6 => 'haravoita', 10 => true], 1 => null, 2 => 'haravoi', 3 => 'ta'],
	    62330 => [0 => [0 => 'suvaita', 1 => 'suvaiče', 2 => 'suvaičče', 3 => 'suvaiči', 4 => 'suvaičči', 5 => 'suvain', 6 => 'suvaita', 10 => true], 1 => null, 2 => 'suvai', 3 => 'ta'],
*/	];
        $this->assertEquals( $expected, $result);        
    }
/*    
    public function testWordformsByStemsLudVerb() {
        $lang_id = 6;
        $pos_id = 11;
        $name_num=null;
        $dialect_id=42;
        $is_reflexive = false;
        $templates = [
	    3461 => 'kač|čoda [o]',
	    42494 => 'kuč|čuda [u]',
	    50380 => 'kyzy|dä []',
	    41301 => 'eč|čidä [i]',
	    22172 => 'an|dada [da]',
	    14596 => 'ot|tada [a]',
	    14594 => 'el|ädä [ä]',
	    45142 => 'itk|ei [e]',
	    43596 => 'särb|äi [ä]',
	    29444 => 'd’|uoda [uo]',
	    62863 => 'v|iedä [ie]',
	    41336 => 'sua|da []',
	    29594 => 'tul|da [e]',
	    3525 => 'män|dä [e]',
	    67094 => 'pur|da [e]',
	    22260 => 'p|agišta [agiže]',
	    44615 => 'pe|stä [ze]',
	    43235 => 'maga|ta [da]',
	    41869 => 'rube|ta [da]',
	    70904 => 'haravoi|ta [če]',
	    62330 => 'suvai|ta [če]',
	];        
        $result = [];
        foreach ($templates as $lemma_id=>$template) {
            list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id, $is_reflexive);
            $result[$lemma_id] = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems, $is_reflexive);
        }

        $expected = [
	    3461 => [26=>'kačon', 27=>'kačod, kačot', 28=>'kaččou', 29=>'kačomme', 30=>'kačotte', 31=>'kačotah', 295=>'kačo', 296=>'kačota', 70=>'en kačo', 71=>'ed kačo, et kačo', 72=>'ei kačo', 73=>'emme kačo', 78=>'ette kačo', 79=>'ei kačota', 32=>'kačoin, kačuoin', 33=>'kačoid, kačoit, kačuoid, kačuoit', 34=>'kaččoi, kaččuoi', 35=>'kačoimme, kačuoimme', 36=>'kačoit, kačoitte, kačuoit, kačuoitte', 37=>'kačottih', 80=>'en kaččonu', 81=>'ed kaččonu, et kaččonu', 82=>'ei kaččonu', 83=>'emme kaččonu', 84=>'ette kaččonu', 85=>'ei kačottu', 86=>'olen kaččonu', 87=>'oled kaččonu, olet kaččonu', 88=>'on kaččonu', 89=>'olemme kaččonu', 90=>'olette kaččonu', 91=>'on kačottu', 92=>'en ole kaččonu', 93=>'ed ole kaččonu, et ole kaččonu', 94=>'ei ole kaččonu', 95=>'emme ole kaččonu', 96=>'ette ole kaččonu', 97=>'ei ole kačottu', 98=>'olin kaččonu', 99=>'olid kaččonu, olit kaččonu', 100=>'oli kaččonu', 101=>'olimme kaččonu', 102=>'olitte kaččonu', 103=>'oli kačottu', 104=>'en olnu kaččonu', 105=>'ed olnu kaččonu, et olnu kaččonu', 107=>'ei olnu kaččonu', 108=>'emme olnu kaččonu', 106=>'ette olnu kaččonu', 109=>'ei oldu kačottu', 51=>'kačo', 52=>'kaččogah', 53=>'kaččogamme', 54=>'kaččogatte', 55=>'kačotakkah', 50=>'älä kačo', 74=>'älgäh kaččogah', 75=>'älgämme kaččogamme', 76=>'älgätte kaččogatte', 77=>'äldägäh kačotakkah', 38=>'kaččoižin', 39=>'kaččoižid, kaččoižit', 40=>'kaččoiš', 41=>'kaččoižimme', 42=>'kaččoižitte', 43=>'kačotaiš', 301=>'kaččoiš', 303=>'kačotaiš', 110=>'en kaččoiš', 111=>'ed kaččoiš, et kaččoiš', 112=>'ei kaččoiš', 113=>'emme kaččoiš', 114=>'ette kaččoiš', 115=>'ei kačotaiš', 44=>'kaččonuižin', 45=>'kaččonuižid, kaččonuižit', 46=>'kaččonnuiš', 47=>'kaččonuižimme', 48=>'kaččonuižitte', 49=>'kačotannuiš', 302=>'kaččonnuiš', 304=>'kačotannuiš', 116=>'en kaččonnuiš', 117=>'ed kaččonuiš, et kaččonuiš', 118=>'ei kaččonuiš', 119=>'emme kaččonuiš', 120=>'ette kaččonuiš', 121=>'ei kačotannuiš', 122=>'oližin kaččonu', 123=>'oližid kaččonu, oližit kaččonu', 124=>'oliš kaččonu', 126=>'oližimme kaččonu', 127=>'oližitte kaččonu', 128=>'oldaiš kačottu', 129=>'en oliš kaččonu', 130=>'ed oliš kaččonu, et oliš kaččonu', 131=>'ei oliš kaččonu', 132=>'emme oliš kaččonu', 133=>'ette oliš kaččonu', 134=>'ei oldaiš kačottu', 135=>'olnuižin kaččonu', 125=>'olnuižid kaččonu, olnuižit kaččonu', 136=>'olnuiš kaččonu', 137=>'olnuižimme kaččonu', 138=>'olnuižitte kaččonu', 139=>'oldanuiš kačottu', 140=>'en olnuiš kaččonu', 141=>'ed olnuiš kaččonu, et olnuiš kaččonu', 142=>'ei olnuiš kaččonu', 143=>'emme olnuiš kaččonu', 144=>'ette olnuiš kaččonu', 145=>'ei oldanuiš kačottu', 146=>'kaččonen', 147=>'kaččoned, kaččonet', 148=>'kaččonou', 149=>'kaččonemme', 150=>'kaččonette', 151=>'kačotanneh', 310=>'kaččone', 311=>'kačotanne', 152=>'en kaččone', 153=>'ed kaččone, et kaččone', 154=>'ei kaččone', 155=>'emme kaččone', 156=>'ette kaččone', 157=>'ei kačotanne', 158=>'lienen kaččonu', 159=>'liened kaččonu, lienet kaččonu', 160=>'lienöy kaččonu', 161=>'lienemme kaččonu', 162=>'lienette kaččonu', 163=>'lienöy kačottu', 164=>'en liene kaččonu', 165=>'ed liene kaččonu, et liene kaččonu', 166=>'ei liene kaččonu', 167=>'emme liene kaččonu', 168=>'ette liene kaččonu', 169=>'ei liene kačottu', 170=>'kaččoda', 171=>'kaččodes', 172=>'kaččoden', 173=>'kaččomal', 174=>'kaččomah', 175=>'kaččomas', 176=>'kaččomas', 177=>'kaččomata, kaččomatta', 178=>'kaččoi', 179=>'kaččonu', 180=>'kačottav', 181=>'kačottu', ],
	    42494 => [26=>'kučun', 27=>'kučud, kučut', 28=>'kuččuu', 29=>'kučumme', 30=>'kučutte', 31=>'kučutah', 295=>'kuču', 296=>'kučuta', 70=>'en kuču', 71=>'ed kuču, et kuču', 72=>'ei kuču', 73=>'emme kuču', 78=>'ette kuču', 79=>'ei kučuta', 32=>'kučuin', 33=>'kučuid, kučuit', 34=>'kuččui', 35=>'kučuimme', 36=>'kučuitte', 37=>'kučuttih', 80=>'en kuččunu', 81=>'ed kuččunu, et kuččunu', 82=>'ei kuččunu', 83=>'emme kuččunu', 84=>'ette kuččunu', 85=>'ei kučuttu', 86=>'olen kuččunu', 87=>'oled kuččunu, olet kuččunu', 88=>'on kuččunu', 89=>'olemme kuččunu', 90=>'olette kuččunu', 91=>'oldah kučuttu, on kučuttu', 92=>'en ole kuččunu', 93=>'ed ole kuččunu, et ole kuččunu', 94=>'ei ole kuččunu', 95=>'emme ole kuččunu', 96=>'ette ole kuččunu', 97=>'ei olda kučuttu', 98=>'olin kuččunu', 99=>'olid kuččunu, olit kuččunu', 100=>'oli kuččunu', 101=>'olimme kuččunu', 102=>'olitte kuččunu', 103=>'oldih kučuttu, oli kučuttu', 104=>'en olnu kuččunu', 105=>'ed olnu kuččunu, et olnu kuččunu', 107=>'ei olnu kuččunu', 108=>'emme olnu kuččunu', 106=>'ette olnu kuččunu', 109=>'ei oldu kučuttu', 51=>'kuču', 52=>'kuččugah', 53=>'kuččugamme', 54=>'kuččugatte', 55=>'kučutakkah', 50=>'älä kuču', 74=>'älgäh kuččugah', 75=>'älgämme kuččugamme', 76=>'älgätte kuččugatte', 77=>'äldägäh kučutakkah', 38=>'kuččuižin', 39=>'kuččuižid, kuččuižit', 40=>'kuččuiš', 41=>'kuččuižimme', 42=>'kuččuižitte', 43=>'kučutaiš', 301=>'kuččuiš', 303=>'kučutaiš', 110=>'en kuččuiš', 111=>'ed kuččuiš, et kuččuiš', 112=>'ei kuččuiš', 113=>'emme kuččuiš', 114=>'ette kuččuiš', 115=>'ei kučutaiš', 44=>'kuččunuižin', 45=>'kuččunuižid, kuččunuižit', 46=>'kuččunuiš', 47=>'kuččunuižimme', 48=>'kuččunuižitte', 49=>'kučutannuiš', 302=>'kuččunuiš', 304=>'kučutannuiš', 116=>'en kuččunuiš', 117=>'ed kuččunuiš, et kuččunuiš', 118=>'ei kuččunuiš', 119=>'emme kuččunuiš', 120=>'ette kuččunuiš', 121=>'ei kučutannuiš', 122=>'oližin kuččunu', 123=>'oližid kuččunu, oližit kuččunu', 124=>'oliš kuččunu', 126=>'oližimme kuččunu', 127=>'oližitte kuččunu', 128=>'oldaiš kučuttu', 129=>'en oliš kuččunu', 130=>'ed oliš kuččunu, et oliš kuččunu', 131=>'ei oliš kuččunu', 132=>'emme oliš kuččunu', 133=>'ette oliš kuččunu', 134=>'ei oldaiš kučuttu', 135=>'olnuižin kuččunu', 125=>'olnuižid kuččunu, olnuižit kuččunu', 136=>'olnuiš kuččunu', 137=>'olnuižimme kuččunu', 138=>'olnuižitte kuččunu', 139=>'oldanuiš kučuttu', 140=>'en olnuiš kuččunu', 141=>'ed olnuiš kuččunu, et olnuiš kuččunu', 142=>'ei olnuiš kuččunu', 143=>'emme olnuiš kuččunu', 144=>'ette olnuiš kuččunu', 145=>'ei oldanuiš kučuttu', 146=>'kuččunen', 147=>'kuččuned', 148=>'kuččunou', 149=>'kuččunemme', 150=>'kuččunette', 151=>'kučutanneh', 310=>'kuččune', 311=>'kučutanne', 152=>'en kuččune', 153=>'ed kuččune, et kuččune', 154=>'ei kuččune', 155=>'emme kuččune', 156=>'ette kuččune', 157=>'ei kučutanne', 158=>'lienen kuččunu', 159=>'liened kuččunu, lienet kuččunu', 160=>'lienöy kuččunu', 161=>'lienemme kuččunu', 162=>'lienette kuččunu', 163=>'lienöy kučuttu', 164=>'en liene kuččunu', 165=>'ed liene kuččunu, et liene kuččunu', 166=>'ei liene kuččunu', 167=>'emme liene kuččunu', 168=>'ette liene kuččunu', 169=>'ei liene kučuttu', 170=>'kuččuda', 171=>'kuččudes', 172=>'kuččuden', 173=>'kuččumal', 174=>'kuččumah', 175=>'kuččumas', 176=>'kuččumas', 177=>'kuččumata', 178=>'kuččui', 179=>'kuččunu', 180=>'kučuttav', 181=>'kučuttu', ],
	    50380 => [26=>'kyzyn', 27=>'kyzyd, kyzyt', 28=>'kyzyy', 29=>'kyzymme', 30=>'kyzytte', 31=>'kyzytäh', 295=>'kyzy', 296=>'kyzytä', 70=>'en kyzy', 71=>'ed kyzy, et kyzy', 72=>'ei kyzy', 73=>'emme kyzy', 78=>'ette kyzy', 79=>'ei kyzytä', 32=>'kyzyin', 33=>'kyzyid, kyzyit', 34=>'kyzyi', 35=>'kyzyimme', 36=>'kyzyitte', 37=>'kyzyttih', 80=>'en kyzyny', 81=>'ed kyzyny, et kyzyny', 82=>'ei kyzyny', 83=>'emme kyzyny', 84=>'ette kyzyny', 85=>'ei kyzytty', 86=>'olen kyzyny', 87=>'oled kyzyny, olet kyzyny', 88=>'on kyzyny', 89=>'olemme kyzyny', 90=>'olette kyzyny', 91=>'oldah kyzytty, on kyzytty', 92=>'en ole kyzyny', 93=>'ed ole kyzyny, et ole kyzyny', 94=>'ei ole kyzyny', 95=>'emme ole kyzyny', 96=>'ette ole kyzyny', 97=>'ei olda kyzytty', 98=>'olin kyzyny', 99=>'olid kyzyny, olit kyzyny', 100=>'oli kyzyny', 101=>'olimme kyzyny', 102=>'olitte kyzyny', 103=>'oldih kyzytty, oli kyzytty', 104=>'en olnu kyzyny', 105=>'ed olnu kyzyny, et olnu kyzyny', 107=>'ei olnu kyzyny', 108=>'emme olnu kyzyny', 106=>'ette olnu kyzyny', 109=>'ei oldu kyzytty', 51=>'kyzy', 52=>'kyzygäh', 53=>'kyzygämme', 54=>'kyzygätte', 55=>'kyzytäkkäh', 50=>'älä kyzy', 74=>'älgäh kyzygäh', 75=>'älgämme kyzygämme', 76=>'älgätte kyzygätte', 77=>'äldägäh kyzytäkkäh', 38=>'kyzyižin', 39=>'kyzyižid, kyzyižit', 40=>'kyzyiš', 41=>'kyzyižimme', 42=>'kyzyižitte', 43=>'kyzytäiš', 301=>'kyzyiš', 303=>'kyzytäiš', 110=>'en kyzyiš', 111=>'ed kyzyiš, et kyzyiš', 112=>'ei kyzyiš', 113=>'emme kyzyiš', 114=>'ette kyzyiš', 115=>'ei kyzytäiš', 44=>'kyzynyižin', 45=>'kyzynyižid, kyzynyižit', 46=>'kyzynyiš', 47=>'kyzynyižimme', 48=>'kyzynyižitte', 49=>'kyzytännyiš', 302=>'kyzynyiš', 304=>'kyzytännyiš', 116=>'en kyzynyiš', 117=>'ed kyzynyiš, et kyzynyiš', 118=>'ei kyzynyiš', 119=>'emme kyzynyiš', 120=>'ette kyzynyiš', 121=>'ei kyzytännyiš', 122=>'oližin kyzyny', 123=>'oližid kyzyny, oližit kyzyny', 124=>'oliš kyzyny', 126=>'oližimme kyzyny', 127=>'oližitte kyzyny', 128=>'oldaiš kyzytty', 129=>'en oliš kyzyny', 130=>'ed oliš kyzyny, et oliš kyzyny', 131=>'ei oliš kyzyny', 132=>'emme oliš kyzyny', 133=>'ette oliš kyzyny', 134=>'ei oldaiš kyzytty', 135=>'olnuižin kyzyny', 125=>'olnuižid kyzyny, olnuižit kyzyny', 136=>'olnuiš kyzyny', 137=>'olnuižimme kyzyny', 138=>'olnuižitte kyzyny', 139=>'oldanuiš kyzytty', 140=>'en olnuiš kyzyny', 141=>'ed olnuiš kyzyny, et olnuiš kyzyny', 142=>'ei olnuiš kyzyny', 143=>'emme olnuiš kyzyny', 144=>'ette olnuiš kyzyny', 145=>'ei oldanuiš kyzytty', 146=>'kyzynen', 147=>'kyzyned, kyzynet', 148=>'kyzynöy', 149=>'kyzynemme', 150=>'kyzynette', 151=>'kyzytänneh', 310=>'kyzyne', 311=>'kyzytänne', 152=>'en kyzyne', 153=>'ed kyzyne, et kyzyne', 154=>'ei kyzyne', 155=>'emme kyzyne', 156=>'ette kyzyne', 157=>'ei kyzytänne', 158=>'lienen kyzyny', 159=>'liened kyzyny, lienet kyzyny', 160=>'lienöy kyzyny', 161=>'lienemme kyzyny', 162=>'lienette kyzyny', 163=>'lienöy kyzytty', 164=>'en liene kyzyny', 165=>'ed liene kyzyny, et liene kyzyny', 166=>'ei liene kyzyny', 167=>'emme liene kyzyny', 168=>'ette liene kyzyny', 169=>'ei liene kyzytty', 170=>'kyzydä', 171=>'kyzydes', 172=>'kyzyden', 173=>'kyzymäl', 174=>'kyzymäh', 175=>'kyzymäs', 176=>'kyzymäs', 177=>'kyzymätä', 178=>'kyzyi', 179=>'kyzyny', 180=>'kyzyttäv', 181=>'kyzytty', ],
	    41301 => [26=>'ečin', 27=>'ečid, ečit', 28=>'eččiy', 29=>'ečimme', 30=>'ečitte', 31=>'ečitäh', 295=>'eči', 296=>'ečitä', 70=>'en eči', 71=>'ed eči, et eči', 72=>'ei eči', 73=>'emme eči', 78=>'ette eči', 79=>'ei ečitä', 32=>'ečiin', 33=>'ečiid, ečiit', 34=>'eččii', 35=>'ečiimme', 36=>'ečiitte', 37=>'ečittih', 80=>'en eččiny', 81=>'ed eččinu, et eččinu', 82=>'ei eččiny', 83=>'emme eččiny', 84=>'ette eččiny', 85=>'ei ečitty', 86=>'olen eččiny', 87=>'oled eččiny, olen eččiny', 88=>'on eččiny', 89=>'olemme eččiny', 90=>'olette eččiny', 91=>'on ečitty', 92=>'en ole eččiny', 93=>'ed ole eččiny, et ole eččiny', 94=>'ei ole eččiny', 95=>'emme ole eččiny', 96=>'ette ole eččiny', 97=>'ei olda ečitty', 98=>'olin eččiny', 99=>'olid eččiny, olit eččiny', 100=>'oli eččiny', 101=>'olimme eččiny', 102=>'olitte eččiny', 103=>'oli ečitty', 104=>'en olnu eččiny', 105=>'ed olnu eččiny, et olnu eččiny', 107=>'ei olnu eččiny', 108=>'emme olnu eččiny', 106=>'ette olnu eččiny', 109=>'ei oldu ečitty', 51=>'eči', 52=>'eččigäh', 53=>'eččigämme', 54=>'eččigätte', 55=>'ečitäkkäh', 50=>'älä eči', 74=>'älgäh eččigäh', 75=>'älgämme eččigämme', 76=>'älgätte eččigätte', 77=>'äldägäh ečitäkkäh', 38=>'eččižin', 39=>'eččižid, eččižit', 40=>'eččiš', 41=>'eččižimme', 42=>'eččižitte', 43=>'ečitäiš', 301=>'eččiš', 303=>'ečitäiš', 110=>'en eččiš', 111=>'ed eččiš, et eččiš', 112=>'ei eččiš', 113=>'emme eččiš', 114=>'ette eččiš', 115=>'ei ečitäiš', 44=>'eččinyižin', 45=>'eččinyižid, eččinyižit', 46=>'eččinyiš', 47=>'eččinyižimme', 48=>'eččinyižitte', 49=>'ečitännyiš', 302=>'eččinyiš', 304=>'ečitännyiš', 116=>'en eččinyiš', 117=>'ed eččinyiš, et eččinyiš', 118=>'ei eččinyiš', 119=>'emme eččinyiš', 120=>'ette eččinyiš', 121=>'ei ečitännyiš', 122=>'oližin eččiny', 123=>'oližid eččiny, oližit eččiny', 124=>'oliš eččiny', 126=>'oližimme eččiny', 127=>'oližitte eččiny', 128=>'oldaiš ečitty', 129=>'en oliš eččiny', 130=>'ed oliš eččiny, et oliš eččiny', 131=>'ei oliš eččiny', 132=>'emme oliš eččiny', 133=>'ette oliš eččiny', 134=>'ei oldaiš ečitty', 135=>'olnuižin eččiny', 125=>'olnuižid eččiny, olnuižit eččiny', 136=>'olnuiš eččiny', 137=>'olnuižimme eččiny', 138=>'olnuižitte eččiny', 139=>'oldanuiš ečitty', 140=>'en olnuiš eččiny', 141=>'ed olnuiš eččiny, et olnuiš eččiny', 142=>'ei olnuiš eččiny', 143=>'emme olnuiš eččiny', 144=>'ette olnuiš eččiny', 145=>'ei oldanuiš ečitty', 146=>'eččinen', 147=>'eččined, eččinet', 148=>'eččinou', 149=>'eččinemme', 150=>'eččinette', 151=>'ečitänneh', 310=>'eččine', 311=>'ečitänne', 152=>'en eččine', 153=>'ed eččine, et eččine', 154=>'ei eččine', 155=>'emme eččine', 156=>'ette eččine', 157=>'ei ečitänne', 158=>'lienen eččiny', 159=>'liened eččiny, lienet eččiny', 160=>'lienöy eččiny', 161=>'lienemme eččiny', 162=>'lienette eččiny', 163=>'lienöy ečitty', 164=>'en liene eččiny', 165=>'ed liene eččiny, et liene eččiny', 166=>'ei liene eččiny', 167=>'emme liene eččiny', 168=>'ette liene eččiny', 169=>'ei liene ečitty', 170=>'eččidä', 171=>'eččides', 172=>'eččiden', 173=>'eččimäl', 174=>'eččimäh', 175=>'eččimäs', 176=>'eččimäs', 177=>'eččimätä', 178=>'eččii', 179=>'eččiny', 180=>'ečittäv', 181=>'ečitty', ],
	    22172 => [26=>'andan', 27=>'andad, andat', 28=>'andau', 29=>'andamme', 30=>'andatte', 31=>'andetah', 295=>'anda', 296=>'andeta', 70=>'en anda', 71=>'ed anda, et anda', 72=>'ei anda', 73=>'emme anda', 78=>'ette anda', 79=>'ei andeta', 32=>'andoin, anduoin', 33=>'andoid, andoit, anduoid, anduoit', 34=>'andoi, anduoi', 35=>'andoimme, anduoimme', 36=>'andoitte, anduoitte', 37=>'andettih', 80=>'en andanu', 81=>'ed andanu, et andanu', 82=>'ei andanu', 83=>'emme andanu', 84=>'ette andanu', 85=>'ei andettu', 86=>'olen andanu', 87=>'oled andanu, olet andanu', 88=>'on andanu', 89=>'olemme andanu', 90=>'olette andanu', 91=>'on andettu', 92=>'en ole andanu', 93=>'ed ole andanu, et ole andanu', 94=>'ei ole andanu', 95=>'emme ole andanu', 96=>'ette ole andanu', 97=>'ei olda andettu', 98=>'olin andanu', 99=>'olid andanu, olit andanu', 100=>'oli andanu', 101=>'olimme andanu', 102=>'olitte andanu', 103=>'oldih andettu, oli andettu', 104=>'en olnu andanu', 105=>'ed olnu andanu, et olnu andanu', 107=>'ei olnu andanu', 108=>'emme olnu andanu', 106=>'ette olnu andanu', 109=>'ei oldu andettu', 51=>'anda', 52=>'andagah', 53=>'andagamme', 54=>'andagatte', 55=>'andetakkah', 50=>'älä anda', 74=>'älgäh andagah', 75=>'älgämme andagamme', 76=>'älgätte andagatte', 77=>'äldägäh andetakkah', 38=>'andaižin', 39=>'andaižid, andaižit', 40=>'andaiš', 41=>'andaižimme', 42=>'andaižitte', 43=>'andetaiš', 301=>'andaiš', 303=>'andetaiš', 110=>'en andaiš', 111=>'ed andaiš, et andaiš', 112=>'ei andaiš', 113=>'emme andaiš', 114=>'ette andaiš', 115=>'ei andetaiš', 44=>'andanuižin', 45=>'andanuižid, andanuižit', 46=>'andanuiš', 47=>'andanuižimme', 48=>'andanuižitte', 49=>'andetannuiš', 302=>'andanuiš', 304=>'andetannuiš', 116=>'en andanuiš', 117=>'ed andanuiš, et andanuiš', 118=>'ei andanuiš', 119=>'emme andanuiš', 120=>'ette andanuiš', 121=>'ei andetannuiš', 122=>'oližin andanu', 123=>'oližid andanu, oližit andanu', 124=>'oliš andanu', 126=>'oližimme andanu', 127=>'oližitte andanu', 128=>'oldaiš andettu', 129=>'en oliš andanu', 130=>'ed oliš andanu, et oliš andanu', 131=>'ei oliš andanu', 132=>'emme oliš andanu', 133=>'ette oliš andanu', 134=>'ei oldaiš andettu', 135=>'olnuižin andanu', 125=>'olnuižid andanu, olnuižit andanu', 136=>'olnuiš andanu', 137=>'olnuižimme andanu', 138=>'olnuižitte andanu', 139=>'oldanuiš andettu', 140=>'en olnuiš andanu', 141=>'ed olnuiš andanu, et olnuiš andanu', 142=>'ei olnuiš andanu', 143=>'emme olnuiš andanu', 144=>'ette olnuiš andanu', 145=>'ei oldanuiš andettu', 146=>'andanen', 147=>'andaned, andanet', 148=>'andanou', 149=>'andanemme', 150=>'andanette', 151=>'andetanneh', 310=>'andane', 311=>'andetanne', 152=>'en andane', 153=>'ed andane, et andane', 154=>'ei andane', 155=>'emme andane', 156=>'ette andane', 157=>'ei andetanne', 158=>'lienen andanu', 159=>'liened andanu, lienet andanu', 160=>'lienöy andanu', 161=>'lienemme andanu', 162=>'lienette andanu', 163=>'lienöy andettu', 164=>'en liene andanu', 165=>'ed liene andanu, et liene andanu', 166=>'ei liene andanu', 167=>'emme liene andanu', 168=>'ette liene andanu', 169=>'ei liene andettu', 170=>'andada', 171=>'andades', 172=>'andaden', 173=>'andamal', 174=>'andamah', 175=>'andamas', 176=>'andamas', 177=>'andamata', 178=>'andai', 179=>'andanu', 180=>'andettav', 181=>'andettu', ],
	    14596 => [26=>'otan', 27=>'otad, otat', 28=>'ottau', 29=>'otamme', 30=>'otatte', 31=>'otetah', 295=>'ota', 296=>'oteta', 70=>'en ota', 71=>'ed ota, et ota', 72=>'ei ota', 73=>'emme ota', 78=>'ette ota', 79=>'ei oteta', 32=>'otin', 33=>'otid, otit', 34=>'otti', 35=>'otimme', 36=>'otitte', 37=>'otettih', 80=>'en ottanu', 81=>'ed ottanu, et ottanu', 82=>'ei ottanu', 83=>'emme ottanu', 84=>'ette ottanu', 85=>'ei otettu', 86=>'olen ottanu', 87=>'oled ottanu, olet ottanu', 88=>'on ottanu', 89=>'olemme ottanu', 90=>'olette ottanu', 91=>'oldah otettu, on otettu', 92=>'en ole ottanu', 93=>'ed ole ottanu, et ole ottanu', 94=>'ei ole ottanu', 95=>'emme ole ottanu', 96=>'ette ole ottanu', 97=>'ei olda otettu', 98=>'olin ottanu', 99=>'olid ottanu, olit ottanu', 100=>'oli ottanu', 101=>'olimme ottanu', 102=>'olitte ottanu', 103=>'oldih otettu, oli otettu', 104=>'en olnu ottanu', 105=>'ed olnu ottanu, et olnu ottanu', 107=>'ei olnu ottanu', 108=>'emme olnu ottanu', 106=>'ette olnu ottanu', 109=>'ei oldu otettu', 51=>'ota', 52=>'ottagah', 53=>'ottagamme', 54=>'ottagatte', 55=>'otetakkah', 50=>'älä ota', 74=>'älgäh ottagah', 75=>'älgämme ottagamme', 76=>'älgätte ottagatte', 77=>'äldägäh otetakkah', 38=>'ottaižin', 39=>'ottaižid, ottaižit', 40=>'ottaiš', 41=>'ottaižimme', 42=>'ottaižitte', 43=>'otetaiš', 301=>'ottaiš', 303=>'otetaiš', 110=>'en ottaiš', 111=>'ed ottaiš, et ottaiš', 112=>'ei ottaiš', 113=>'emme ottaiš', 114=>'ette ottaiš', 115=>'ei otetaiš', 44=>'ottanuižin', 45=>'ottanuižid, ottanuižit', 46=>'ottanuiš', 47=>'ottanuižimme', 48=>'ottanuižitte', 49=>'otetannuiš', 302=>'ottanuiš', 304=>'otetannuiš', 116=>'en ottanuiš', 117=>'ed ottanuiš, et ottanuiš', 118=>'ei ottanuiš', 119=>'emme ottanuiš', 120=>'ette ottanuiš', 121=>'ei otetannuiš', 122=>'oližin ottanu', 123=>'oližid ottanu, oližit ottanu', 124=>'oliš ottanu', 126=>'oližimme ottanu', 127=>'oližin ottanu', 128=>'oldaiš otettu', 129=>'en oliš ottanu', 130=>'ed oliš ottanu, et oliš ottanu', 131=>'ei oliš ottanu', 132=>'emme oliš ottanu', 133=>'ette oliš ottanu', 134=>'ei oldaiš otettu', 135=>'olnuižin ottanu', 125=>'olnuižid ottanu, olnuižit ottanu', 136=>'olnuiš ottanu', 137=>'olnuižimme ottanu', 138=>'olnuižitte ottanu', 139=>'oldanuiš otettu', 140=>'en olnuiš ottanu', 141=>'ed olnuiš ottanu, et olnuiš ottanu', 142=>'ei olnuiš ottanu', 143=>'emme olnuiš ottanu', 144=>'ette olnuiš ottanu', 145=>'ei oldanuiš otettu', 146=>'ottanen', 147=>'ottaned, ottanet', 148=>'ottanou', 149=>'ottanemme', 150=>'ottanette', 151=>'otetanneh', 310=>'ottane', 311=>'otetanne', 152=>'en ottane', 153=>'ed ottane, et ottane', 154=>'ei ottane', 155=>'emme ottane', 156=>'ette ottane', 157=>'ei otetanne', 158=>'lienen ottanu', 159=>'liened ottanu, lienet ottanu', 160=>'lienöy ottanu', 161=>'lienemme ottanu', 162=>'lienette ottanu', 163=>'lienöy otettu', 164=>'en liene ottanu', 165=>'ed liene ottanu, et liene ottanu', 166=>'ei liene ottanu', 167=>'emme liene ottanu', 168=>'ette liene ottanu', 169=>'ei liene otettu', 170=>'ottada', 171=>'ottades', 172=>'ottaden', 173=>'ottamal', 174=>'ottamah', 175=>'ottamas', 176=>'ottamas', 177=>'ottamata', 178=>'ottai', 179=>'ottanu', 180=>'otettav', 181=>'otettu', ],
	    14594 => [26=>'elän', 27=>'eläd, elät', 28=>'eläy', 29=>'elämme', 30=>'elätte', 31=>'eletäh', 295=>'elä', 296=>'eletä', 70=>'en elä', 71=>'ed elä, et elä', 72=>'ei elä', 73=>'emme elä', 78=>'ette elä', 79=>'ei eletä', 32=>'elin', 33=>'elid, elit', 34=>'eli', 35=>'elimme', 36=>'elitte', 37=>'elettih', 80=>'en eläny', 81=>'ed eläny, et eläny', 82=>'ei eläny', 83=>'emme eläny', 84=>'ette eläny', 85=>'ei eletty', 86=>'olen eläny', 87=>'oled eläny, olet eläny', 88=>'on eläny', 89=>'olemme eläny', 90=>'olette eläny', 91=>'oldah eletty, on eletty', 92=>'en ole eläny', 93=>'ed ole eläny, et ole eläny', 94=>'ei ole eläny', 95=>'emme ole eläny', 96=>'en ole eläny', 97=>'ei olda eletty', 98=>'olin eläny', 99=>'olid eläny, olit eläny', 100=>'oli eläny', 101=>'olimme eläny', 102=>'olitte eläny', 103=>'oldih eletty, oli eletty', 104=>'en olnu eläny', 105=>'ed olnu eläny, et olnu eläny', 107=>'ei olnu eläny', 108=>'emme olnu eläny', 106=>'ette olnu eläny', 109=>'ei oldu eletty', 51=>'elä', 52=>'elägäh', 53=>'elägämme', 54=>'elägätte', 55=>'eletäkkäh', 50=>'älä elä', 74=>'älgäh elägäh', 75=>'älgämme elägämme', 76=>'älgätte elägätte', 77=>'älgädäh eletäkkäh', 38=>'eläižin', 39=>'eläižid, eläižit', 40=>'eläiš', 41=>'eläižimme', 42=>'eläižitte', 43=>'eletäiš', 301=>'eläiš', 303=>'eletäiš', 110=>'en eläiš', 111=>'ed eläiš, et eläiš', 112=>'ei eläiš', 113=>'emme eläiš', 114=>'ette eläiš', 115=>'ei eletäiš', 44=>'elänyižin', 45=>'elänyižid, elänyižit', 46=>'elänyiš', 47=>'elänyižimme', 48=>'elänyižitte', 49=>'elettännyiš', 302=>'elänyiš', 304=>'elettännyiš', 116=>'en elänyiš', 117=>'ed elänyiš, et elänyiš', 118=>'ei elänyiš', 119=>'emme elänyiš', 120=>'ette elänyiš', 121=>'ei elettännyiš', 122=>'oližin eläny', 123=>'oližid eläny, oližit eläny', 124=>'oliš eläny', 126=>'oližimme eläny', 127=>'oližitte eläny', 128=>'oldaiš eletty', 129=>'en oliš eläny', 130=>'ed oliš eläny, et oliš eläny', 131=>'ei oliš eläny', 132=>'emme oliš eläny', 133=>'ette oliš eläny', 134=>'ei oldaiš eletty', 135=>'olnuižin eläny', 125=>'olnuižid eläny, olnuižit eläny', 136=>'olnuiš eläny', 137=>'olnuižimme eläny', 138=>'olnuižitte eläny', 139=>'oldaiš eletty', 140=>'en olnuiš eläny', 141=>'ed olnuiš eläny, et olnuiš eläny', 142=>'ei olnuiš eläny', 143=>'emme olnuiš eläny', 144=>'ette olnuiš eläny', 145=>'ei oldaiš eletty', 146=>'elänen', 147=>'eläned, elänet', 148=>'elänöy', 149=>'elänemme', 150=>'elänette', 151=>'eletänneh', 310=>'eläne', 311=>'eletänne', 152=>'en eläne', 153=>'ed eläne, et eläne', 154=>'ei eläne', 155=>'emme eläne', 156=>'ette eläne', 157=>'ei eletänne', 158=>'lienen eläny', 159=>'liened eläny, lienet eläny', 160=>'lienöy eläny', 161=>'lienemme eläny', 162=>'lienette eläny', 163=>'lienöy eletty', 164=>'en liene eläny', 165=>'ed liene eläny, et liene eläny', 166=>'ei liene eläny', 167=>'emme liene eläny', 168=>'ette liene eläny', 169=>'ei liene eletty', 170=>'elädä', 171=>'elädes', 172=>'eläden', 173=>'elämäl', 174=>'elämäh', 175=>'elämäs', 176=>'elämäs', 177=>'elämätä', 178=>'eläi', 179=>'eläny', 180=>'elettäv', 181=>'eletty', ],
	    45142 => [26=>'itken', 27=>'itked, itket', 28=>'itköy', 29=>'itkemme', 30=>'itkette', 31=>'itketäh', 295=>'itke', 296=>'itketä', 70=>'en itke', 71=>'ed itke, et itke', 72=>'ei itke', 73=>'emme itke', 78=>'ette itke', 79=>'ei itketä', 32=>'itkin', 33=>'itkid, itkit', 34=>'itki', 35=>'itkimme', 36=>'itkitte', 37=>'itkettih', 80=>'en itkeny', 81=>'ed itkeny, et itkeny', 82=>'ei itkeny', 83=>'emme itkeny', 84=>'ette itkeny', 85=>'ei itketty', 86=>'olen itkeny', 87=>'oled itkeny, olet itkeny', 88=>'on itkeny', 89=>'olemme itkeny', 90=>'olette itkeny', 91=>'oldah itketty, on itketty', 92=>'en ole itkeny', 93=>'ed ole itkeny, et ole itkeny', 94=>'ei ole itkeny', 95=>'emme ole itkeny', 96=>'ette ole itkeny', 97=>'ei olda itketty', 98=>'olin itkeny', 99=>'olid itkeny, olit itkeny', 100=>'oli itkeny', 101=>'olimme itkeny', 102=>'olitte itkeny', 103=>'oldih itketty, oli itketty', 104=>'en olnu itkeny', 105=>'ed olnu itkeny, et olnu itkeny', 107=>'ei olnu itkeny', 108=>'emme olnu itkeny', 106=>'ette olnu itkeny', 109=>'ei oldu itketty', 51=>'itke', 52=>'itkegäh', 53=>'itkegämme', 54=>'itkegätte', 55=>'itketäkkäh', 50=>'älä itke', 74=>'älgäh itkegäh', 75=>'älgämme itkegämme', 76=>'älgätte itkegätte', 77=>'äldägäh itketäkkäh', 38=>'itkižin', 39=>'itkižid, itkižit', 40=>'itkiš', 41=>'itkižimme', 42=>'itkižitte', 43=>'itketäiš', 301=>'itkiš', 303=>'itketäiš', 110=>'en itkiš', 111=>'ed itkiš, et itkiš', 112=>'ei itkiš', 113=>'emme itkiš', 114=>'ette itkiš', 115=>'ei itketäiš', 44=>'itkenyižin', 45=>'itkenyižid, itkenyižit', 46=>'itkenyiš', 47=>'itkenyižimme', 48=>'itkenyižitte', 49=>'itketännyiš', 302=>'itkenyiš', 304=>'itketännyiš', 116=>'en itkenyiš', 117=>'ed itkenyiš, et itkenyiš', 118=>'ei itkenyiš', 119=>'emme itkenyiš', 120=>'ette itkenyiš', 121=>'ei itketännyiš', 122=>'oližin itkeny', 123=>'oližid itkeny, oližit itkeny', 124=>'oližin itkeny', 126=>'oliš itkeny', 127=>'oližitte itkeny', 128=>'oldaiš itketty', 129=>'en oliš itkeny', 130=>'ed oliš itkeny, et oliš itkeny', 131=>'ei oliš itkeny', 132=>'emme oliš itkeny', 133=>'ette oliš itkeny', 134=>'ei oldaiš itkietty', 135=>'olnužin itkeny', 125=>'olnužid itkeny, olnužit itkeny', 136=>'olnuiš itkeny', 137=>'olnužimme itkeny', 138=>'olnužitte itkeny', 139=>'oldanuiš itkietty', 140=>'en olnuiš itkeny', 141=>'ed olnuiš itkeny, et olnuiš itkeny', 142=>'ei olnuiš itkeny', 143=>'emme olnuiš itkeny', 144=>'ette olnuiš itkeny', 145=>'ei oldanuiš itkietty', 146=>'itkenen', 147=>'itkened, itkenet', 148=>'itkenöy', 149=>'itkenemme', 150=>'itkenette', 151=>'itketänneh', 310=>'itkene', 311=>'itketänne', 152=>'en itkene', 153=>'ed itkene, et itkene', 154=>'ei itkene', 155=>'emme itkene', 156=>'ette itkene', 157=>'ei itketänne', 158=>'lienen itkeny', 159=>'liened itkeny, lienet itkeny', 160=>'lienöy itkeny', 161=>'lienemme itkeny', 162=>'lienette itkeny', 163=>'lienöy itketty', 164=>'en liene itkeny', 165=>'ed liene itkeny, et liene itkeny', 166=>'ei liene itkeny', 167=>'emme liene itkeny', 168=>'ette liene itkeny', 169=>'ei liene itketty', 170=>'itkedä, itkei', 171=>'itkedes', 172=>'itkeden', 173=>'itkemäl', 174=>'itkemäh', 175=>'itkemäs', 176=>'itkemäs', 177=>'itkemätä', 178=>'itkii', 179=>'itkeny', 180=>'itkettäv', 181=>'itketty', ],
	    43596 => [26=>'särbän', 27=>'särbäd, särbät', 28=>'särbäy', 29=>'särbämme', 30=>'särbätte', 31=>'särbetäh', 295=>'särbä', 296=>'särbetä', 70=>'en särbä', 71=>'ed särbä, et särbä', 72=>'ei särbä', 73=>'emme särbä', 78=>'ette särbä', 79=>'ei särbetä', 32=>'särbin', 33=>'särbid, särbit', 34=>'särbi', 35=>'särbimme', 36=>'särbitte', 37=>'särbettih', 80=>'en särbäny', 81=>'ed särbäny, et särbäny', 82=>'ei särbäny', 83=>'emme särbäny', 84=>'ette särbäny', 85=>'ei särbetty', 86=>'olen särbäny', 87=>'oled särbäny, olet särbäny', 88=>'on särbäny', 89=>'olemme särbäny', 90=>'olette särbäny', 91=>'oldah särbetty, on särbetty', 92=>'en ole särbäny', 93=>'ed ole särbäny, et ole särbäny', 94=>'ei ole särbäny', 95=>'emme ole särbäny', 96=>'ette ole särbäny', 97=>'ei olda särbetty', 98=>'olin särbäny', 99=>'olid särbäny, olit särbäny', 100=>'oli särbäny', 101=>'olimme särbäny', 102=>'olitte särbäny', 103=>'oldih särbetty, oli särbetty', 104=>'en olnu särbäny', 105=>'ed olnu särbäny, et olnu särbäny', 107=>'ei olnu särbäny', 108=>'emme olnu särbäny', 106=>'ette olnu', 109=>'ei oldu särbetty', 51=>'särbä', 52=>'särbägäh', 53=>'särbägämme', 54=>'särbägätte', 55=>'särbetäkkäh', 50=>'älä särbä', 74=>'älgäh särbägäh', 75=>'älgämme särbägämme', 76=>'älgätte särbägätte', 77=>'äldägäh särbetäkkäh', 38=>'särbäižin', 39=>'särbäižid, särbäižit', 40=>'särbäiš', 41=>'särbäižimme', 42=>'särbäižitte', 43=>'särbetäiš', 301=>'särbäiš', 303=>'särbetäiš', 110=>'en särbäiš', 111=>'ed särbäiš, et särbäiš', 112=>'ei särbäiš', 113=>'emme särbäiš', 114=>'ette särbäiš', 115=>'ei särbetäiš', 44=>'särbänyižin', 45=>'särbänyižid, särbänyižit', 46=>'särbänyiš', 47=>'särbänyižimme', 48=>'särbänyižitte', 49=>'särbetännyiš', 302=>'särbänyiš', 304=>'särbetännyiš', 116=>'en särbänyiš', 117=>'ed särbänyiš, et särbänyiš', 118=>'ei särbänyiš', 119=>'emme särbänyiš', 120=>'ette särbänyiš', 121=>'ei särbetännyiš', 122=>'oližin särbäny', 123=>'oližid särbäny, oližit särbäny', 124=>'oliš särbäny', 126=>'oližimme särbäny', 127=>'oližitte särbäny', 128=>'oldaiš särbetty', 129=>'en oliš särbäny', 130=>'ed oliš särbäny, et oliš särbäny', 131=>'ei oliš särbäny', 132=>'emme oliš särbäny', 133=>'ette oliš särbäny', 134=>'ei oldaiš särbetty', 135=>'olnuižin särbäny', 125=>'olnuižid särbäny, olnuižit särbäny', 136=>'olnuiš särbäny', 137=>'olnuižimme särbäny', 138=>'olnuižitte särbäny', 139=>'oldanuiš särbetty', 140=>'en olnuiš särbäny', 141=>'ed olnuiš särbäny, et olnuiš särbäny', 142=>'ei olnuiš särbäny', 143=>'emme olnuiš särbäny', 144=>'ette olnuiš särbäny', 145=>'ei oldanuiš särbetty', 146=>'särbänen', 147=>'särbäned, särbänet', 148=>'särbänöy', 149=>'särbänemme', 150=>'särbänette', 151=>'särbetänneh', 310=>'särbäne', 311=>'särbetänne', 152=>'en särbäne', 153=>'ed särbäne, et särbäne', 154=>'ei särbäne', 155=>'emme särbäne', 156=>'ette särbäne', 157=>'ei särbetänne', 158=>'lienen särbäny', 159=>'liened särbäny, lienet särbäny', 160=>'lienöy särbäny', 161=>'lienemme särbäny', 162=>'lienette särbäny', 163=>'lienöy särbetty', 164=>'en liene särbäny', 165=>'ed liene särbäny, et liene särbäny', 166=>'ei liene särbäny', 167=>'emme liene särbäny', 168=>'ette liene särbäny', 169=>'ei liene särbetty', 170=>'särbädä', 171=>'särbädes', 172=>'särbäden', 173=>'särbämäl', 174=>'särbämäh', 175=>'särbämäs', 176=>'särbämäs', 177=>'särbämätä', 178=>'särbäi', 179=>'särbäny', 180=>'särbettäv', 181=>'särbetty', ],
	    29444 => [26=>'d’uon', 27=>'d’uod, d’uot', 28=>'d’uou', 29=>'d’uomme', 30=>'d’uotte', 31=>'d’uodah', 295=>'d’uo', 296=>'d’uoda', 70=>'en d’uo', 71=>'et d’uo', 72=>'ei d’uo', 73=>'emme d’uo', 78=>'ette d’uo', 79=>'ei d’uoda', 32=>'d’oin, d’uoin', 33=>'d’oid, d’oit, d’uoid, d’uoit', 34=>'d’oi, d’uoi', 35=>'d’oimme, d’uoimme', 36=>'d’oitte, d’uoitte', 37=>'d’uodih', 80=>'en d’uonu', 81=>'ed d’uonu, et d’uonu', 82=>'ei d’uonu', 83=>'emme d’uonu', 84=>'ette d’uonu', 85=>'ei d’uodu', 86=>'olen d’uonu', 87=>'oled d’uonu, olet d’uonu', 88=>'on d’uonu', 89=>'olemme d’uonu', 90=>'olette d’uonu', 91=>'on d’uodu', 92=>'en ole d’uonu', 93=>'ed ole d’uonu, et ole d’uonu', 94=>'ei ole d’uonu', 95=>'emme ole d’uonu', 96=>'ette ole d’uonu', 97=>'ei ole d’uodu', 98=>'olin d’uonu', 99=>'olid d’uonu, olit d’uonu', 100=>'oli d’uonu', 101=>'olimme d’uonu', 102=>'olitte d’uonu', 103=>'oli d’uodu', 104=>'en olnu d’uonu', 105=>'ed olnu d’uonu, et olnu d’uonu', 107=>'ei olnu d’uonu', 108=>'emme olnu d’uonu', 106=>'ette olnu d’uonu', 109=>'ei oldu d’uodu', 51=>'d’uo', 52=>'d’uogah', 53=>'d’uogamme', 54=>'d’uogatte', 55=>'d’uodagah', 50=>'älä d’uo', 74=>'älgäh d’uogah', 75=>'älgämme d’uogamme', 76=>'älgätte d’uogatte', 77=>'äldägäh d’uodagah', 38=>'d’uoižin', 39=>'d’uoižid, d’uoižit', 40=>'d’uoiš', 41=>'d’uoižimme', 42=>'d’uoižitte', 43=>'d’uodaiš', 301=>'d’uoiš', 303=>'d’uodaiš', 110=>'en d’uoiš', 111=>'ed d’uoiš, et d’uoiš', 112=>'ei d’uoiš', 113=>'emme d’uoiš', 114=>'ette d’uoiš', 115=>'ei d’uodaiš', 44=>'d’uonuižin', 45=>'d’uonuižid, d’uonuižit', 46=>'d’uonuiš', 47=>'d’uonuižimme', 48=>'d’uonuižitte', 49=>'d’uodanuiš', 302=>'d’uonuiš', 304=>'d’uodanuiš', 116=>'en d’uonuiš', 117=>'ed d’uonuiš, et d’uonuiš', 118=>'ei d’uonuiš', 119=>'emme d’uonuiš', 120=>'ette d’uonuiš', 121=>'ei d’uodanuiš', 122=>'oližin d’uonu', 123=>'oližid d’uonu, oližit d’uonu', 124=>'oliš d’uonu', 126=>'oližimme d’uonu', 127=>'oližitte d’uonu', 128=>'oldaiš d’uodu', 129=>'en oliš d’uonu', 130=>'ed oliš d’uonu, et oliš d’uonu', 131=>'ei oliš d’uonu', 132=>'emme oliš d’uonu', 133=>'ette oliš d’uonu', 134=>'ei oldaiš d’uodu', 135=>'olnuižin d’uonu', 125=>'olnuižid d’uonu, olnuižit d’uonu', 136=>'olnuiš d’uonu', 137=>'olnuižimme d’uonu', 138=>'olnuižitte d’uonu', 139=>'oldanuiš d’udu', 140=>'en olnuiš d’uonu', 141=>'ed olnuiš d’uonu, et olnuiš d’uonu', 142=>'ei olnuiš d’uonu', 143=>'emme olnuiš d’uonu', 144=>'ette olnuiš d’uonu', 145=>'ee oldanuiš d’uodu', 146=>'d’uonen', 147=>'d’uoned, d’uonet', 148=>'d’uonou', 149=>'d’uonemme', 150=>'d’uonette', 151=>'d’uodaneh', 310=>'d’uone', 311=>'d’uodane', 152=>'en d’uone', 153=>'ed d’uone, et d’uone', 154=>'ei d’uone', 155=>'emme d’uone', 156=>'ette d’uone', 157=>'ei d’uodane', 158=>'lienen d’uonu', 159=>'liened d’uonu, lienet d’uonu', 160=>'lienöy d’uonu', 161=>'lienemme d’uonu', 162=>'lienette d’uonu', 163=>'lienöy d’uodu', 164=>'en liene d’uonu', 165=>'ed liene d’uonu, et liene d’uonu', 166=>'ei liene d’uonu', 167=>'emme liene d’uonu', 168=>'ette liene d’uonu', 169=>'ei liene d’uodu', 170=>'d’uoda', 171=>'d’uodes', 172=>'d’uoden', 173=>'d’uomal', 174=>'d’uomah', 175=>'d’uomas', 176=>'d’uomas', 177=>'d’uomata', 178=>'d’uoi', 179=>'d’uonu', 180=>'d’uodav', 181=>'d’uodu', ],
	    62863 => [26=>'vien', 27=>'vied, viet', 28=>'viey', 29=>'viemme', 30=>'viette', 31=>'viedäh', 295=>'vie', 296=>'viedä', 70=>'en vie', 71=>'ed vie, et vie', 72=>'ei vie', 73=>'emme vie', 78=>'ette vie', 79=>'ei viedä', 32=>'vein, viein', 33=>'veid, veit, vieid, vieit', 34=>'vei, viei', 35=>'vieimme, viemme', 36=>'veitte, vieitte', 37=>'viedih', 80=>'en vieny', 81=>'ed vieny, et vieny', 82=>'ei vieny', 83=>'emme vieny', 84=>'ette vieny', 85=>'ei viedy', 86=>'olen vieny', 87=>'oled vieny, olet vieny', 88=>'on vieny', 89=>'olemme vieny', 90=>'olette vieny', 91=>'oldah viedy, on viedy', 92=>'en ole vieny', 93=>'ed ole vieny, et ole vieny', 94=>'ei ole vieny', 95=>'emme ole vieny', 96=>'ette ole vieny', 97=>'ei olda viedy, ei ole viedy', 98=>'olin vieny', 99=>'olid vieny, olit vieny', 100=>'oli vieny', 101=>'olimme vieny', 102=>'olitte vieny', 103=>'oldih viedy, oli viedy', 104=>'en olnu vieny', 105=>'ed olnu vieny, et olnu vieny', 107=>'ei olnu vieny', 108=>'emme olnu vieny', 106=>'ette olnu vieny', 109=>'ei oldu viedy, en olnu viedy', 51=>'vie', 52=>'viegäh', 53=>'viegämme', 54=>'viegätte', 55=>'viedägäh', 50=>'älä vie', 74=>'älgäh viegäh', 75=>'älgämme viegämme', 76=>'älgätte viegätte', 77=>'äldägäh viedägäh', 38=>'vieižin', 39=>'vieižid, vieižit', 40=>'vieiš', 41=>'vieižimme', 42=>'vieižitte', 43=>'viedäiš', 301=>'vieiš', 303=>'viedäiš', 110=>'en vieiš', 111=>'ed vieiš, et vieiš', 112=>'ei vieiš', 113=>'emme vieiš', 114=>'ette vieiš', 115=>'ei viedäiš', 44=>'vienyižin', 45=>'vienyižid, vienyižit', 46=>'vienyiš', 47=>'vienyižimme', 48=>'vienyižitte', 49=>'viedänyiš', 302=>'vienyiš', 304=>'viedänyiš', 116=>'en vienyiš', 117=>'ed vienyiš, et vienyiš', 118=>'ei vienyiš', 119=>'emme vienyiš', 120=>'ette vienyiš', 121=>'ei viedänyiš', 122=>'oližin vieny', 123=>'oližid vieny, oližit vieny', 124=>'oliš vieny', 126=>'oližimme vieny', 127=>'oližitte vieny', 128=>'oldaiš viedy', 129=>'en oliš vieny', 130=>'ed oliš vieny, et oliš vieny', 131=>'ei oliš vieny', 132=>'emme oliš vieny', 133=>'ette oliš vieny', 134=>'ei oldaiš viedy', 135=>'olnuižin vieny', 125=>'olnuižid vieny, olnuižit vieny', 136=>'olnuiš vieny', 137=>'olnuižimme vieny', 138=>'olnuižitte vieny', 139=>'oldanuiš viedy', 140=>'en olnuiš vieny', 141=>'ed olnuiš vieny, et olnuiš vieny', 142=>'ei olnuiš vieny', 143=>'emme olnuiš vieny', 144=>'ette olnuiš vieny', 145=>'ei oldanuiš viedy', 146=>'vienen', 147=>'viened, vienet', 148=>'vienou', 149=>'vienemme', 150=>'vienette', 151=>'viedäneh', 310=>'viene', 311=>'viedäne', 152=>'en viene', 153=>'ed viene, et viene', 154=>'ei viene', 155=>'emme viene', 156=>'ette viene', 157=>'ei viedäne', 158=>'lienen vieny', 159=>'liened vieny, lienet vieny', 160=>'lienöy vieny', 161=>'lienemme vieny', 162=>'lienette vieny', 163=>'lienöy viedy', 164=>'en liene vieny', 165=>'ed liene vieny, et liene vieny', 166=>'ei liene vieny', 167=>'emme liene vieny', 168=>'ette liene vieny', 169=>'ei liene viedy', 170=>'viedä', 171=>'viedes', 172=>'vieden', 173=>'viemäl', 174=>'viemäh', 175=>'viemäs', 176=>'viemäs', 177=>'viemätä', 178=>'viei', 179=>'vieny', 180=>'viedäv', 181=>'viedy', ],
	    41336 => [26=>'suan', 27=>'suad, suat', 28=>'suau', 29=>'suamme', 30=>'suatte', 31=>'suadah', 295=>'sua', 296=>'suada', 70=>'en sua', 71=>'ed sua', 72=>'ei sua', 73=>'emme sua', 78=>'ette sua', 79=>'ei suada', 32=>'sain, suain', 33=>'said, sait, suaid, suait', 34=>'sai, suai', 35=>'saimme, suaimme', 36=>'saitte, suaitte', 37=>'suadih', 80=>'en suanu', 81=>'ed suanu, et suanu', 82=>'ei suanu', 83=>'emme suanu', 84=>'ette suanu', 85=>'ei suadu', 86=>'olen suanu', 87=>'oled suanu, olet suanu', 88=>'on suanu', 89=>'olemme suanu', 90=>'olette suanu', 91=>'oldah suadu, on suadu', 92=>'en ole suanu', 93=>'ed ole suanu, et ole suanu', 94=>'ei ole suanu', 95=>'emme ole suanu', 96=>'ette ole suanu', 97=>'ei olda suadu', 98=>'olin suanu', 99=>'olid suanu, olit suanu', 100=>'oli suanu', 101=>'olimme suanu', 102=>'olitte suanu', 103=>'oldih suadu, oli suadu', 104=>'en olnu suanu', 105=>'ed olnu suanu, et olnu suanu', 107=>'ei olnu suanu', 108=>'emme olnu suanu', 106=>'ette olnu suanu', 109=>'ei oldu suadu', 51=>'sua', 52=>'suagah', 53=>'suagamme', 54=>'suagatte', 55=>'suadagah', 50=>'älä sua', 74=>'älgäh suagah', 75=>'älgämme suagamme', 76=>'älgätte suagatte', 77=>'äldägäh suadagah', 38=>'suaižin', 39=>'suaižid, suaižit', 40=>'suaiš', 41=>'suaižimme', 42=>'suaižitte', 43=>'suadaiš', 301=>'suaiš', 303=>'suadaiš', 110=>'en suaiš', 111=>'ed suaiš, et suaiš', 112=>'ei suaiš', 113=>'emme suaiš', 114=>'ette suaiš', 115=>'ei suadaiš', 44=>'suanuižin', 45=>'suanuižid, suanuižit', 46=>'suanuiš', 47=>'suanuižimme', 48=>'suanuižitte', 49=>'suadanuiš', 302=>'suanuiš', 304=>'suadanuiš', 116=>'en suanuiš', 117=>'ed suanuiš, ei suanuiš', 118=>'ei suanuiš', 119=>'emme suanuiš', 120=>'ette suanuiš', 121=>'ei suadanuiš', 122=>'oližin suanu', 123=>'oližid suanu, oližit suanu', 124=>'oliš suanu', 126=>'oližimme suanu', 127=>'oližitte suanu', 128=>'oldaiš suadu', 129=>'en oliš suanu', 130=>'ed oliš suanu, et oliš suanu', 131=>'ei oliš suanu', 132=>'emme oliš suanu', 133=>'ette oliš suanu', 134=>'ei oldaiš suadu', 135=>'olnuižin suanu', 125=>'olnuižid suanu, olnuižit suanu', 136=>'olnuiš suanu', 137=>'olnuižimme suanu', 138=>'olnuižitte suanu', 139=>'oldanuiš suadu', 140=>'en olnuiš suanu', 141=>'ed olnuiš suanu, et olnuiš suanu', 142=>'ei olnuiš suanu', 143=>'emme olnuiš suanu', 144=>'ette olnuiš suanu', 145=>'ei oldanuiš suadu', 146=>'suanen', 147=>'suaned, suanet', 148=>'suanou', 149=>'suanemme', 150=>'suanette', 151=>'suadaneh', 310=>'suane', 311=>'suadane', 152=>'en suane', 153=>'ed suane, et suane', 154=>'ei suane', 155=>'emme suane', 156=>'ette suane', 157=>'ei suadane', 158=>'lienen suanu', 159=>'liened suanu, lienet suanu', 160=>'lienöy suanu', 161=>'lienemme suanu', 162=>'lienette suanu', 163=>'lienöy suadu', 164=>'en liene suanu', 165=>'ed liene suanu, et liene suanuu', 166=>'ei liene suanu', 167=>'emme liene suanu', 168=>'ette liene suanu', 169=>'ei liene suadu', 170=>'suada', 171=>'suades', 172=>'suaden', 173=>'suamal', 174=>'suamah', 175=>'suamas', 176=>'suamas', 177=>'suamata', 178=>'suai', 179=>'suanu', 180=>'suadav', 181=>'suadu', ],
	    29594 => [26=>'tulen', 27=>'tuled, tulet', 28=>'tulou', 29=>'tulemme', 30=>'tulette', 31=>'tuldah', 295=>'tule', 296=>'tulda', 70=>'en tule', 71=>'ed tule, et tule', 72=>'ei tule', 73=>'emme tule', 78=>'ette tule', 79=>'ei tulda', 32=>'tulin', 33=>'tulid, tulit', 34=>'tuli', 35=>'tulimme', 36=>'tulitte', 37=>'tuldih', 80=>'en tulnu', 81=>'ed tulnu, et tulnu', 82=>'ei tulnu', 83=>'emme tulnu', 84=>'ette tulnu', 85=>'ei tuldu', 86=>'olen tulnu', 87=>'oled tulnu, olet tulnu', 88=>'on tulnu', 89=>'olemme tulnu', 90=>'olette tulnu', 91=>'oldah tuldu, on tuldu', 92=>'en ole tulnu', 93=>'ed ole tulnu, et ole tulnu', 94=>'ei ole tulnu', 95=>'emme ole tulnu', 96=>'ette ole tulnu', 97=>'ei olda tuldu', 98=>'olin tulnu', 99=>'olid tulnu, olit tulnu', 100=>'oli tulnu', 101=>'olimme tulnu', 102=>'olitte tulnu', 103=>'oldih tuldu', 104=>'en olnu tulnu', 105=>'ed olnu tulnu, et olnu tulnu', 107=>'ei olnu tulnu', 108=>'emme olnu tulnu', 106=>'ette olnu tulnu', 109=>'ei oldu tuldu', 51=>'tule', 52=>'tulgah', 53=>'tulgamme', 54=>'tulgatte', 55=>'tuldagah', 50=>'älä tule', 74=>'älgäh tulgah', 75=>'älgämme tulgamme', 76=>'älgätte tulgatte', 77=>'äldägäh tuldagah', 38=>'tuližin', 39=>'tuližid, tuližit', 40=>'tuliš', 41=>'tuližimme', 42=>'tuližitte', 43=>'tuldaiš', 301=>'tuliš', 303=>'tuldaiš', 110=>'en tuliš', 111=>'ed tuliš, et tuliš', 112=>'ei tuliš', 113=>'emme tuliš', 114=>'ette tuliš', 115=>'ei tuldaiš', 44=>'tulnuižin', 45=>'tulnuižid, tulnuižit', 46=>'tulnuiš', 47=>'tulnuižimme', 48=>'tulnuižitte', 49=>'tuldanuiš', 302=>'tulnuiš', 304=>'tuldanuiš', 116=>'en tulnuiš', 117=>'ed tulnuiš, et tulnuiš', 118=>'ei tulnuiš', 119=>'emme tulnuiš', 120=>'ette tulnuiš', 121=>'ei tuldanuiš', 122=>'oližin tulnu', 123=>'oližid tulnu, oližit tulnu', 124=>'oliš tulnu', 126=>'oližimme tulnu', 127=>'oližitte tulnu', 128=>'oldaiš tuldu', 129=>'en oliš tulnu', 130=>'ed oliš tulnu, et oliš tulnu', 131=>'ei oliš tulnu', 132=>'emme oliš tulnu', 133=>'ette oliš tulnu', 134=>'ei oldaiš tuldu', 135=>'olnuižin tulnu', 125=>'olnuižid tulnu, olnuižit tulnu', 136=>'olnuiš tulnu', 137=>'olnuižimme tulnu', 138=>'olnuižitte tulnu', 139=>'oldanuiš tuldu', 140=>'en olnuiš tulnu', 141=>'ed olnuiš tulnu, et olnuiš tulnu', 142=>'ei olnuiš tulnu', 143=>'emme olnuiš tulnu', 144=>'ette olnuiš tulnu', 145=>'ei oldanuiš tuldu', 146=>'tulnen', 147=>'tulned, tulnet', 148=>'tulnou', 149=>'tulnemme', 150=>'tulnette', 151=>'tuldaneh', 310=>'tulne', 311=>'tuldane', 152=>'en tulne', 153=>'ed tulne, et tulne', 154=>'ei tulne', 155=>'emme tulne', 156=>'ette tulne', 157=>'ei tuldane', 158=>'lienen tulnu', 159=>'liened tulnu, lienet tulnu', 160=>'lienöy tulnu', 161=>'lienemme tulnu', 162=>'lienette tulnu', 163=>'lienöy tuldu', 164=>'en liene tulnu', 165=>'ed liene tulnu, et liene tulnu', 166=>'ei liene tulnu', 167=>'emme liene tulnu', 168=>'ette liene tulnu', 169=>'ei liene tuldu', 170=>'tulda', 171=>'tuldes', 172=>'tulden', 173=>'tulemal', 174=>'tulemah', 175=>'tulemas', 176=>'tulemas', 177=>'tulemata', 178=>'tulii', 179=>'tulnu', 180=>'tuldav', 181=>'tuldu', ],
	    3525 => [26=>'mänen', 27=>'mäned, mänet', 28=>'mänöy', 29=>'mänemme', 30=>'mänette', 31=>'mändäh', 295=>'mäne', 296=>'mändä', 70=>'en mäne', 71=>'ed mäne, et mäne', 72=>'ei mäne', 73=>'emme mäne', 78=>'ette mäne', 79=>'ei mändä', 32=>'mänin', 33=>'mänid, mänit', 34=>'mäni', 35=>'mänimme', 36=>'mänitte', 37=>'mändih', 80=>'en männy', 81=>'ed männy, et männy', 82=>'ei männy', 83=>'emme männy', 84=>'ette männy', 85=>'ei mändy', 86=>'olen männy', 87=>'oled männy, olet männy', 88=>'on männy', 89=>'olemme männy', 90=>'olette männy', 91=>'oldah mändy, on mändy', 92=>'en ole männy', 93=>'ed ole männy, et ole männy', 94=>'ei ole männy', 95=>'emme ole männy', 96=>'ette ole männy', 97=>'ei olda mändy', 98=>'olin männy', 99=>'olid männy, olit männy', 100=>'oli männy', 101=>'olimme männy', 102=>'olitte männy', 103=>'oldih mändy, oli mändy', 104=>'en olnu männy', 105=>'ed olnu männy, et olnu männy', 107=>'ei olnu männy', 108=>'emme olnu männy', 106=>'ette olnu männy', 109=>'ei oldu mändy', 51=>'mäne', 52=>'mängäh', 53=>'mängämme', 54=>'mängätte', 55=>'mändägäh', 50=>'älä mäne', 74=>'älgäh mängäh', 75=>'älgämme mängämme', 76=>'älgätte mängätte', 77=>'äldägäh mändägäh', 38=>'mänižin', 39=>'mänižid, mänižit', 40=>'mäniš', 41=>'mänižimme', 42=>'mänižitte', 43=>'mändäiš', 301=>'mäniš', 303=>'mändäiš', 110=>'en mäniš', 111=>'ed mäniš, et mäniš', 112=>'ei mäniš', 113=>'emme mäniš', 114=>'ette mäniš', 115=>'ei mändäiš', 44=>'männyižin', 45=>'männyižid, männyižit', 46=>'männyiš', 47=>'männyižimme', 48=>'männyižitte', 49=>'mändänyiš', 302=>'männyiš', 304=>'mändänyiš', 116=>'en männyiš', 117=>'ed männyiš, et männyiš', 118=>'ei männyiš', 119=>'emme männyiš', 120=>'ette männyiš', 121=>'ei mändänyiš', 122=>'oližin männy', 123=>'oližid männy, oližit männy', 124=>'oliš männy', 126=>'oližimme männy', 127=>'oližitte männy', 128=>'oldaiš mändy', 129=>'en oliš männy', 130=>'ed oliš männy, et oliš männy', 131=>'ei oliš männy', 132=>'emme oliš männy', 133=>'ette oliš männy', 134=>'ei oldaiš mändy', 135=>'olnuižin männy', 125=>'olnuižid männy, olnuižit männy', 136=>'olnuiš männy', 137=>'olnuižimme männy', 138=>'olnuižitte männy', 139=>'oldanuiš mändy', 140=>'en olnuiš männy', 141=>'ed olnuiš männy, et olnuiš männy', 142=>'ei olnuiš männy', 143=>'emme olnuiš männy', 144=>'ette olnuiš männy', 145=>'ei oldanuiš mändy', 146=>'männen', 147=>'männed, männet', 148=>'männöy', 149=>'männemme', 150=>'männette', 151=>'mändäneh', 310=>'männe', 311=>'mändäne', 152=>'en männe', 153=>'ed männe, et männe', 154=>'ei männe', 155=>'emme männe', 156=>'ette männe', 157=>'ei mändäne', 158=>'lienen männy', 159=>'liened männy, lienet männy', 160=>'lienöy männy', 161=>'lienemme männy', 162=>'lienette männy', 163=>'lienöy mändy', 164=>'en liene männy', 165=>'ed liene männy, et liene männy', 166=>'ei liene männy', 167=>'emme liene männy', 168=>'ette liene männy', 169=>'ei liene mändy', 170=>'mändä', 171=>'mändes', 172=>'mänden', 173=>'mänemäl', 174=>'mänemäh', 175=>'mänemäs', 176=>'mänemäs', 177=>'mänemätä', 178=>'mänii', 179=>'männy', 180=>'mändäv', 181=>'mändy', ],
	    67094 => [26=>'puren', 27=>'pured, puret', 28=>'purou', 29=>'puremme', 30=>'purette', 31=>'purdah', 295=>'pure', 296=>'purda', 70=>'en pure', 71=>'ed pure, et pure', 72=>'ei pure', 73=>'emme pure', 78=>'ette pure', 79=>'ei purda', 32=>'purin', 33=>'purid, purit', 34=>'puri', 35=>'purimme', 36=>'puritte', 37=>'purdih', 80=>'en purnu', 81=>'ed purnu, et purnu', 82=>'ei purnu', 83=>'emme purnu', 84=>'ette purnu', 85=>'ei purdu', 86=>'olen purnu', 87=>'oled purnu, olet purnu', 88=>'on purnu', 89=>'olemme purnu', 90=>'olette purnu', 91=>'oldah purdu, on purdu', 92=>'en ole purnu', 93=>'ed ole purnu, et ole purnu', 94=>'ei ole purnu', 95=>'emme ole purnu', 96=>'ette ole purnu', 97=>'ei olda purdu, ei ole purdu', 98=>'olin purnu', 99=>'olid purnu, olit purnu', 100=>'oli purnu', 101=>'olimme purnu', 102=>'olitte purnu', 103=>'oldih purdu, oli purdu', 104=>'en olnu purnu', 105=>'ed olnu purnu, et en olnu purnu', 107=>'ei olnu purnu', 108=>'emme olnu purnu', 106=>'ette olnu purnu', 109=>'ei oldu purdu, ei olnu purdu', 51=>'pure', 52=>'purgah', 53=>'purgamme', 54=>'purgatte', 55=>'purdagah', 50=>'älä pure', 74=>'älgäh purgah', 75=>'älgämme purgamme', 76=>'älgätte purgatte', 77=>'äldägäh purdagah', 38=>'purižin', 39=>'purižid, purižit', 40=>'puriži', 41=>'puriš', 42=>'purižitte', 43=>'purdaiš', 301=>'puriš', 303=>'purdaiš', 110=>'en puriš', 111=>'ed puriš, et puriš', 112=>'ei puriš', 113=>'emme puriš', 114=>'ette puriš', 115=>'ei purdaiš', 44=>'purnuižin', 45=>'purnuižid, purnuižit', 46=>'purnuiš', 47=>'purnuižimme', 48=>'purnuižitte', 49=>'purdanuiš', 302=>'purnuiš', 304=>'purdanuiš', 116=>'en purnuiš', 117=>'ed purnuiš, et purnuiš', 118=>'ei purnuiš', 119=>'emme purnuiš', 120=>'ette purnuiš', 121=>'ei purdanuiš', 122=>'oližin purnu', 123=>'oližid purnu, oližit purnu', 124=>'oliš purnu', 126=>'oližimme purnu', 127=>'oližitte purnu', 128=>'oldaiš purdu', 129=>'en oliš purnu', 130=>'ed oliš purnu, et oliš purnu', 131=>'ei oliš purnu', 132=>'emme oliš purnu', 133=>'ette oliš purnu', 134=>'ei oldaiš purdu', 135=>'olnuižin purnu', 125=>'olnuižid purnu, olnuižit purnu', 136=>'olnuiš purnu', 137=>'olnuižimme purnu', 138=>'olnuižitte purnu', 139=>'oldanuiš purdu', 140=>'en olnuiš purnu', 141=>'ed olnuiš purnu, et olnuiš purnu', 142=>'ei olnuiš purnu', 143=>'emme olnuiš purnu', 144=>'ette olnuiš purnu', 145=>'ei oldanuiš purdu', 146=>'purnen', 147=>'purned, purnet', 148=>'purnou', 149=>'purnemme', 150=>'purnette', 151=>'purdaneh', 310=>'purne', 311=>'purdane', 152=>'en purne', 153=>'ed purne, et purne', 154=>'ei purne', 155=>'emme purne', 156=>'ette purne', 157=>'ei purdane', 158=>'lienen purnu', 159=>'liened purnu, lienet purnu', 160=>'lienöy purnu', 161=>'lienemme purnu', 162=>'lienette purnu', 163=>'lienöy purdu', 164=>'en liene purnu', 165=>'ed liene purnu, et liene purnu', 166=>'ei liene purnu', 167=>'emme liene purnu', 168=>'ette liene purnu', 169=>'ei liene purdu', 170=>'purda', 171=>'purdes', 172=>'purden', 173=>'puremal', 174=>'puremah', 175=>'puremas', 176=>'puremas', 177=>'puremata', 178=>'purii', 179=>'purnu', 180=>'purdav', 181=>'purdu', ],
	    22260 => [26=>'pagižen', 27=>'pagižed, pagižet', 28=>'pagižou', 29=>'pagižemme', 30=>'pagižette', 31=>'pagištah', 295=>'pagiže', 296=>'pagišta', 70=>'en pagiže', 71=>'ed pagiže, et pagiže', 72=>'ei pagiže', 73=>'emme pagiže', 78=>'ette pagiže', 79=>'ei pagišta', 32=>'pagižin', 33=>'pagižid, pagižit', 34=>'pagiži', 35=>'pagižimme', 36=>'pagižitte', 37=>'pagištih', 80=>'en pagišnu', 81=>'ed pagišnu, et pagišnu', 82=>'ei pagišnu', 83=>'emme pagišnu', 84=>'ette pagišnu', 85=>'ei pagištu', 86=>'olen pagišnu', 87=>'oled pagišnu, olet pagišnu', 88=>'on pagišnu', 89=>'olemme pagišnu', 90=>'olette pagišnu', 91=>'oldah pagištu, on pagištu', 92=>'en ole pagišnu', 93=>'ed ole pagišnu, et ole pagišnu', 94=>'ei ole pagišnu', 95=>'emme ole pagišnu', 96=>'ette ole pagišnu', 97=>'ei olda pagištu, ei ole pagištu', 98=>'olin pagišnu', 99=>'olid pagišnu, olit pagišnu', 100=>'oli pagišnu', 101=>'olimme pagišnu', 102=>'olitte pagišnu', 103=>'oldih pagištu, oli pagištu', 104=>'en olnu pagišnu', 105=>'ed olnu pagišnu, et olnu pagišnu', 107=>'ei olnu pagišnu', 108=>'emme olnu pagišnu', 106=>'ette olnu pagišnu', 109=>'ei oldu pagištu, ei olnu pagištu', 51=>'pagiže', 52=>'pagiškah', 53=>'pagiškamme', 54=>'pagiškatte', 55=>'pagištakkah', 50=>'älä pagiže', 74=>'älgäh pagiškah', 75=>'älgämme pagiškamme', 76=>'älgät pagiškat, älgättee pagiškatte', 77=>'äldägäh pagištakkah', 38=>'pagižižin', 39=>'pagižižid, pagižižit', 40=>'pagižiš', 41=>'pagižižimme', 42=>'pagižižitte', 43=>'pagištaiš', 301=>'pagižiš', 303=>'pagištaiš', 110=>'en pagižiš', 111=>'ed pagižiš, et pagižiš', 112=>'ei pagižiš', 113=>'emme pagižiš', 114=>'ette pagižiš', 115=>'ei pagištaiš', 44=>'pagišnuižin', 45=>'pagišnuižid, pagišnuižit', 46=>'pagišnuiš', 47=>'pagišnuižimme', 48=>'pagišnuižitte', 49=>'pagištannuiš', 302=>'pagišnuiš', 304=>'pagištannuiš', 116=>'en pagišnuiš', 117=>'ed pagišnuiš, et pagišnuiš', 118=>'ei pagišnuiš', 119=>'emme pagišnuiš', 120=>'ette pagišnuiš', 121=>'ei pagištannuiš', 122=>'oližin pagišnu', 123=>'oližid pagišnu, oližit pagišnu', 124=>'oliš pagišnu', 126=>'oližimme pagišnu', 127=>'oližitte pagišnu', 128=>'oldaiš pagištu', 129=>'en oliš pagišnu', 130=>'ed oliš pagišnu, et oliš pagišnu', 131=>'ei oliš pagišnu', 132=>'emme oliš pagišnu', 133=>'ette oliš pagišnu', 134=>'ei oldaiš pagištu', 135=>'olnuižin pagišnu', 125=>'olnuižid pagišnu, olnuižit pagišnu', 136=>'olnuiš pagišnu', 137=>'olnuižimme pagišnu', 138=>'olnuižitte pagišnu', 139=>'oldanuiš pagištu', 140=>'en olnuiš pagišnu', 141=>'ed olnuiš pagišnu, et olnuiš pagišnu', 142=>'ei olnuiš pagišnu', 143=>'emme olnuiš pagišnu', 144=>'ette olnuiš pagišnu', 145=>'ei oldanuiš pagištu', 146=>'pagišnen', 147=>'pagišned, pagišnet', 148=>'pagišnou', 149=>'pagišnemme', 150=>'pagišnette', 151=>'pagištanneh', 310=>'pagišne', 311=>'pagištanne', 152=>'en pagišne', 153=>'ed pagišne, et pagišne', 154=>'ei pagišne', 155=>'emme pagišne', 156=>'ette pagišne', 157=>'ei pagištanne', 158=>'lienen pagišnu', 159=>'liened pagišnu, lienet pagišnu', 160=>'lienöy pagišnu', 161=>'lienemme pagišnu', 162=>'lienette pagišnu', 163=>'lienöy pagištu', 164=>'en liene pagišnu', 165=>'ed liene pagišnu, et liene pagišnu', 166=>'ei liene pagišnu', 167=>'emme liene pagišnu', 168=>'ette liene pagišnu', 169=>'ei liene pagištu', 170=>'pagišta', 171=>'pagištes', 172=>'pagišten', 173=>'pagižemal', 174=>'pagižemah', 175=>'pagižemas', 176=>'pagižemas', 177=>'pagižemata', 178=>'pagižii', 179=>'pagišnu', 180=>'pagištav', 181=>'pagištu', ],
	    44615 => [26=>'pezen', 27=>'pezed, pezet', 28=>'pezöy', 29=>'pezemme', 30=>'pezette', 31=>'pestäh', 295=>'peze', 296=>'pestä', 70=>'en peze', 71=>'ed peze, et peze', 72=>'ei peze', 73=>'emme peze', 78=>'ette peze', 79=>'ei pestä', 32=>'pezin', 33=>'pezid, pezit', 34=>'pezi', 35=>'pezimme', 36=>'pezitte', 37=>'pestih', 80=>'en pesny', 81=>'ed pesny, et pesny', 82=>'ei pesny', 83=>'emme pesny', 84=>'ette pesny', 85=>'ei pesty', 86=>'olen pesny', 87=>'oled pesny, olet pesny', 88=>'on pesny', 89=>'olemme pesny', 90=>'olette pesny', 91=>'on pesty', 92=>'en ole pesny', 93=>'ed ole pesny, et ole pesny', 94=>'ei ole pesny', 95=>'emme ole pesny', 96=>'ette ole pesny', 97=>'ei olda pesty, ei ole pesty', 98=>'olin pesny', 99=>'olid pesny, olit pesny', 100=>'oli pesny', 101=>'olimme pesny', 102=>'olitte pesny', 103=>'oli pesty', 104=>'en olnu pesny', 105=>'ed olnu pesny, et olnu pesny', 107=>'ei olnu pesny', 108=>'emme olnu pesny', 106=>'ette olnu pesny', 109=>'ei oldu pesty', 51=>'peze', 52=>'peskäh', 53=>'peskämme', 54=>'peskätte', 55=>'pestäkkäh', 50=>'älä peze', 74=>'älgäh peskäh', 75=>'älgämme peskämme', 76=>'älgätte peskätte', 77=>'äldägäh pestäkkäh', 38=>'pezižin', 39=>'pezižid, pezižit', 40=>'peziš', 41=>'pezižimme', 42=>'pezižitte', 43=>'pestäiš', 301=>'peziš', 303=>'pestäiš', 110=>'en peziš', 111=>'ed peziš, et peziš', 112=>'ei peziš', 113=>'emme peziš', 114=>'ette peziš', 115=>'ei pestäiš', 44=>'pesnyižin', 45=>'pesnyižid, pesnyižit', 46=>'pesnyiš', 47=>'pesnyižimme', 48=>'pesnyižitte', 49=>'pestänyiš', 302=>'pesnyiš', 304=>'pestänyiš', 116=>'en pesnyiš', 117=>'ed pesnyiš, et pesnyiš', 118=>'ei pesnyiš', 119=>'emme pesnyiš', 120=>'ette pesnyiš', 121=>'ei pestänyiš', 122=>'oližin pesny', 123=>'oližid pesny, oližit pesny', 124=>'oliš pesny', 126=>'oližimme pesny', 127=>'oližitte pesny', 128=>'oldaiš pesty', 129=>'en oliš pesny', 130=>'ed oliš pesny, et oliš pesny', 131=>'ei oliš pesny', 132=>'emme oliš pesny', 133=>'ette oliš pesny', 134=>'ei oldaiš pesty', 135=>'olnuižin pesny', 125=>'olnuižid pesny, olnuižit pesny', 136=>'olnuiš pesny', 137=>'olnuižimme pesny', 138=>'olnuižitte pesny', 139=>'oldanuiš pesty', 140=>'en olnuiš pesny', 141=>'ed olnuiš pesny, et olnuiš pesny', 142=>'ei olnuiš pesny', 143=>'emme olnuiš pesny', 144=>'ette olnuiš pesny', 145=>'ei oldanuiš pesty', 146=>'pesnen', 147=>'pesned, pesnet', 148=>'pesnou', 149=>'pesnemme', 150=>'pesnette', 151=>'pestäneh', 310=>'pesne', 311=>'pestäne', 152=>'en pesne', 153=>'ed pesne, et pesne', 154=>'ei pesne', 155=>'emme pesne', 156=>'ette pesne', 157=>'ei pestäne', 158=>'lienen pesny', 159=>'liened pesny, lienet pesny', 160=>'lienöy pesny', 161=>'lienemme pesny', 162=>'lienette pesny', 163=>'lienöy pesty', 164=>'en liene pesny', 165=>'ed liene pesny, et liene pesny', 166=>'ei liene pesny', 167=>'emme liene pesny', 168=>'ette liene pesny', 169=>'ei liene pesty', 170=>'pestä', 171=>'pestes', 172=>'pesten', 173=>'pezemäl', 174=>'pezemäh', 175=>'pezemäs', 176=>'pezemäs', 177=>'pezemätä', 178=>'pezii', 179=>'pesny', 180=>'pestäv', 181=>'pesty', ],
	    43235 => [26=>'magadan', 27=>'magadad, magadat', 28=>'magadau', 29=>'magadamme', 30=>'magadatte', 31=>'magatah', 295=>'magada', 296=>'magata', 70=>'en magada', 71=>'ed magada, et magada', 72=>'ei magada', 73=>'emme magada', 78=>'ette magada', 79=>'ei magata', 32=>'magažin', 33=>'magažid, magažit', 34=>'magaži', 35=>'magažimme', 36=>'magažitte', 37=>'magattih', 80=>'en magannu', 81=>'ed magannu, et magannu', 82=>'ei magannu', 83=>'emme magannu', 84=>'ette magannu', 85=>'ei magattu', 86=>'olen magannu', 87=>'oled magannu, olet magannu', 88=>'on magannu', 89=>'olemme magannu', 90=>'olette magannu', 91=>'oldah magattu, on magattu', 92=>'en ole magannu', 93=>'ed ole magannu, et ole magannu', 94=>'ei ole magannu', 95=>'emme ole magannu', 96=>'ette ole magannu', 97=>'ei olda magattu', 98=>'olin magannu', 99=>'olid magannu, olit magannu', 100=>'oli magannu', 101=>'olimme magannu', 102=>'olitte magannu', 103=>'oldih magattu, oli magattu', 104=>'en olnu magannu', 105=>'ed olnu magannu, et olnu magannu', 107=>'ei olnu magannu', 108=>'emme olnu magannu', 106=>'ette olnu magannu', 109=>'ei oldu magattu', 51=>'magada', 52=>'magakkah', 53=>'magakkamme', 54=>'magakkatte', 55=>'magakkah', 50=>'älä magada', 74=>'älgäh magakkah', 75=>'älgämme magakkamme', 76=>'älgätte magakkatte', 77=>'äldägäh magatakkah', 38=>'magadaižin', 39=>'magadaižid, magadaižit', 40=>'magadaiš', 41=>'magadaižimme', 42=>'magadaižitte', 43=>'magataiš', 301=>'magadaiš', 303=>'magataiš', 110=>'en magadaiš', 111=>'ed magadaiš, et magadaiš', 112=>'ei magadaiš', 113=>'emme magadaiš', 114=>'ette magadaiš', 115=>'ei magataiš', 44=>'magannuižin', 45=>'magannuižid, magannuižit', 46=>'magannuiš', 47=>'magannuižimme', 48=>'magannuižitte', 49=>'magatannuiš', 302=>'magannuiš', 304=>'magatannuiš', 116=>'en magannuiš', 117=>'ed magannuiš, et magannuiš', 118=>'ei magannuiš', 119=>'emme magannuiš', 120=>'ette magannuiš', 121=>'ei magatannuiš', 122=>'oližin magannu', 123=>'oližid magannu, oližit magannu', 124=>'oliš magannu', 126=>'oližimme magannu', 127=>'oližitte magannu', 128=>'oldaiš magattu', 129=>'en oliš magannu', 130=>'ed oliš magannu, et oliš magannu', 131=>'ei oliš magannu', 132=>'emme oliš magannu', 133=>'ette oliš magannu', 134=>'ei oldaiš magattu', 135=>'olnuižin magannu', 125=>'olnuižid magannu, olnuižit magannu', 136=>'olnuiš magannu', 137=>'olnuižimme magannu', 138=>'olnuižitte magannu', 139=>'oldanuiš magattu', 140=>'en olnuiš magannu', 141=>'ed olnuiš magannu, et olnuiš magannu', 142=>'ei olnuiš magannu', 143=>'emme olnuiš magannu', 144=>'ette olnuiš magannu', 145=>'ei oldanuiš magattu', 146=>'magannen', 147=>'maganned, magannet', 148=>'magannou', 149=>'magannemme', 150=>'magannette', 151=>'magatanneh', 310=>'maganne', 311=>'magatanne', 152=>'en maganne', 153=>'ed maganne, et maganne', 154=>'ei maganne', 155=>'emme maganne', 156=>'ette maganne', 157=>'ei magatanne', 158=>'lienen magannu', 159=>'liened magannu, lienet magannu', 160=>'lienöy magannu', 161=>'lienemme magannu', 162=>'lienette magannu', 163=>'lienöy magattu', 164=>'en liene magannu', 165=>'ed liene magannu, et liene magannu', 166=>'ei liene magannu', 167=>'emme liene magannu', 168=>'ette liene magannu', 169=>'ei liene magattu', 170=>'magata', 171=>'magates', 172=>'magaten', 173=>'magadamal', 174=>'magadamah', 175=>'magadamas', 176=>'magadamas', 177=>'magadamata', 178=>'magadai', 179=>'magannu', 180=>'magattav', 181=>'magattu', ],
	    41869 => [26=>'rubedan', 27=>'rubedad, rubedat', 28=>'rubedau', 29=>'rubedamme', 30=>'rubedatte', 31=>'rubetah', 295=>'rubeda', 296=>'rubeta', 70=>'en rubeda', 71=>'ed rubeda, et rubeda', 72=>'ei rubeda', 73=>'emme rubeda', 78=>'ette rubeda', 79=>'ei rubeta', 32=>'rubežin', 33=>'rubežid, rubežit', 34=>'rubeži', 35=>'rubežimme', 36=>'rubežitte', 37=>'rubettih', 80=>'en rubennu', 81=>'ed rubennu, et rubennu', 82=>'ei rubennu', 83=>'emme rubennu', 84=>'ette rubennu', 85=>'ei rubettu', 86=>'olen rubennu', 87=>'oled rubennu, olet rubennu', 88=>'on rubennu', 89=>'olemme rubennu', 90=>'olette rubennu', 91=>'on rubettu', 92=>'en ole rubennu', 93=>'ed ole rubennu, et ole rubennu', 94=>'ei ole rubennu', 95=>'emme ole rubennu', 96=>'ette ole rubennu', 97=>'ei ole rubettu', 98=>'olin rubennu', 99=>'olid rubennu, olit rubennu', 100=>'oli rubennu', 101=>'olimme rubennu', 102=>'olitte rubennu', 103=>'oli rubettu', 104=>'en olnu rubennu', 105=>'ed olnu rubennu, et olnu rubennu', 107=>'ei olnu rubennu', 108=>'emme olnu rubennu', 106=>'ette olnu rubennu', 109=>'ei olnu rubettu', 51=>'rubeda', 52=>'rubekkah', 53=>'rubekkamme', 54=>'rubekkatte', 55=>'rubetakkah', 50=>'älä rubeda', 74=>'älgäh rubekkah', 75=>'älgämme rubekkamme', 76=>'älgätte rubekkatte', 77=>'äldägäh rubetakkah', 38=>'rubedaižin', 39=>'rubedaižid, rubedaižit', 40=>'rubedaiš', 41=>'rubedaižimme', 42=>'rubedaižitte', 43=>'rubetaiš', 301=>'rubedaiš', 303=>'rubetaiš', 110=>'en rubedaiš', 111=>'ed rubedaiš, et rubedaiš', 112=>'ei rubedaiš', 113=>'emme rubedaiš', 114=>'ette rubedaiš', 115=>'ei rubetaiš', 44=>'rubennuižin', 45=>'rubennuižid, rubennuižit', 46=>'rubennuiš', 47=>'rubennuižimme', 48=>'rubennuižitte', 49=>'rubetannuiš', 302=>'rubennuiš', 304=>'rubetannuiš', 116=>'en rubennuiš', 117=>'ed rubennuiš, et rubennuiš', 118=>'ei rubennuiš', 119=>'emme rubennuiš', 120=>'ette rubennuiš', 121=>'ei rubetannuiš', 122=>'oližin rubennu', 123=>'oližid rubennu, oližit rubennu', 124=>'oliš rubennu', 126=>'oližimme rubennu', 127=>'oližitte rubennu', 128=>'oldaiš rubettu', 129=>'en oliš rubennu', 130=>'ed oliš rubennu, et oliš rubennu', 131=>'ei oliš rubennu', 132=>'emme oliš rubennu', 133=>'ette oliš rubennu', 134=>'ei oldaiš rubettu', 135=>'olnuižin rubennu', 125=>'olnuižid rubennu, olnuižit rubennu', 136=>'olnuiš rubennu', 137=>'olnuižimme rubennu', 138=>'olnuižitte rubennu', 139=>'oldanuiš rubettu', 140=>'en olnuiš rubennu', 141=>'ed olnuiš rubennu, et olnuiš rubennu', 142=>'ei olnuiš rubennu', 143=>'emme olnuiš rubennu', 144=>'ette olnuiš rubennu', 145=>'ei oldanuiš rubettu', 146=>'rubennen', 147=>'rubenned, rubennet', 148=>'rubennou', 149=>'rubennemme', 150=>'rubennette', 151=>'rubetanneh', 310=>'rubenne', 311=>'rubetanne', 152=>'en rubenne', 153=>'ed rubenne, et rubenne', 154=>'ei rubenne', 155=>'emme rubenne', 156=>'ette rubenne', 157=>'ei rubetanne', 158=>'lienen rubennu', 159=>'liened rubennu, lienet rubennu', 160=>'lienöy rubennu', 161=>'lienemme rubennu', 162=>'lienette rubennu', 163=>'lienöy rubettu', 164=>'en liene rubennu', 165=>'ed liene rubennu, et liene rubennu', 166=>'ei liene rubennu', 167=>'emme liene rubennu', 168=>'ette liene rubennu', 169=>'ei liene rubettu', 170=>'rubeta', 171=>'rubetes', 172=>'rubeten', 173=>'rubedamal', 174=>'rubedamah', 175=>'rubedamas', 176=>'rubedamas', 177=>'rubedamata', 178=>'rubedai', 179=>'rubennu', 180=>'rubettav', 181=>'rubettu', ],
	    70904 => [26=>'haravoičen', 27=>'haravoičed, haravoičet', 28=>'haravoiččou', 29=>'haravoičemme', 30=>'haravoičette', 31=>'haravoitah', 295=>'haravoiče', 296=>'haravoita', 70=>'en haravoiče', 71=>'ed haravoiče, et haravoiče', 72=>'ei haravoiče', 73=>'emme haravoiče', 78=>'ette haravoiče', 79=>'ei haravoita', 32=>'haravoičin', 33=>'haravoičid, haravoičit', 34=>'haravoičči', 35=>'haravoičimme', 36=>'haravoičitte', 37=>'haravoittih', 80=>'en haravoinnu', 81=>'ed haravoinnu, et haravoinnu', 82=>'ei haravoinnu', 83=>'emme haravoinnu', 84=>'ette haravoinnu', 85=>'ei haravoittu', 86=>'olen haravoinnu', 87=>'oled haravoinnu, olet haravoinnu', 88=>'on haravoinnu', 89=>'olemme haravoinnu', 90=>'olette haravoinnu', 91=>'oldah haravoittu, on haravoittu', 92=>'en ole haravoinnu', 93=>'ed ole haravoinnu, et ole haravoinnu', 94=>'ei ole haravoinnu', 95=>'emme ole haravoinnu', 96=>'ette ole haravoinnu', 97=>'ei olda haravoittu', 98=>'olin haravoinnu', 99=>'olid haravoinnu, olit haravoinnu', 100=>'oli haravoinnu', 101=>'olimme haravoinnu', 102=>'olitte haravoinnu', 103=>'oldih haravoittu, oli haravoittu', 104=>'en olnu haravoinnu', 105=>'ed olnu haravoinnu, et olnu haravoinnu', 107=>'ei olnu haravoinnu', 108=>'emme olnu haravoinnu', 106=>'ette olnu haravoinnu', 109=>'ei oldu haravoittu', 51=>'haravoiče', 52=>'haravoikkah', 53=>'haravoikkamme', 54=>'haravoikkatte', 55=>'haravoitakkah', 50=>'älä haravoiče', 74=>'älgäh haravoikkah', 75=>'älgämme haravoikkamme', 76=>'älgätte haravoikkatte', 77=>'äldägäh haravoitakkah', 38=>'haravoiččižin', 39=>'haravoiččižid, haravoiččižit', 40=>'haravoiččiš', 41=>'haravoiččižimme', 42=>'haravoiččižitte', 43=>'haravoitaiš', 301=>'haravoiččiš', 303=>'haravoitaiš', 110=>'en haravoiččiš', 111=>'ed haravoiččiš, et haravoiččiš', 112=>'ei haravoiččiš', 113=>'emme haravoiččiš', 114=>'ette haravoiččiš', 115=>'ei haravoitaiš', 44=>'haravoinnuižin', 45=>'haravoinnuižid, haravoinnuižit', 46=>'haravoinnuiš', 47=>'haravoinnuižimme', 48=>'haravoinnuižitte', 49=>'haravoittanuiš', 302=>'haravoinnuiš', 304=>'haravoittanuiš', 116=>'en haravoinnuiš', 117=>'ed haravoinnuiš, et haravoinnuiš', 118=>'ei haravoinnuiš', 119=>'emme haravoinnuiš', 120=>'ette haravoinnuiš', 121=>'ei haravoittanuiš', 122=>'oližin haravoinnu', 123=>'oližid haravoinnu, oližit haravoinnu', 124=>'oliš haravoinnu', 126=>'oližimme haravoinnu', 127=>'oližitte haravoinnu', 128=>'oldaiš haravoittu', 129=>'en oliš haravoinnu', 130=>'ed oliš haravoinnu, et oliš haravoinnu', 131=>'ei oliš haravoinnu', 132=>'emme oliš haravoinnu', 133=>'ette oliš haravoinnu', 134=>'ei oldaiš haravoittu', 135=>'olnuižin haravoinnu', 125=>'olnuižid haravoinnu, olnuižit haravoinnu', 136=>'olnuiš haravoinnu', 137=>'olnuižimme haravoinnu', 138=>'olnuižitte haravoinnu', 139=>'oldanuiš haravoittu', 140=>'en olnuiš haravoinnu', 141=>'ed olnuiš haravoinnu, et olnuiš haravoinnu', 142=>'ei olnuiš haravoinnu', 143=>'emme olnuiš haravoinnu', 144=>'ette olnuiš haravoinnu', 145=>'ei oldanuiš haravoittu', 146=>'haravoinnen', 147=>'haravoinned, haravoinnet', 148=>'haravoinnou', 149=>'haravoinnemme', 150=>'haravoinnette', 151=>'haravoittaneh', 310=>'haravoinne', 311=>'haravoittane', 152=>'en haravoinne', 153=>'ed haravoinne, et haravoinne', 154=>'ei haravoinne', 155=>'emme haravoinne', 156=>'ette haravoinne', 157=>'ei haravoittane', 158=>'lienen haravoinnu', 159=>'liened haravoinnu, lienet haravoinnu', 160=>'lienöy haravoinnu', 161=>'lienemme haravoinnu', 162=>'lienette haravoinnu', 163=>'lienöy haravoittu', 164=>'en liene haravoinnu', 165=>'ed liene haravoinnu, et liene haravoinnu', 166=>'ei liene haravoinnu', 167=>'emme liene haravoinnu', 168=>'ette liene haravoinnu', 169=>'ei liene haravoittu', 170=>'haravoita', 171=>'haravoites', 172=>'haravoiten', 173=>'haravoiččemal', 174=>'haravoiččemah', 175=>'haravoiččemas', 176=>'haravoiččemas', 177=>'haravoiččemata', 178=>'haravoiččii', 179=>'haravoinnu', 180=>'haravoittav', 181=>'haravoittu', ],
	    62330 => [26=>'suvaičen', 27=>'suvaičed, suvaičet', 28=>'suvaiččou', 29=>'suvaičemme', 30=>'suvaičette', 31=>'suvaitah', 295=>'suvaiče', 296=>'suvaita', 70=>'en suvaiče', 71=>'ed suvaiče, et suvaiče', 72=>'ei suvaiče', 73=>'emme suvaiče', 78=>'ette suvaiče', 79=>'ei suvaita', 32=>'suvaičin', 33=>'suvaičid, suvaičit', 34=>'suvaičči', 35=>'suvaičimme', 36=>'suvaičitte', 37=>'suvaittih', 80=>'en suvainnu', 81=>'ed suvainnu, et suvainnu', 82=>'ei suvainnu', 83=>'emme suvainnu', 84=>'ette suvainnu', 85=>'ei suvaittu', 86=>'olen suvainnu', 87=>'oled suvainnu, olet suvainnu', 88=>'on suvainnu', 89=>'olemme suvainnu', 90=>'olette suvainnu', 91=>'on suvaittu', 92=>'en ole suvainnu', 93=>'ed ole suvainnu, et ole suvainnu', 94=>'ei ole suvainnu', 95=>'emme ole suvainnu', 96=>'ette ole suvainnu', 97=>'ei ole suvaittu', 98=>'olin suvainnu', 99=>'olid suvainnu, olit suvainnu', 100=>'oli suvainnu', 101=>'olimme suvainnu', 102=>'olitte suvainnu', 103=>'oli suvaittu', 104=>'en olnu suvainnu', 105=>'ed olnu suvainnu, et olnu suvainnu', 107=>'ei olnu suvainnu', 108=>'emme olnu suvainnu', 106=>'ette olnu suvainnu', 109=>'ei olnu suvaittu', 51=>'suvaiče', 52=>'suvaikkah', 53=>'suvaikkamme', 54=>'suvaikkatte', 55=>'suvaitakkah', 50=>'älä suvaiče', 74=>'älgäh suvaikkah', 75=>'älgämme suvaikkamme', 76=>'älgätte suvaikkatte', 77=>'äldägäh suvaitakkah', 38=>'suvaiččižin', 39=>'suvaiččižid, suvaiččižit', 40=>'suvaiččiš', 41=>'suvaiččižimme', 42=>'suvaiččižitte', 43=>'suvaitaiš', 301=>'suvaiččiš', 303=>'suvaitaiš', 110=>'en suvaiččiš', 111=>'ed suvaiččiš', 112=>'ei suvaiččiš', 113=>'emme suvaiččiš', 114=>'ette suvaiččiš', 115=>'ei suvaitaiš', 44=>'suvainnuižin', 45=>'suvainnuižid, suvainnuižit', 46=>'suvainnuiš', 47=>'suvainnuižimme', 48=>'suvainnuižitte', 49=>'suvaitannuiš', 302=>'suvainnuiš', 304=>'suvaitannuiš', 116=>'en suvainnuiš', 117=>'ed suvainnuiš, et suvainnuiš', 118=>'ei suvainnuiš', 119=>'emme suvainnuiš', 120=>'ette suvainnuiš', 121=>'ei suvaitannuiš', 122=>'oližin suvainnu', 123=>'oližid suvainnu, oližit suvainnu', 124=>'oliš suvainnu', 126=>'oližimme suvainnu', 127=>'oližitte suvainnu', 128=>'oldaiš suvaittu', 129=>'en oliš suvainnu', 130=>'ed oliš suvainnu', 131=>'ei oliš suvainnu', 132=>'emme oliš suvainnu', 133=>'ette oliš suvainnu', 134=>'ei oldaiš suvaittu', 135=>'olnuižin suvainnu', 125=>'olnuižid suvainnu, olnuižit suvainnu', 136=>'olnuiš suvainnu', 137=>'olnuižimme suvainnu', 138=>'olnuižitte suvainnu', 139=>'oldanuiš suvaittu', 140=>'en olnuiš suvainnu', 141=>'ed olnuiš suvainnu, et olnuiš suvainnu', 142=>'ei olnuiš suvainnu', 143=>'emme olnuiš suvainnu', 144=>'ette olnuiš suvainnu', 145=>'ei oldanuiš suvaittu', 146=>'suvainnen', 147=>'suvainned, suvainnet', 148=>'suvainnou', 149=>'suvainnemme', 150=>'suvainnette', 151=>'suvaitanneh', 310=>'suvainne', 311=>'suvaitanne', 152=>'en suvainne', 153=>'ed suvainne, et suvainne', 154=>'ei suvainne', 155=>'emme suvainne', 156=>'ette suvainne', 157=>'ei suvaitanne', 158=>'lienen suvainnu', 159=>'liened suvainnu, lienet suvainnu', 160=>'lienöy suvainnu', 161=>'lienemme suvainnu', 162=>'lienette suvainnu', 163=>'lienöy suvaittu', 164=>'en liene suvainnu', 165=>'ed liene suvainnu, et liene suvainnu', 166=>'ei liene suvainnu', 167=>'emme liene suvainnu', 168=>'ette liene suvainnu', 169=>'ei liene suvaittu', 170=>'suvaita', 171=>'suvaites', 172=>'suvaiten', 173=>'suvaiččemal', 174=>'suvaiččemah', 175=>'suvaiččemas', 176=>'suvaiččemas', 177=>'suvaiččemata', 178=>'suvaiččii', 179=>'suvainnu', 180=>'suvaittav', 181=>'suvaittu', ],
	];
        $this->assertEquals( $expected, $result);        
    }

    /*    
    public function testWordformsByStemsKarVerbProper() {
        $template = '';
        $lang_id = 4;
        $pos_id = 11;
        $name_num=null;
        $dialect_id='';
        $is_reflexive = false;
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id, $is_reflexive);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems, $is_reflexive);
        $expected = [26 => '',   27 => '',  28 => '',  
                29 => '',  30 => '',  31 => '', 295 => '', 296 => '', 
                70 => '',   71 => '',  72 => '',  
                73 => '',  78 => '',  79 => '', 
            
                32 => '',   33 => '',  34 => '',  
                35 => '',  36 => '',  37 => '', 297 => '', 298 => '', 
                80 => '',   81 => '',  82 => '',  
                83 => '',  84 => '',  85 => '', 
            
                86 => '',   87 => '',  88 => '',  
                89 => '',  90 => '',  91 => '',  
                92 => '',  93 => '',  94 => '',  
                95 => '',  96 => '',  97 => '',
            
                98 => '',   99 => '', 100 => '', 
                101 => '', 102 => '', 103 => '', 
                104 => '', 105 => '', 107 => '', 
                108 => '', 106 => '', 109 => '',
            
                      51 => '',  52 => '',  
                53 => '',  54 => '',  55 => '',       
                      50 => '',  74 => '',  
                75 => '',  76 => '',  77 => '',  
            
                44 => '',   45 => '',  46 => '',  
                47 => '',  48 => '',  49 => '', 
                116 => '', 117 => '', 118 => '', 
                119 => '', 120 => '', 121 => '',
            
                135 => '', 125 => '', 136 => '', 
                137 => '', 138 => '', 139 => '', 
                140 => '', 141 => '', 142 => '', 
                143 => '', 144 => '', 145 => '',
            
                146 => '', 147 => '', 148 => '', 310 => '', 311 => '', 
                149 => '', 150 => '', 151 => '', 
                152 => '', 153 => '', 154 => '', 
                155 => '', 156 => '', 157 => '',
            
                158 => '', 159 => '', 160 => '', 
                161 => '', 162 => '', 163 => '', 
                164 => '', 165 => '', 166 => '', 
                167 => '', 168 => '', 169 => '',
            
                170 => '', 171 => '', 172 => '', 
                173 => '', 174 => '', 175 => '', 
                176 => '', 177 => '', 
            
                178 => '', 179 => '', 282=>'', 180 => '', 181 => ''];
//        $slice = 100;
//        $this->assertEquals(array_slice($expected, 0, $slice, true), array_slice($result, 0, $slice, true));        
        $this->assertEquals( $expected, $result);        
    }

    public function testWordformsByStemsKarNameOloP() {
        $template = '';
        $lang_id = 5;
        $pos_id = 5;
        $name_num=null;
        $dialect_id='44';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'',  56=>'', 3=>'',  4=>'', 277=>'',  
                     5=>'', 6=>'', 8=>'',  9=>'', 10=>'',  
                     11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 
 
            2=>'', 57=>'', 24=>'', 22=>'', 279=>'', 
            59=>'', 64=>'', 23=>'', 60=>'', 61=>'',  
            25=>'', 62=>'', 63=>'', 65=>'', 66=>'', 281=>''];
        $this->assertEquals( $expected, $result);        
    }
  
    public function testWordformsByStemsKarVerbOlo() {
        $template = '';
        $lang_id = 5;
        $pos_id = 11;
        $name_num=null;
        $dialect_id='';
        $is_reflexive = false;
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id, $is_reflexive);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems, $is_reflexive);
        $expected = [26 => '',   27 => '',  28 => '',  
                29 => '',  30 => '',  31 => '', 295 => '', 296 => '', 
                70 => '',   71 => '',  72 => '',  
                73 => '',  78 => '',  79 => '', 
            
                32 => '',   33 => '',  34 => '',  
                35 => '',  36 => '',  37 => '', 
                80 => '',   81 => '',  82 => '',  
                83 => '',  84 => '',  85 => '', 
            
                86 => '',   87 => '',  88 => '',  
                89 => '',  90 => '',  91 => '',  
                92 => '',  93 => '',  94 => '',  
                95 => '',  96 => '',  97 => '',
            
                98 => '',   99 => '', 100 => '', 
                101 => '', 102 => '', 103 => '', 
                104 => '', 105 => '', 107 => '', 
                108 => '', 106 => '', 109 => '',
            
                      51 => '',  52 => '',  
                53 => '',  54 => '',  55 => '',       
                      50 => '',  74 => '',  
                75 => '',  76 => '',  77 => '',  
            
                38 => '',   39 => '',  40 => '',  
                41 => '',  42 => '',  43 => '', 
                110 => '', 111 => '', 112 => '', 
                113 => '', 114 => '', 115 => '',

                44 => '',   45 => '',  46 => '',  
                47 => '',  48 => '',  49 => '', 
                116 => '', 117 => '', 118 => '', 
                119 => '', 120 => '', 121 => '',
            
                122 => '', 123 => '', 124 => '', 
                126 => '', 127 => '', 128 => '', 
                129 => '', 130 => '', 131 => '', 
                132 => '', 133 => '', 134 => '',
            
                135 => '', 125 => '', 136 => '', 
                137 => '', 138 => '', 139 => '', 
                140 => '', 141 => '', 142 => '', 
                143 => '', 144 => '', 145 => '',
            
                146 => '', 147 => '', 148 => '', 
                149 => '', 150 => '', 151 => '', 
                152 => '', 153 => '', 154 => '', 
                155 => '', 156 => '', 157 => '',
            
                158 => '', 159 => '', 160 => '', 
                161 => '', 162 => '', 163 => '', 
                164 => '', 165 => '', 166 => '', 
                167 => '', 168 => '', 169 => '',
            
                170 => '', 171 => '', 172 => '', 
                173 => '', 174 => '', 175 => '', 
                176 => '', 177 => '', 312 => '',
            
                178 => '', 179 => '', 180 => '', 181 => ''];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformsByStemsKarVerbOlo() {
        $template = '';
        $lang_id = 5;
        $pos_id = 11;
        $name_num=null;
        $dialect_id='';
        $is_reflexive = false;
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id, $is_reflexive);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [26 => '',   27 => '',  28 => '',  29 => '',  30 => '',  31 => '', 295 => '', 296 => '', 
                70 => '',   71 => '',  72 => '',  73 => '',  78 => '',  79 => '', 
                32 => '',   33 => '',  34 => '',  35 => '',  36 => '',  37 => '', 
                80 => '',   81 => '',  82 => '',  83 => '',  84 => '',  85 => '', 
                86 => '',   87 => '',  88 => '',  89 => '',  90 => '',  91 => '',  92 => '',  93 => '',  94 => '',  95 => '',  96 => '',  97 => '',
                98 => '',   99 => '', 100 => '', 101 => '', 102 => '', 103 => '', 104 => '', 105 => '', 107 => '', 108 => '', 106 => '', 109 => '',
                      51 => '',  52 => '',  53 => '',  54 => '',  55 => '',       50 => '',  74 => '',  75 => '',  76 => '',  77 => '',  
                38 => '',   39 => '',  40 => '',  41 => '',  42 => '',  43 => '', 

                110 => '', 111 => '', 112 => '', 
                113 => '', 114 => '', 115 => '',

                44 => '',   45 => '',  46 => '',  47 => '',  48 => '',  49 => '', 116 => '', 117 => '', 118 => '', 119 => '', 120 => '', 121 => '',
                122 => '', 123 => '', 124 => '', 126 => '', 127 => '', 128 => '', 129 => '', 130 => '', 131 => '', 132 => '', 133 => '', 134 => '',
                135 => '', 125 => '', 136 => '', 137 => '', 138 => '', 139 => '', 140 => '', 141 => '', 142 => '', 143 => '', 144 => '', 145 => '',
                146 => '', 147 => '', 148 => '', 149 => '', 150 => '', 151 => '', 
                152 => '', 153 => '', 154 => '', 155 => '', 156 => '', 157 => '',
                158 => '', 159 => '', 160 => '', 161 => '', 162 => '', 163 => '', 164 => '', 165 => '', 166 => '', 167 => '', 168 => '', 169 => '',
                170 => '', 171 => '', 172 => '', 173 => '', 174 => '', 175 => '', 176 => '', 177 => '', 312 => '',
                178 => '', 179 => '', 282 => '', 180 => '', 181 => ''];
        $this->assertEquals( $expected, $result);        
    }
    public function testWordformsFromMiniTemplateKarProperName() {
        $template = '';
        $lang_id = 4;
        $pos_id = 5;
        $name_num=null;
        $dialect_id='46';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'',  3=>'',  4=>'', 277=>'',  
                     5=>'', 8=>'',  9=>'', 10=>'', 278=>'', 
                     12=>'', 6=>'', 14=>'', 15=>'', 
 
            2=>'', 24=>'', 22=>'', 279=>'', 
            59=>'', 23=>'', 60=>'', 61=>'', 280=>'', 
            62=>'', 64=>'', 65=>'', 66=>'', 281=>''];
        $this->assertEquals( $expected, $result);        
    }

    public function testWordformsFromMiniTemplateKarOloName() {
        $template = '';
        $lang_id = 5;
        $pos_id = 5;
        $name_num=null;
        $dialect_id='44';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [1=>'',  56=>'', 3=>'',  4=>'', 277=>'',  
                     5=>'', 6=>'', 8=>'',  9=>'', 10=>'',  
                     11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 
 
            2=>'', 57=>'', 24=>'', 22=>'', 279=>'', 
            59=>'', 64=>'', 23=>'', 60=>'', 61=>'',  
            25=>'', 62=>'', 63=>'', 65=>'', 66=>'', 281=>''];
        $this->assertEquals( $expected, $result);        
    }
*/   
/* исключения, не работают правила    
    public function testToRightFormSost() {
        $words = ['poĺpäivad' => 'pol’päivad',
            'poĺpeiv΄ad' => 'pol’peiv’ad',
            'ńeĺĺanśpei' => 'nel’l’ans’pei',
            'neĺanśpäi' => 'nel’ans’päi',
            'baŕbmätas' => 'bar’bmätas',
            'täuźigääńe' => 'täuz’igääne',
            't΄äuźigaińe' => 'täuz’igaine',
            't΄äuźigäińe' => 'täuz’igäine',
            't΄äuźigääńe' => 'täuz’igääne',
 
            'soĺhiin' => 'sol’hiin',
            'poŕmi̮i' => 'por’mii',
            
            ];
        $result = [];
        foreach ($words as $word =>$word_exp) {
            $result[] = Grammatic::phoneticsToLemma($word);
        }
        $this->assertEquals(array_values($words), $result);        
    }
*/   
    
}
