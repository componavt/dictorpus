<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic;
use App\Models\Dict\Lemma;
// php artisan make:test Library\GrammaticTest
// ./vendor/bin/phpunit tests/Library/GrammaticTest

class GrammaticTest extends TestCase
{/*
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
        $result = Grammatic::toRightForm($word);
        $expected = "päivinka";
        $this->assertEquals( $expected, $result);        
    }
    
    public function testToRightFormNFalse() {
        $word = "päivińka";
        $result = Grammatic::toRightForm($word,false);
        $expected = "päivińka";
        $this->assertEquals( $expected, $result);        
    }
    
    public function testToRightFormPaivuFalse() {
        $word = "päivü";
        $result = Grammatic::toRightForm($word,false);
        $expected = "päivü";
        $this->assertEquals( $expected, $result);        
    }
    */
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
            't΄üuniśt΄üö' => 'tüunist’üö',
            'd΄ärvenseĺgä'=>'därvenselgä',
            ];
        $result = [];
        foreach ($words as $word =>$word_exp) {
            $result[] = Grammatic::toRightForm($word,true);
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
            'varź' => 'varz’'];
        $result = [];
        foreach ($words as $word =>$word_exp) {
            $result[] = Grammatic::toRightForm($word,true);
        }
        $this->assertEquals(array_values($words), $result);        
    }
    
    public function testToRightFormBeforeConsonant() {
        $words = [
            'gaĺbuu'=>'gal’buu', 
            'saĺm' => 'sal’m', 
            'lińdžoi' => 'lin’džoi',
            'luńd΄žuo' => 'lun’d’žuo',
            'mańdžikka' => 'man’džikka',
            'mańdžoi' => 'man’džoi',
            'mańd΄žuo' => 'man’d’žuo',
            'mańd΄žuoi' => 'man’d’žuoi',
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
            ];
        $result = [];
        foreach ($words as $word =>$word_exp) {
            $result[] = Grammatic::toRightForm($word,true);
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
            'śeŕanka' => 'ser’anka'];
        $result = [];
        foreach ($words as $word =>$word_exp) {
            $result[] = Grammatic::toRightForm($word,true);
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
            'čeŕohm' => 'čer’ohm'];
        $result = [];
        foreach ($words as $word =>$word_exp) {
            $result[] = Grammatic::toRightForm($word,true);
        }
        $this->assertEquals(array_values($words), $result);        
    }
    
    public function testToRightFormDiacretBeforeU() {
        $words = ['šĺuakott' => 'šl’uakott',
            'šĺuboi' => 'šl’uboi',
            'šĺubuoi' => 'šl’ubuoi',
            'd΄uońuo' => 'd’uon’uo',
            'ńuaglahut' => 'n’uaglahut',
            'muuŕuo' => 'muur’uo'];
        $result = [];
        foreach ($words as $word =>$word_exp) {
            $result[] = Grammatic::toRightForm($word,true);
        }
        $this->assertEquals(array_values($words), $result);        
    }
    
    public function testToRightFormDiacretOther() {
        $words = [ 
            'giĺiηgeińe'=>'gilingeine',
            'hiĺĺeta' => 'hilleta',
            'hiĺĺetä' => 'hilletä',
//            'järvenšeĺgä' => 'järvenšelgä',
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
            'ĺiäd΄žö' => 'liäd’žö',
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
//            'seĺged' => 'selged',
//            'seĺgie' => 'selgie',
//            'seĺgiä' => 'selgiä',
//            'seĺgä' => 'selgä',
            'seĺitra' => 'selitra',
            'seĺvä' => 'selvä',
            'sĺäc' => 'släc',
            'sĺäč' => 'släč',
            'tuĺjaańe' => 'tuljaane',
            'tuĺĺi' => 'tulli',
            'tuĺĺii' => 'tullii',
            'tuuĺe' => 'tuule',
//            'tuuĺhagar' => 'tuulhagar',
            'tuuĺi' => 'tuuli',
            'zaĺiv' => 'zaliv',
//            'šeĺged' => 'šelged',
//            'šeĺgä' => 'šelgä',
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
//            'päivińka' => 'päivinka',
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
            $result[] = Grammatic::toRightForm($word,true);
        }
        $this->assertEquals(array_values($words), $result);        
    }
/*    
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
//        $dialect_id=47;
        $template = "{pieni, piene, piene, piendä, pieni, pieni}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
//dd($result);        
        $expected = [0=>['pieni', 'piene', 'piene', 'piendä', 'pieni', 'pieni'],
            1=>null, 2=>'pien', 3=>'i'];
        $this->assertEquals( $expected, $result);        
    }
    
    // stemsFromTemplate() karelian verbs
    public function testStemsFromTemplateTulla() {
        $lang_id = 4;
        $pos_id = 11;
//        $dialect_id=47;
        $template = "{tulla, tule, tule, tuli, tuli, tul, tulla, tuld}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id);
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
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id, $num);
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
        $word = Grammatic::toRightForm($word);
        $result = Grammatic::toSearchForm($word);
        $expected = "tüunistüö";
        $this->assertEquals( $expected, $result);        
    }*/
}
