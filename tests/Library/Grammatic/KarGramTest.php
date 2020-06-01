<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\KarGram;

// php artisan make:test Library\Grammatic\KarGramTest
// ./vendor/bin/phpunit tests/Library/Grammatic/KarGramTest

class KarGramTest extends TestCase
{
    public function testGarmVowelBack1Let() {
        $stem = 'anda';
        $vowel = 'a';
        $result = KarGram::garmVowel($stem, $vowel);
        
        $expected = 'a';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testGarmVowelFront2Let() {
        $stem = 'mäne';
        $vowel = 'ou';
        $result = KarGram::garmVowel(KarGram::isBackVowels($stem), $vowel);
        
        $expected = 'öy';
        $this->assertEquals( $expected, $result);        
    }
    
     public function testToRightTemplate_3bases()
    {
        $template = "ič|e {-čie, -čiedä, -čei}";
        $num = null;
        $pos_id = 5; // noun
        $result = KarGram::toRightTemplate($template, $num, $pos_id);
       
        $expected = "{iče, iččie, iččie, iččiedä, iččei, iččei}";
        $this->assertEquals($expected, $result);        
    }
           
     public function testToRightTemplateSg()
    {
        $template = "Kariel|a {-a, -ua}";
        $num = 'sg';
        $pos_id = 5; // noun
        $result = KarGram::toRightTemplate($template, $num, $pos_id);
       
        $expected = "{Kariela, Kariela, Kariela, Karielua, , }";
        $this->assertEquals($expected, $result);        
    }
              
    public function testStemsFromFullList()
    {
        $template = "{Kariela, Kariela, Kariela, Karielua, , }";
        $lang_id = 4;
        $pos_id = 5; // noun
        $result = KarGram::stemsFromFullList($template);
       
        $expected = [0=>'Kariela', 
                     1=>'Kariela', 
                     2=>'Kariela', 
                     3=>'Karielua', 
                     4=>'', 
                     5=>''];
        $this->assertEquals($expected, $result);        
    }
    
    public function testStemsFromTemplateNameSg() {
        $template = "Kariel|a {-a, -ua}";
        $pos_id = 5; // noun
        $num = 'sg';
        $dialect_id = 47;
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'Kariela', 
                      1=>'Kariela', 
                      2=>'Kariela', 
                      3=>'Karielua', 
                      4=>'', 
                      5=>''], $num, 'Kariel', 'a'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloPeittyo() {
        $template = "peit|työ (-yn, -tyy; -ytäh; -yi, -yttih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'peittyö',            
                      1=>'peity', 
                      2=>'peittyy', 
                      3=>'peitty', 
                      4=>'peityi', 
                      5=>'peityi', 
                      6=>'peitytä', 
                      7=>'peitytt', 
                      8=>'peitty'], $num, 'peit', 'työ'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloPenguo() {
        $template = "peng|uo (-on, -ou; -otah; -oi, -ottih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'penguo',            
                      1=>'pengo', 
                      2=>'pengou', 
                      3=>'pengo', 
                      4=>'pengoi', 
                      5=>'pengoi', 
                      6=>'pengota', 
                      7=>'pengott', 
                      8=>'pengo'], $num, 'peng', 'uo'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloPidia() {
        $template = "pi|diä (-en, -däy; -etäh; -di, -ettih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'pidiä',            
                      1=>'pie', 
                      2=>'pidäy', 
                      3=>'pidä', 
                      4=>'pidi', 
                      5=>'piji', 
                      6=>'pietä', 
                      7=>'piett', 
                      8=>'pidä'], $num, 'pi', 'diä'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloPolgie() {
        $template = "pol|gie (-len, -gou; -gietah; -gi, -giettih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'polgie',            
                      1=>'polle', 
                      2=>'polgou', 
                      3=>'polge', 
                      4=>'polgi', 
                      5=>'polli', 
                      6=>'polgieta', 
                      7=>'polgiett', 
                      8=>'polge'], $num, 'pol', 'gie'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloEccie() {
        $template = "eč|čie (-in, -čiy; -itäh; -či, -ittih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'eččie',            
                      1=>'eči', 
                      2=>'eččiy', 
                      3=>'ečči', 
                      4=>'ečči', 
                      5=>'eči', 
                      6=>'ečitä', 
                      7=>'ečitt', 
                      8=>'ečči'], $num, 'eč', 'čie'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloPabaittua() {
        $template = "pabait|tua (-an, -tau; -etah; -ti, -ettih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'pabaittua',            
                      1=>'pabaita', 
                      2=>'pabaittau', 
                      3=>'pabaitta', 
                      4=>'pabaitti', 
                      5=>'pabaiti', 
                      6=>'pabaiteta', 
                      7=>'pabaitett', 
                      8=>'pabaitta'], $num, 'pabait', 'tua'];
        $this->assertEquals( $expected, $result);        
    }
  
    public function testStemsFromTemplateVerbOloPalua() {
        $template = "pal|ua (-an, -au; -etah; -oi, -ettih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'palua',            
                      1=>'pala', 
                      2=>'palau', 
                      3=>'pala', 
                      4=>'paloi', 
                      5=>'paloi', 
                      6=>'paleta', 
                      7=>'palett', 
                      8=>'pala'], $num, 'pal', 'ua'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateVerbOloPuija() {
        $template = "pui|ja (-n, -bi; -jah; pui, -dih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'puija',            
                      1=>'pui', 
                      2=>'puibi', 
                      3=>'pui', 
                      4=>'pui', 
                      5=>'pui', 
                      6=>'puija', 
                      7=>'puid', 
                      8=>'pui'], $num, 'pui', 'ja'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateVerbOloJuvva() {
        $template = "j|uvva (-uon, -uou; -uvvah; -oi, -uodih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'juvva',            
                      1=>'juo', 
                      2=>'juou', 
                      3=>'juo', 
                      4=>'joi', 
                      5=>'joi', 
                      6=>'juvva', 
                      7=>'juod', 
                      8=>'juo'], $num, 'j', 'uvva'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateVerbOloSuaha() {
        $template = "s|uaha (-uan, -uau; -uahah/-uajah; -ai, -uadih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'suaha',            
                      1=>'sua', 
                      2=>'suau', 
                      3=>'sua', 
                      4=>'sai', 
                      5=>'sai', 
                      6=>'suaha/suaja', 
                      7=>'suad', 
                      8=>'sua'], $num, 's', 'uaha'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloPaganoija() {
        $template = "paganoi|ja (-čen, -ččou; -jah; -čči, -ttih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'paganoija',            
                      1=>'paganoiče', 
                      2=>'paganoiččou', 
                      3=>'paganoičče', 
                      4=>'paganoičči', 
                      5=>'paganoiči', 
                      6=>'paganoija', 
                      7=>'paganoitt', 
                      8=>'paganoi'], $num, 'paganoi', 'ja'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloPainella() {
        $template = "painel|la (-en, -ou; -lah; -i, -tih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'painella',            
                      1=>'painele', 
                      2=>'painelou', 
                      3=>'painele', 
                      4=>'paineli', 
                      5=>'paineli', 
                      6=>'painella', 
                      7=>'painelt', 
                      8=>'painel'], $num, 'painel', 'la'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateVerbOloPanna() {
        $template = "pan|na (-en, -ou; -nah; -i, -dih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'panna',            
                      1=>'pane', 
                      2=>'panou', 
                      3=>'pane', 
                      4=>'pani', 
                      5=>'pani', 
                      6=>'panna', 
                      7=>'pand', 
                      8=>'pan'], $num, 'pan', 'na'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloPierta() {
        $template = "pier|tä (-en, -öy; -täh; -i, -tih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'piertä',            
                      1=>'piere', 
                      2=>'pieröy', 
                      3=>'piere', 
                      4=>'pieri', 
                      5=>'pieri', 
                      6=>'piertä', 
                      7=>'piert', 
                      8=>'pier'], $num, 'pier', 'tä'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateVerbOloPesta() {
        $template = "pe|stä (-zen, -zöy; -stäh; -zi, -stih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'pestä',            
                      1=>'peze', 
                      2=>'pezöy', 
                      3=>'peze', 
                      4=>'pezi', 
                      5=>'pezi', 
                      6=>'pestä', 
                      7=>'pest', 
                      8=>'pes'], $num, 'pe', 'stä'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloPaikata() {
        $template = "paik|ata (-kuan, -kuau; -atah; -kai, -attih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'paikata',            
                      1=>'paikkua', 
                      2=>'paikkuau', 
                      3=>'paikkua', 
                      4=>'paikkai', 
                      5=>'paikkai', 
                      6=>'paikata', 
                      7=>'paikatt', 
                      8=>'paikat'], $num, 'paik', 'ata'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloPuhketa() {
        $template = "puhk|eta (-ien/-enen, -ieu/-enou; -etah; -ei/-eni, -ettih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'puhketa',            
                      1=>'puhkie/puhkene', 
                      2=>'puhkieu/puhkenou', 
                      3=>'puhkie/puhkene', 
                      4=>'puhkei/puhkeni', 
                      5=>'puhkei/puhkeni', 
                      6=>'puhketa', 
                      7=>'puhkett', 
                      8=>'puhket'], $num, 'puhk', 'eta'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateVerbOloPaheta() {
        $template = "pahe|ta (-nen, -nou; -tah; -ni, -ttih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'paheta',            
                      1=>'pahene', 
                      2=>'pahenou', 
                      3=>'pahene', 
                      4=>'paheni', 
                      5=>'paheni', 
                      6=>'paheta', 
                      7=>'pahett', 
                      8=>'pahet'], $num, 'pahe', 'ta'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateVerbOloPakita() {
        $template = "paki|ta (-čen, -ččou; -tah; -čči, -ttih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'pakita',            
                      1=>'pakiče', 
                      2=>'pakiččou', 
                      3=>'pakičče', 
                      4=>'pakičči', 
                      5=>'pakiči', 
                      6=>'pakita', 
                      7=>'pakitt', 
                      8=>'pakit'], $num, 'paki', 'ta'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateVerbOloVedia() {
        $template = "vediä (vien, vedäy; vietäh; vedi, viettih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'vediä',            
                      1=>'vie', 
                      2=>'vedäy', 
                      3=>'vedä', 
                      4=>'vedi', 
                      5=>'veji', 
                      6=>'vietä', 
                      7=>'viett', 
                      8=>'vedä'], $num, 'vediä', ''];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloKuadua() {
        $template = "kua|dua (-n, -dau; -tah; -doi, -ttih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'kuadua',            
                      1=>'kua', 
                      2=>'kuadau', 
                      3=>'kuada', 
                      4=>'kuadoi', 
                      5=>'kavoi', 
                      6=>'kuata', 
                      7=>'kuatt', 
                      8=>'kuada'], $num, 'kua', 'dua'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateVerbOloJagua() {
        $template = "jagua (juan, jagau; juatah; jagoi, juattih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'jagua',            
                      1=>'jua', 
                      2=>'jagau', 
                      3=>'jaga', 
                      4=>'jagoi', 
                      5=>'javoi', 
                      6=>'juata', 
                      7=>'juatt', 
                      8=>'jaga'], $num, 'jagua', ''];
        $this->assertEquals( $expected, $result);        
    }    

    public function testStemsFromTemplateVerbOloJiaha() {
        $template = "jiä|hä (-n, -y; -häh/-jäh; jäi, -dih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'jiähä',            
                      1=>'jiä', 
                      2=>'jiäy', 
                      3=>'jiä', 
                      4=>'jäi', 
                      5=>'jäi', 
                      6=>'jiähä/jiäjä', 
                      7=>'jiäd', 
                      8=>'jiä'], $num, 'jiä', 'hä'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloJiaja() {
        $template = "jiä|jä (-n, -y; -häh/-jäh; jäi, -dih)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id);
//dd($result);        
        $expected = [[0=>'jiäjä',            
                      1=>'jiä', 
                      2=>'jiäy', 
                      3=>'jiä', 
                      4=>'jäi', 
                      5=>'jäi', 
                      6=>'jiähä/jiäjä', 
                      7=>'jiäd', 
                      8=>'jiä'], $num, 'jiä', 'jä'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloRefPackahtellakseh() {
        $template = "pačkahtel|lakseh (-emmos, -eh/-ehes; -lаhes; -ih/-ihes, -tihes)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $is_reflexive = true;
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id, $is_reflexive);
//dd($result);        
        $expected = [[0=>'pačkahtellakseh',            
                      1=>'pačkahtele', 
                      2=>'pačkahteleh/pačkahtelehes', 
                      3=>'pačkahtele', 
                      4=>'pačkahtelih/pačkahtelihes', 
                      5=>'pačkahteli', 
                      6=>'pačkahtellаhes', 
                      7=>'pačkahtelt', 
                      8=>'pačkahtel'], $num, 'pačkahtel', 'lakseh'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloRefPackahtuakseh() {
        $template = "pačkaht|uakseh (-ammos, -ah/-ahes; -etahes; -ih/-ihes, -ettihes)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $is_reflexive = true;
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id, $is_reflexive);
//dd($result);        
        $expected = [[0=>'pačkahtuakseh',            
                      1=>'pačkahta', 
                      2=>'pačkahtah/pačkahtahes', 
                      3=>'pačkahta', 
                      4=>'pačkahtih/pačkahtihes', 
                      5=>'pačkahti', 
                      6=>'pačkahtetahes', 
                      7=>'pačkahtett', 
                      8=>'pačkahta'], $num, 'pačkaht', 'uakseh'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloRefPahoitellakseh() {
        $template = "pahoit|ellakseh (-telemmos, -teleh/-telehes; -ellahes; -telih/-telihes, -eltihes)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $is_reflexive = true;
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id, $is_reflexive);
//dd($result);        
        $expected = [[0=>'pahoitellakseh',            
                      1=>'pahoittele', 
                      2=>'pahoitteleh/pahoittelehes', 
                      3=>'pahoittele', 
                      4=>'pahoittelih/pahoittelihes', 
                      5=>'pahoitteli', 
                      6=>'pahoitellahes', 
                      7=>'pahoitelt', 
                      8=>'pahoitel'], $num, 'pahoit', 'ellakseh'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloRefPakastuakseh() {
        $template = "pakast|uakseh (-ah/-ahes; -ih/-ihes)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $is_reflexive = true;
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id, $is_reflexive);
//dd($result);        
        $expected = [[0=>'pakastuakseh',            
                      1=>'pakasta', 
                      2=>'pakastah/pakastahes', 
                      3=>'pakasta', 
                      4=>'pakastih/pakastihes', 
                      5=>'pakasti', 
                      6=>'', 
                      7=>'', 
                      8=>'pakasta'], 'def', 'pakast', 'uakseh'];
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplateVerbOloRefPakitakseh() {
        $template = "paki|takseh (-čemmos, -ččeh/-ččehes; -tahes; -ččih/-ččihes, -ttihes)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $is_reflexive = true;
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id, $is_reflexive);
//dd($result);        
        $expected = [[0=>'pakitakseh',            
                      1=>'pakiče', 
                      2=>'pakiččeh/pakiččehes', 
                      3=>'pakičče', 
                      4=>'pakiččih/pakiččihes', 
                      5=>'pakiči', 
                      6=>'pakitahes', 
                      7=>'pakitt', 
                      8=>'pakit'], $num, 'paki', 'takseh'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateVerbOloRefPalkatakseh() {
        $template = "palk|atakseh (-uammos, -uahes; -atahes; -aihes, -attihes)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $is_reflexive = true;
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id, $is_reflexive);
//dd($result);        
        $expected = [[0=>'palkatakseh',            
                      1=>'palkua', 
                      2=>'palkuahes', 
                      3=>'palkua', 
                      4=>'palkaihes', 
                      5=>'palkai', 
//                      5=>'palkat', 
                      6=>'palkatahes', 
                      7=>'palkatt', 
                      8=>'palkat'], $num, 'palk', 'atakseh'];
        $this->assertEquals( $expected, $result);        
    }
  
    public function testStemsFromTemplateVerbOloRefPerguakseh() {
        $template = "per|guakseh (-rammos, -gahes; -retähes; -gihes, -rettihes)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $is_reflexive = true;
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id, $is_reflexive);
//dd($result);        
        $expected = [[0=>'perguakseh',            
                      1=>'perra', 
                      2=>'pergahes', 
                      3=>'perga', 
                      4=>'pergihes', 
                      5=>'perri', 
                      6=>'perretähes', 
                      7=>'perrett', 
                      8=>'perga'], $num, 'per', 'guakseh'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateVerbOloRefPestakseh() {
        $template = "pe|stäkseh (-zemmös, -zeh/-zehes; -stähes; -zih/-zihes, -stihes)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $is_reflexive = true;
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id, $is_reflexive);
//dd($result);        
        $expected = [[0=>'pestäkseh',            
                      1=>'peze', 
                      2=>'pezeh/pezehes', 
                      3=>'peze', 
                      4=>'pezih/pezihes', 
                      5=>'pezi', 
                      6=>'pestähes', 
                      7=>'pest', 
                      8=>'pes'], $num, 'pe', 'stäkseh'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateVerbOloRefPidiakseh() {
        $template = "pi|diäkseh (-emmös, -däh/-dähes; -etähes; -dih/-dihes, -ettihes)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $is_reflexive = true;
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id, $is_reflexive);
//dd($result);        
        $expected = [[0=>'pidiäkseh',            
                      1=>'pie', 
                      2=>'pidäh/pidähes', 
                      3=>'pidä', 
                      4=>'pidih/pidihes', 
                      5=>'piji', 
                      6=>'pietähes', 
                      7=>'piett', 
                      8=>'pidä'], $num, 'pi', 'diäkseh'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateVerbOloRefPotkiekseh() {
        $template = "potki|ekseh (-mmos, -h/-hes; -tahes; -h/-hes, -ttihes)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $is_reflexive = true;
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id, $is_reflexive);
//dd($result);        
        $expected = [[0=>'potkiekseh',            
                      1=>'potki', 
                      2=>'potkih/potkihes', 
                      3=>'potki', 
                      4=>'potkih/potkihes', 
                      5=>'potki', 
                      6=>'potkitahes', 
                      7=>'potkitt', 
                      8=>'potki'], $num, 'potki', 'ekseh'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateVerbOloRefPuassiekseh() {
        $template = "puaš|šiekseh (-immos, -šihes; -itahes; -šihes, -ittihes)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $is_reflexive = true;
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id, $is_reflexive);
//dd($result);        
        $expected = [[0=>'puaššiekseh',            
                      1=>'puaši', 
                      2=>'puaššihes', 
                      3=>'puašši', 
                      4=>'puaššihes', 
                      5=>'puaši', 
                      6=>'puašitahes', 
                      7=>'puašitt', 
                      8=>'puašši'], $num, 'puaš', 'šiekseh'];
        $this->assertEquals( $expected, $result);        
    }

    public function testStemsFromTemplateVerbOloRefPunuokseh() {
        $template = "pun|uokseh (-ommos, -oh/-ohes; -otahes; -oih/-oihes, -ottihes)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $is_reflexive = true;
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id, $is_reflexive);
//dd($result);        
        $expected = [[0=>'punuokseh',            
                      1=>'puno', 
                      2=>'punoh/punohes', 
                      3=>'puno', 
                      4=>'punoih/punoihes', 
                      5=>'punoi', 
                      6=>'punotahes', 
                      7=>'punott', 
                      8=>'puno'], $num, 'pun', 'uokseh'];
        $this->assertEquals( $expected, $result);        
    }
       
    public function testStemsFromTemplateVerbOloRefPyrgiekseh() {
        $template = "pyr|giekseh (-rimmös, -gih/-gihes; -ritähes; -gih/-gihes, -rittihes)";
        $pos_id = 11; // verb
        $num = '';
        $dialect_id = '';
        $is_reflexive = true;
        $result = KarGram::stemsFromTemplate($template, $pos_id, $num, $dialect_id, $is_reflexive);
//dd($result);        
        $expected = [[0=>'pyrgiekseh',            
                      1=>'pyrri', 
                      2=>'pyrgih/pyrgihes', 
                      3=>'pyrgi', 
                      4=>'pyrgih/pyrgihes', 
                      5=>'pyrri', 
                      6=>'pyrritähes', 
                      7=>'pyrritt', 
                      8=>'pyrgi'], $num, 'pyr', 'giekseh'];
        $this->assertEquals( $expected, $result);        
    }    
    
    public function testIsBackVowels() {
        $word = 'pezo||v|ezi';
        $result = KarGram::isBackVowels($word);
        
        $expected = false;
        $this->assertEquals( $expected, $result);        
    }
    
    public function testCountSyllableSimple() {
        $word = 'pyrgiekseh';
        $result = KarGram::countSyllable($word);
        
        $expected = 3;
        $this->assertEquals( $expected, $result);        
    }
    
    public function testCountSyllableCompound() {
        $word = 'pezo||vezi';
        $result = KarGram::countSyllable($word);
        
        $expected = 2;
        $this->assertEquals( $expected, $result);        
    }
}
