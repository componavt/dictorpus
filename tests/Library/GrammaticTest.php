<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic;
use App\Models\Dict\Lemma;
// php artisan make:test Library\GrammaticTest
// ./vendor/bin/phpunit tests/Library/GrammaticTest

class GrammaticTest extends TestCase
{
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
        
        $expected = 'en ';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testNegativeFormInd2Sing() {
        $lang_id = 4;
        $gramset_id = 81; // индикатив, имперфект, 2 л., ед. ч., отриц
        $result = Grammatic::negativeForm($gramset_id, $lang_id);
        
        $expected = 'et ';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testNegativeVepsVerbForInd1Sing() {
        $lang_id = 1;
        $gramset_id = 70; // индикатив, презенс, 1 л., ед. ч., отриц
        $result = Grammatic::negativeForm($gramset_id, $lang_id);
        
        $expected = 'en ';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testverbWordformByStemsAndazin() {
        $lang_id = 4;
        $stems = ['andua', 'anna', 'anda', 'annoi', 'ando', 'anda', 'anneta', 'annett'];
        $gramset_id = 44; //71. кондиционал, имперфект, 1 л., ед. ч., пол.
        $dialect_id=47;
        $result = Grammatic::verbWordformByStems($stems, $gramset_id, $lang_id, $dialect_id);
        
        $expected = 'andazin';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testVerbWordformByStems282Andua() {
        $lang_id = 4;
        $stems = ['andua', 'anna', 'anda', 'annoi', 'ando', 'anda', 'anneta', 'annett'];
        $gramset_id = 282; //141. актив, 2-е причастие  (сокращенная форма); перфект (форма основного глагола)
        $dialect_id=47;
        $result = Grammatic::verbWordformByStems($stems, $gramset_id, $lang_id, $dialect_id);
        
        $expected = 'andan';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testVerbWordformByStemsActive1Partic() {
        $lang_id = 4;
        $stems = ['tulla', 'tule', 'tule', 'tuli', 'tuli', 'tul', 'tulla', 'tuld'];
        $gramset_id = 178; //139. актив, 1-е причастие 
        $dialect_id=47;
        $result = Grammatic::verbWordformByStems($stems, $gramset_id, $lang_id, $dialect_id);
        
        $expected = 'tulija';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testCondImp3SingPolByStemAndazin() {
        $lang_id = 4;
        $stems = ['andua', 'anna', 'anda', 'annoi', 'ando', 'anda', 'anneta', 'annett'];
        $gramset_id = 46; //73. кондиционал, имперфект, 3 л., ед. ч., пол.
        $dialect_id=47;
        $result = Grammatic::verbWordformByStems($stems, $gramset_id, $lang_id, $dialect_id);
        
        $expected = 'andais’';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testPotPres3PlurPolByStemAnnettanneh() {
        $lang_id = 4;
        $stems = ['andua', 'anna', 'anda', 'annoi', 'ando', 'anda', 'anneta', 'annett'];
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
/*    
    public function testToRightFormSost() {
        $words = ['poĺpäivad' => 'pol’päivad',
            'poĺpeiv΄ad' => 'pol’peiv’ad',
            'ńeĺĺanśpei' => 'nel’l’ans’pei',
            'neĺanśpäi' => 'nel’ans’päi'
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
        $expected = [0=>['pieni', 'piene', 'piene', 'piendä', 'pieni', 'pieni'],
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
        $expected = [0=>['tulla', 'tule', 'tule', 'tuli', 'tuli', 'tul', 'tulla', 'tuld'],
            1=>null, 2=>'tul', 3=>'la'];
//dd($result);        
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
                      5=>''], $num, 'abuozuteseli', 'ne'];
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
                      5=>''], $num, 'Kariel', 'a'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVepsWrongPartitiv() {
        $lang_id = 1;
        $pos_id = 5; // noun
//        $dialect_id=43;
        $template = "neičuka|ine (-ižed, -št, -ižid)";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
//dd($result);        
        $expected = [0=>['neičuka|ine (-ižed, -št, -ižid)'],
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
        $expected = [[0=>'abuozuteseline'], $num, 'abuozuteseli', 'ne'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateCompoundVepsNounWrongTemplate() {
        $template = "abu||ozuteseli|ne (";
        $lang_id = 1;
        $pos_id = 1; // noun
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [NULL, $num, 'abuozuteseli|ne (', NULL];
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
        $expected = [[0=>'abudengu'], $num, 'abudeng', 'u'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateOloNounCompound() {
        $template = "abu||deng|u";
        $lang_id = 5;
        $pos_id = 1; // noun
        $num = null;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [[0=>'abudengu'], $num, 'abudeng', 'u'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateKarNounCompoundWrongTemplate() {
        $template = "abu||deng|u (-an,";
        $lang_id = 5;
        $pos_id = 1; // noun
        $num = null;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [NULL, $num, 'abudeng|u (-an,', NULL];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateCompoundVepsVerb() {
        $template = "alle||kirjut|ada";
        $lang_id = 1;
        $pos_id = 11; 
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [[0=>'allekirjutada'], $num, 'allekirjut', 'ada'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateCompoundVepsVerbWrongTemplate() {
        $template = "alle||kirjut|ada (";
        $lang_id = 1;
        $pos_id = 11; 
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [NULL, $num, 'allekirjut|ada (', NULL];
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
        $expected = [[0=>'allekirjuttua'], $num, 'allekirjut', 'tua'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateOloVerbCompound() {
        $template = "alle||kirjut|tua";
        $lang_id = 5;
        $pos_id = 11; 
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [[0=>'allekirjuttua'], $num, 'allekirjut', 'tua'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateKarVerbCompoundWrongTemplate() {
        $template = "alle||kirjut|tua (";
        $lang_id = 5;
        $pos_id = 11; 
        $num = NULL;
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
//dd($result);        
        $expected = [NULL, $num, 'allekirjut|tua (', NULL];
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
    {/*
        $id=3539;
        $lemma = Lemma::findOrFail($id);        
        $base_n = 4;
        $dialect_id=null;
        $result = Grammatic::getStemFromWordform($lemma, $base_n, $lemma->lang_id,  $lemma->pos_id, $dialect_id, $is_reflexive);
        
        $expected = 'tulou';
        $this->assertEquals( $expected, $result);        */
    }
    
    public function testWordformsByStemsKarVerbOlo() {
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
                86 => 'olen puhkennuh',   87 => 'olet puhkennuh',  88 => 'on puhkennuh',  89 => 'olemmo puhkennuh',  90 => 'oletto puhkennuh',  91 => 'on puhkettu',  
                92 => 'en ole puhkennuh',  93 => 'et ole puhkennuh',  94 => 'ei ole puhkennuh',  95 => 'emmo ole puhkennuh',  96 => 'etto ole puhkennuh',  97 => 'ei olla puhkettu',
                98 => 'olin puhkennuh',   99 => 'olit puhkennuh', 100 => 'oli puhkennuh', 101 => 'olimmo puhkennuh', 102 => 'olitto puhkennuh', 103 => 'oli puhkettu', 
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
        $this->assertEquals( $expected, $result);        
    }
/*    
    public function testWordformsByStemsKarVerbOlo() {
        $template = '';
        $lang_id = 5;
        $pos_id = 11;
        $name_num=null;
        $dialect_id='';
        list($stems, $name_num, $max_stem, $affix) = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = Grammatic::wordformsByStems($lang_id, $pos_id, $dialect_id, $name_num, $stems);
        $expected = [26 => '',   27 => '',  28 => '',  29 => '',  30 => '',  31 => '', 295 => '', 296 => '', 
                70 => '',   71 => '',  72 => '',  73 => '',  78 => '',  79 => '', 
                32 => '',   33 => '',  34 => '',  35 => '',  36 => '',  37 => '', 
                80 => '',   81 => '',  82 => '',  83 => '',  84 => '',  85 => '', 
                86 => '',   87 => '',  88 => '',  89 => '',  90 => '',  91 => '',  92 => '',  93 => '',  94 => '',  95 => '',  96 => '',  97 => '',
                98 => '',   99 => '', 100 => '', 101 => '', 102 => '', 103 => '', 104 => '', 105 => '', 107 => '', 108 => '', 106 => '', 109 => '',
                      51 => '',  52 => '',  53 => '',  54 => '',  55 => '',       50 => '',  74 => '',  75 => '',  76 => '',  77 => '',  
                38 => '',   39 => '',  40 => '',  41 => '',  42 => '',  43 => '', 
                70 => '',   71 => '',  72 => '',  73 => '',  78 => '',  79 => '',
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
*/    
}
