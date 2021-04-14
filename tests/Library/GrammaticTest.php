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
{
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
            15=>'pezovieči', 17=>'pezovielluo', 16=>'pezoviessäh',
            
            2=>'pezoviet', 57=>'pezoviet', 24=>'pezovezien, pezovezilöin', 22=>'pezovezii, pezovezilöi', 
            279=>'pezovezinny, pezovezilöinny', 59=>'pezovezikse, pezovezilöikse', 
            64=>'pezovezittäh, pezovezilöittäh', 23=>'pezovezis, pezovezilöis', 
            60=>'pezovezis, pezovezispäi, pezovezilöis, pezovezilöispäi', 61=>'pezovezih, pezovezilöih',  
            25=>'pezovezil, pezovezilöil', 62=>'pezovezil, pezovezilpäi, pezovezilöil, pezovezilöilpäi', 
            63=>'pezovezile, pezovezilöile', 65=>'pezovezienke, pezovezinneh, pezovezilöinke, pezovezilöinneh', 
            66=>'pezoveziči, pezovezilöiči', 281=>'pezovezin, pezovezilöin', 
            18=>'pezovezilluo, pezovezilöilluo', 67=>'pezovezissäh, pezovezilöissäh'];
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
            65=>'puolienke, puolinneh', 66=>'puoliči', 281=>'puolin', 18=>'puolilluo', 67=>'puolissah'];
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
            62=>'rannoilta', 64=>'rannoitta', 65=>'rantoineh', 66=>'', 281=>'rannoin'];
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
                     11=>'rannal', 12=>'rannal, rannalpäi', 13=>'rannale', 14=>'rannanke', 15=>'rannači', 17=>'rannalluo', 16=>'rannassah',
 
            2=>'rannat', 57=>'rannat', 24=>'rannoin', 22=>'randoi', 279=>'rannoinnu', 
            59=>'rannoikse', 64=>'rannoittah', 23=>'rannois', 60=>'rannois, rannoispäi', 61=>'randoih',  
            25=>'rannoil', 62=>'rannoil, rannoilpäi', 63=>'rannoile', 65=>'rannoinke, rannoinneh', 
            66=>'rannoiči', 281=>'rannoin', 18=>'rannoilluo', 67=>'rannoissah'];
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
            62=>'peltoloilta', 64=>'peltoloitta', 65=>'peltoloineh', 66=>'', 281=>'peltoloin'];
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
                     11=>'pellol', 12=>'pellol, pellolpäi', 13=>'pellole', 14=>'pellonke', 15=>'pelloči', 17=>'pellolluo', 16=>'pellossah',
 
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
            62=>'mailta', 64=>'maitta', 65=>'maineh', 66=>'', 281=>'main'];
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
            62=>'nuorilta', 64=>'nuoritta', 65=>'nuorineh', 66=>'', 281=>'nuorin'];
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
            25=>'nuoril', 62=>'nuoril, nuorilpäi', 63=>'nuorile', 65=>'nuorienke, nuorinneh', 66=>'nuoriči', 281=>'nuorin', 18=>'nuorilluo', 67=>'nuorissah'];
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
            62=>'lyhyiltä', 64=>'lyhyittä', 65=>'lyhyineh', 66=>'', 281=>'lyhyin'];
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
                     11=>'lyhyöl', 12=>'lyhyöl, lyhyölpäi', 13=>'lyhyöle', 14=>'lyhyönke', 15=>'lyhyöči', 17=>'lyhyölluo', 16=>'lyhyössäh',
 
            2=>'lyhyöt', 57=>'lyhyöt', 24=>'lyhyzien', 22=>'lyhyzii', 279=>'lyhyzinny', 
            59=>'lyhyzikse', 64=>'lyhyzittäh', 23=>'lyhyzis', 60=>'lyhyzis, lyhyzispäi', 61=>'lyhyzih',  
            25=>'lyhyzil', 62=>'lyhyzil, lyhyzilpäi', 63=>'lyhyzile', 
            65=>'lyhyzienke, lyhyzinneh', 66=>'lyhyziči', 281=>'lyhyzin', 18=>'lyhyzilluo', 67=>'lyhyzissäh'];
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
            62=>'vesiltä', 64=>'vesittä', 65=>'vesineh', 66=>'', 281=>'vesin'];
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
                     11=>'viel', 12=>'viel, vielpäi', 13=>'viele', 14=>'vienke', 15=>'vieči', 17=>'vielluo', 16=>'viessäh',
 
            2=>'viet', 57=>'viet', 24=>'vezien', 22=>'vezii', 279=>'vezinny', 
            59=>'vezikse', 64=>'vezittäh', 23=>'vezis', 60=>'vezis, vezispäi', 61=>'vezih',  
            25=>'vezil', 62=>'vezil, vezilpäi', 63=>'vezile', 
            65=>'vezienke, vezinneh', 66=>'veziči', 281=>'vezin', 18=>'vezilluo', 67=>'vezissäh'];
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
