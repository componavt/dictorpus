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
    
    public function testMaxStem() {
        $stems = ['andua', 'anna', 'anda', 'annoi', 'ando', 'anda', 'anneta', 'annett'];
        $result = Grammatic::maxStem($stems);
        
        $expected = ['an', 'dua'];
        $this->assertEquals( $expected, $result);        
    }
    
    // stemsFromTemplate()
    public function testStemsFromTemplateIncorrectLang() {
        $lang_id = 3;
        $pos_id = 0;
//        $dialect_id=47;
        $template = "ativo, ativo, ativo, ativu, ativo, ativo";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
        
        $expected = [null, null, $template, null];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateKarelianIncorrectPOS() {
        $lang_id = 4;
        $pos_id = 3; // conjunction
//        $dialect_id=47;
        $template = "ativo, ativo, ativo, ativu, ativo, ativo";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
        
        $expected = [null, null, $template, null];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVepsIncorrectPOS() {
        $lang_id = 1; // veps
        $pos_id = 3; // conjunction
//        $dialect_id=43; // New written Veps
        $template = "{{vep-decl-stems|n=pl|Alama|d|id}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
        
        $expected = [null, null, $template, NULL];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVepsIncorrectTemplate() {
        $lang_id = 1; // veps
        $pos_id = 3; // conjunction
//        $dialect_id=43; // New written Veps
        $template = "{ativo, ativo, ativo, ativu, ativo}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
        
        $expected = [null, null, $template, NULL];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateWithoutBrackets() {
        $lang_id = 4;
        $pos_id = 5;
//        $dialect_id=47;
        $template = "ativo, ativo, ativo, ativu, ativo, ativo";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
        
        $expected = [null, null, $template, NULL];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateIncorrectNumberOfStems() {
        $lang_id = 4;
        $pos_id = 5;
//        $dialect_id=47;
        $template = "{ativo, ativo, ativo, ativu, ativo}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
//dd($result);                
        $expected = [null, null, $template, null];
        $this->assertEquals( $expected, $result);        
    }
    
    // stemsFromTemplate() karelian nominals
    public function testStemsByTemplatePieni() {
        $lang_id = 4;
        $pos_id = 1; //adjective
//        $dialect_id=47;
        $template = "{pieni, piene, piene, piendä, pieni, pieni}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
//dd($result);        
/*        $lemma_id = 21360; //pieni 
        $lemma = Lemma::find($lemma_id); 
        $dialect_id=46;
        $expected = $lemma->getWordformsForTest($dialect_id);*/
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
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
//dd($result);        
/*        $lemma_id = 21337; //tulla 
        $lemma = Lemma::find($lemma_id); 

        $expected = $lemma->getWordformsForTest($dialect_id);*/
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
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
//dd($result);        
/*        $lemma_id = 21324; // abai 
        $lemma = Lemma::find($lemma_id); 

        $expected = $lemma->getWordformsForTest($dialect_id);*/
        $expected = [0=>['abai', 'abajo', 'abajo', 'abajod', 'abajoi', ''],
            1=>null, 2=>'aba', 3=>'i'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsByTemplateVepsKoiv() {
        $lang_id = 1;
        $pos_id = 5; // noun
        $dialect_id=43;
        $template = "{{vep-decl-stems|koiv||un|ud|uid}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
//dd($result);        
/*        $lemma_id = 550; // koiv
        $lemma = Lemma::find($lemma_id); 

        $expected = $lemma->getWordformsForTest($dialect_id);*/
        $expected = [0=>['koiv', 'koivu', 'koivu', 'koivud', 'koivui', ''],
            1=>null, 2=>'koiv', 3=>''];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsByTemplateVepsSg() {
        $lang_id = 1;
        $pos_id = 14; // proper noun
//        $dialect_id=43;
        $template = "{{vep-decl-stems|n=sg|Amerik||an|ad}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
//dd($result);        
/*        $lemma_id = 21531; //Amerik 
        $lemma = Lemma::find($lemma_id); 

        $expected = $lemma->getWordformsForTest($dialect_id);*/
        $expected = [0=>['Amerik', 'Amerika', 'Amerika', 'Amerikad', '', ''],
            1=>'sg', 2=>'Amerik', 3=>''];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsByTemplateVepsPl() {
        $lang_id = 1;
        $pos_id = 14; // proper noun
//        $dialect_id=43;
        $template = "{{vep-decl-stems|n=pl|Alama|d|id}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
//dd($result);        
/*        $lemma_id = 21530; //Alamad 
        $lemma = Lemma::find($lemma_id); 

        $expected = $lemma->getWordformsForTest($dialect_id);*/
        $expected = [0=>['Alamad', '', '', '', 'Alamai', ''],
            1=>'pl', 2=>'Alama', 3=>'d'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsByTemplateVepsNeičukaine() {
        $lang_id = 1;
        $pos_id = 5; // noun
//        $dialect_id=43;
        $template = "neičuka|ine (-ižen, -št, -ižid)";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
//dd($result);        
/*        $lemma_id = 851; // neičukaine 
        $lemma = Lemma::find($lemma_id); 

        $expected = $lemma->getWordformsForTest($dialect_id);*/
        $expected = [0=>['neičukaine', 'neičukaiže', 'neičukaiže', 'neičukašt', 'neičukaiži', ''],
            1=>null, 2=>'neičuka', 3=>'ine'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsByTemplateVepsČoma() {
        $lang_id = 1;
        $pos_id = 1; // noun
//        $dialect_id=43;
        $template = "čom|a (-an, -id)";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
//dd($result);        
/*        $lemma_id = 147; // čoma  
        $lemma = Lemma::find($lemma_id); 

        $expected = $lemma->getWordformsForTest($dialect_id);*/
        $expected = [0=>['čoma', 'čoma', 'čoma', 'čomad', 'čomi', ''],
            1=>null, 2=>'čom', 3=>'a'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsByTemplateVepsSur() {
        $lang_id = 1;
        $pos_id = 1; // noun
//        $dialect_id=43;
        $template = "sur|’ (-en, ’t, -id)";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
//dd($result);        
/*        $lemma_id = 257; // sur’  
        $lemma = Lemma::find($lemma_id); 

        $expected = $lemma->getWordformsForTest($dialect_id);*/
        $expected = [0=>['sur’', 'sure', 'sure', 'sur’t', 'suri', ''],
            1=>null, 2=>'sur', 3=>'’'];
        $this->assertEquals( $expected, $result);        
    }
    
    // stemsFromTemplate() veps verbs
    public function testStemsByTemplateVepsVerbVoikta() {
        $lang_id = 1;
        $pos_id = 11; // verb
//        $dialect_id=43;
        $template = "{{vep-conj-stems|voik|ta|ab|i}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
//dd($result);        
/*        $lemma_id = 82; //voikta
        $lemma = Lemma::find($lemma_id); 

        $expected = $lemma->getWordformsForTest($dialect_id);*/
        $expected = [0=>['voik', 'voika', 'voiki', 'voik', 'voika', 'voik', 't', 'a'],
            1=>null, 2=>'voik', 3=>'ta'];
        $this->assertEquals( $expected, $result);        
    }
   
    public function testStemsByTemplateVepsVerbNullDialect() {
        $lang_id = 1;
        $pos_id = 11; // verb
//        $dialect_id=43;
        $template = "{{vep-conj-stems|töndu|da|b|i}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
//dd($result);        
/*        $lemma_id = 1540; //tönduda
        $lemma = Lemma::find($lemma_id); 

        $expected = $lemma->getWordformsForTest($dialect_id);*/
        $expected = [0=>['töndu', 'töndu', 'töndui', 'töndu', 'töndu', 'töndu', 'd', 'a'],
            1=>null, 2=>'töndu', 3=>'da'];
        $this->assertEquals( $expected, $result);        
    }
    public function testStemsByTemplateVepsVerbAstta() {
        $lang_id = 1;
        $pos_id = 11; // verb
//        $dialect_id=43;
        $template = "{{vep-conj-stems|ast|ta|ub|ui}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
//dd($result);        
/*        $lemma_id = 56; //astta
        $lemma = Lemma::find($lemma_id); 

        $expected = $lemma->getWordformsForTest($dialect_id);*/
        $expected = [0=>['ast', 'astu', 'astui', 'ast', 'astu', 'ast', 't', 'a'],
            1=>null, 2=>'ast', 3=>'ta'];
        $this->assertEquals( $expected, $result);        
    }
       
    public function testStemsByTemplateVepsVerbValita() {
        $lang_id = 1;
        $pos_id = 11; // verb
//        $dialect_id=43;
        $template = "{{vep-conj-stems|vali|ta|čeb|či}}";
        $result = Grammatic::stemsFromTemplate($template, $lang_id, $pos_id/*, $dialect_id*/);
//dd($result);        
/*        $lemma_id = 1126; //valita
        $lemma = Lemma::find($lemma_id); 

        $expected = $lemma->getWordformsForTest($dialect_id);*/
        $expected = [0=>['vali', 'valiče', 'valiči', 'vali', 'valič', 'valiče','t','a'],
            1=>null, 2=>'vali', 3=>'ta'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testParseLemmaField()
    {
        $lemma_field="abei|";
        $data = ['lemma'=>$lemma_field, 'lang_id'=>null, 'pos_id'=>null, 'dialect_id'=>null];
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
}