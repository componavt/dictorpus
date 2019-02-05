<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic;
use App\Models\Dict\Lemma;
// php artisan make:test Models\Library\GrammaticTest
// ./vendor/bin/phpunit tests/Models/Library/GrammaticTest

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
    
    public function testWordformsByTemplateIncorrectLang() {
        $lang_id = 1;
        $pos_id = 0;
        $dialect_id=47;
        $template = "ativo, ativo, ativo, ativu, ativo, ativo";
        $result = Grammatic::wordformsByTemplate($template, $lang_id, $pos_id, $dialect_id);
        
        $expected = [$template, false, $template, NULL];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformsByTemplateIncorrectPOS() {
        $lang_id = 4;
        $pos_id = 3; // conjunction
        $dialect_id=47;
        $template = "ativo, ativo, ativo, ativu, ativo, ativo";
        $result = Grammatic::wordformsByTemplate($template, $lang_id, $pos_id, $dialect_id);
        
        $expected = [$template, false, $template, NULL];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformsByTemplateWithoutTemplate() {
        $lang_id = 4;
        $pos_id = 5;
        $dialect_id=47;
        $template = "ativo, ativo, ativo, ativu, ativo, ativo";
        $result = Grammatic::wordformsByTemplate($template, $lang_id, $pos_id, $dialect_id);
        
        $expected = [$template, false, $template, NULL];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testWordformsByTemplateIncorrectNumberOfStems() {
        $lang_id = 4;
        $pos_id = 5;
        $dialect_id=47;
        $template = "{ativo, ativo, ativo, ativu, ativo}";
        $result = Grammatic::wordformsByTemplate($template, $lang_id, $pos_id, $dialect_id);
        
        $expected = ['ativo', false, $template, NULL];
        $this->assertEquals( $expected, $result);        
    }
    
    // падает на 91, 103, 151, 
    public function testWordformsByTemplateTulla() {
        $lang_id = 4;
        $pos_id = 11;
        $dialect_id=47;
        $template = "{tulla, tule, tule, tuli, tuli, tul, tulla, tuld}";
        $result = Grammatic::wordformsByTemplate($template, $lang_id, $pos_id, $dialect_id);
//dd($result);        
        $lemma_id = 21337; //tulla 
        $lemma = Lemma::find($lemma_id); 

        $expected = $lemma->getWordformsForTest($dialect_id);
//dd($result);        
        $this->assertEquals( $expected, $result[1]);        
    }
/*    
    public function testWordformsByTemplatePieni() {
        $lang_id = 4;
        $pos_id = 1; //adjective
        $dialect_id=47;
        $template = "{pieni, piene, piene, piendä, pieni, pieni}";
        $result = Grammatic::wordformsByTemplate($template, $lang_id, $pos_id, $dialect_id);
//dd($result);        
        $lemma_id = 21360; //pieni 
        $lemma = Lemma::find($lemma_id); 
        $dialect_id=46;
        $expected = $lemma->getWordformsForTest($dialect_id);
//dd($result);        
        $this->assertEquals( $expected, $result[1]);        
    }
*/    
    public function testNegativeVerbForInd1Sing() {
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
    
    public function testPerfectVerbFormTulla() {
        $lang_id = 4;
        $stem = 'tul';
        $result = Grammatic::perfectForm($stem, $lang_id);
        
        $expected = 'tullun';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testPerfectVerbFormAndua() {
        $lang_id = 4;
        $stem = 'anda';
        $result = Grammatic::perfectForm($stem, $lang_id);
        
        $expected = 'andan';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testAuxFormIndPerf2SingPol() {
        $lang_id = 4;
        $gramset_id = 87; // индикатив, перфект, 2 л., ед. ч., пол
        $dialect_id=47;
        $result = Grammatic::auxForm($gramset_id, $lang_id, $dialect_id);
        
        $expected = 'olet ';
        $this->assertEquals( $expected, $result);        
    }
/*    
    public function testAuxFormIndPerf3PlurNeg() {
        $lang_id = 4;
        $gramset_id = 97; // 36. индикатив, перфект, 3 л., мн. ч., отриц
        $dialect_id=47;
        $result = Grammatic::auxForm($gramset_id, $lang_id, $dialect_id);
        
        $expected = 'ei ole ';
        $this->assertEquals( $expected, $result);        
    }
*/    
    public function testAuxFormIndPlurf3PlurNeg() {
        $lang_id = 4;
        $gramset_id = 103; // 42. индикатив, плюсквамперфект, 3 л., мн. ч., пол.
        $dialect_id=47;
        $result = Grammatic::auxForm($gramset_id, $lang_id, $dialect_id);
        
        $expected = 'oldih ';
        $this->assertEquals( $expected, $result);        
    }
   
    public function testAuxFormCondPlur3SingPol() {
        $lang_id = 4;
        $gramset_id = 136; // 97. кондиционал, плюсквамперфект, 3 л., ед. ч., пол.
        $dialect_id=47;
        $result = Grammatic::auxForm($gramset_id, $lang_id, $dialect_id);
        
        $expected = 'olis’ ';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testimp3SingPolByStem() {
        $stem5 = 'lien';
        $lemma = 'lietä';
        $dialect_id=47;
        $result = Grammatic::imp3SingPolByStem($stem5, $lemma, $dialect_id);
        
        $expected = 'liekkäh';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testimp2PlurPolByStem() {
        $stem5 = 'lien';
        $lemma = 'lietä'; 
        $dialect_id=47;
        $result = Grammatic::imp2PlurPolByStem($stem5, $lemma, $dialect_id);
        
        $expected = 'liekkiä';
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
    
    public function testGarmVowelBack1Let() {
        $stem = 'anda';
        $vowel = 'a';
        $result = Grammatic::garmVowel($stem, $vowel);
        
        $expected = 'a';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testGarmVowelFront2Let() {
        $stem = 'mäne';
        $vowel = 'ou';
        $result = Grammatic::garmVowel($stem, $vowel);
        
        $expected = 'öy';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testProcessForWordformApostroph() {
        $word = "ändäis'";
        $result = Grammatic::processForWordform($word);
        $expected = "ändäis’";
        $this->assertEquals( $expected, $result);        
    }
    
    public function testProcessForWordformWhitespaces() {
        $word = "elgiä  olgua";
        $result = Grammatic::processForWordform($word);
        $expected = "elgiä olgua";
        $this->assertEquals( $expected, $result);        
    }
    
    public function testMaxStem() {
        $stems = ['andua', 'anna', 'anda', 'annoi', 'ando', 'anda', 'anneta', 'annett'];
        $result = Grammatic::maxStem($stems);
        
        $expected = ['an', 'dua'];
        $this->assertEquals( $expected, $result);        
    }
    
}
