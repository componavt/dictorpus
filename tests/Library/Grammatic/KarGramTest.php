<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\KarGram;

// php artisan make:test Library\Grammatic\KarGram
// ./vendor/bin/phpunit tests/Library/Grammatic\KarGram

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
        $result = KarGram::garmVowel($stem, $vowel);
        
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
}
