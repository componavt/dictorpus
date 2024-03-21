<?php

//use Illuminate\Foundation\Testing\WithoutMiddleware;
//use Illuminate\Foundation\Testing\DatabaseMigrations;
//use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Experiments\Ludgen;
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
{
/*    // Ref: 10. potkiekseh
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
            279=>'pezovezinny, pezovezilöinny', 59=>'pezovezikse, pezovezilöikse', 
            64=>'pezovezittäh, pezovezilöittäh', 23=>'pezovezis, pezovezilöis', 
            60=>'pezovezis, pezovezispäi, pezovezilöis, pezovezilöispäi', 61=>'pezovezih, pezovezilöih',  
            25=>'pezovezil, pezovezilöil', 62=>'pezovezil, pezovezilpäi, pezovezilöil, pezovezilöilpäi', 
            63=>'pezovezile, pezovezilöile', 65=>'pezovezienke, pezovezinneh, pezovezilöinke, pezovezilöinneh', 
            66=>'pezoveziči, pezovezilöiči', 281=>'pezovezin, pezovezilöin', 
            18=>'pezoveziellyö, pezovezillyö, pezovezilöillyö', 67=>'pezovezissäh, pezovezilöissäh'];
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
        $expected = [1=>'puoli',  56=>'puoli, puolen', 3=>'puolen',  4=>'puoldu', 
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
        $expected = [1=>'ranta',  56=>'ranta, rannan',  3=>'rannan',  4=>'rantua', 277=>'rantana',  
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
        $expected = [1=>'pelto',  56=>'pelto, pellon',  3=>'pellon',  4=>'peltuo', 277=>'peltona',  
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
        $expected = [1=>'nuori',  56=>'nuori, nuoren',  3=>'nuoren',  4=>'nuorta', 277=>'nuorena',  
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
        $expected = [1=>'nuori',  56=>'nuori, nuoren', 3=>'nuoren',  4=>'nuordu', 277=>'nuorennu',  
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
        $expected = [1=>'vesi', 56=>'vesi, vejen', 3=>'vejen',  4=>'vettä', 277=>'vetenä',  
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
*/        
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
	    66666 => 'uk|ke [o]',
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
	    66666 => [0 => [0 => 'ukke', 1 => 'uko', 2 => 'ukko', 3 => 'ukkod', 4 => 'ukoi', 5 => 'ukkoi', 6 => 'ukko', 10 => true], 1 => null, 2 => 'uk', 3 => 'ke'],
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
	    3540 => [0 => [0 => 'pedäi', 1 => 'pedäjä', 2 => 'pedäjä', 3 => 'pedäjäd', 4 => 'pedäji', 5 => 'pedäji', 6 => 'pedäjä', 10 => false], 1 => null, 2 => 'pedä', 3 => 'i'],
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
	    66666 => 'uk|ke [o]',
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
            66666 => [1=>'ukke', 56=>'ukke, ukon', 3=>'ukon', 4=>'ukkod', 277=>'ukon, ukonnu', 5=>'ukoks, ukokse', 6=>'ukota', 8=>'ukos', 9=>'ukos, ukospiäi', 10=>'ukkoh', 11=>'ukol', 12=>'ukol, ukolpiäi', 13=>'ukol, ukole', 14=>'ukonke', 15=>'ukoči', 17=>'ukolloh, ukolloo, ukolluo', 16=>'ukkossuai', 19=>'ukkohpiäi', 2=>'ukod, ukot', 57=>'ukod, ukot', 24=>'ukoiden', 22=>'ukkoid', 279=>'ukoin, ukoinnu', 59=>'ukoiks, ukoikse', 64=>'ukoita', 23=>'ukois', 60=>'ukois, ukoispiäi', 61=>'ukkoih', 25=>'ukoil', 62=>'ukoil, ukoilpiäi', 63=>'ukoil, ukoile', 65=>'ukoineh', 66=>'ukoiči', 281=>'ukoin', 18=>'ukoilloh, ukoilloo, ukoilluo', 67=>'ukkoissuai', 68=>'ukkoihpiäi', ],
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
