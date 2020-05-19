<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\KarName;

// ./vendor/bin/phpunit tests/Library/Grammatic/KarNameTest

class KarNameTest extends TestCase
{
    /**
     * plural noun, nom sg
     *
     * @return void
     */
    public function testWordformByStems_pl_nom_sg()
    {
        $dialect_id=47;
        $lang_id=4;
        $gramset_id = 1;
        $name_num = 'pl';
        $stems = ['aluššovat', '', '', '', 'aluššovi', 'aluššobi'];
        $result = KarName::wordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num);
        
        $expected = '';
        $this->assertEquals( $expected, $result);        
    }
    
    /**
     * plural noun, nom sg
     *
     * @return void
     */
    public function testWordformByStems_pl_nom_pl()
    {
        $dialect_id=47;
        $lang_id=4;
        $gramset_id = 2;
        $name_num = 'pl';
        $stems = ['aluššovat', '', '', '', 'aluššovi', 'aluššobi'];
        $result = KarName::wordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num);
        
        $expected = 'aluššovat';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplatePaikku() {
        $template = 'paik|ku (-an, -kua; -koi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['paikku', 'paika', 'paikka', 'paikkua', 'paikoi', 'paikkoi'], '', 'paik', 'ku'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePajojoukko() {
        $template = 'pajojouk|ko (-on, -kuo; -koloi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['pajojoukko', 'pajojouko', 'pajojoukko', 'pajojoukkuo', 'pajojoukkoloi', 'pajojoukkoloi'], '', 'pajojouk', 'ko'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePuoli() {
        $template = 'puol|i (-en, -du; -ii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['puoli', 'puole', 'puole', 'puoldu', 'puoli', 'puoli'], '', 'puol', 'i'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePappi() {
        $template = 'päp|pi (-in, -pii; -pilöi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['päppi', 'päpi', 'päppi', 'päppii', 'päppilöi', 'päppilöi'], '', 'päp', 'pi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePia() {
        $template = 'piä (-n, -dy; -löi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['piä', 'piä', 'piä', 'piädy', 'piälöi', 'piälöi'], '', 'piä', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePackeh() {
        $template = 'pačkeh (-en, -tu; -ii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['pačkeh', 'pačkehe', 'pačkehe', 'pačkehtu', 'pačkehi', 'pačkehi'], '', 'pačkeh', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePada() {
        $template = 'pada (puan, padua; padoi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['pada', 'pua', 'pada', 'padua', 'pavoi', 'padoi'], '', 'pada', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePoudu() {
        $template = 'po|udu (-vvan, -udua; -udii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['poudu', 'povva', 'pouda', 'poudua', 'povvi', 'poudi'], '', 'po', 'udu'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePaganrengi() {
        $template = 'paganrengi (-n, -i; -löi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['paganrengi', 'paganrengi', 'paganrengi', 'paganrengii', 'paganrengilöi', 'paganrengilöi'], '', 'paganrengi', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateKolmaspaivy() {
        $template = 'kolma|späivy (-npiän, -ttupäiviä; -nziipäivii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['kolmaspäivy', 'kolmanpiä', 'kolmattupäivä', 'kolmattupäiviä', 'kolmanziipäivi', 'kolmanziipäivi'], '', 'kolma', 'späivy'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePahavuozi() {
        $template = 'pahavu|ozi (-vven, -ottu; -ozii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['pahavuozi', 'pahavuvve', 'pahavuode', 'pahavuottu', 'pahavuozi', 'pahavuozi'], '', 'pahavu', 'ozi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateAbei() {
        $template = 'ab|ei (-ien, -iedu; -ieloi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['abei', 'abie', 'abie', 'abiedu', 'abieloi', 'abieloi'], '', 'ab', 'ei'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateUuzi() {
        $template = 'uuzi (uvven, uuttu; uuzii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['uuzi', 'uvve', 'uude', 'uuttu', 'uuzi', 'uuzi'], '', 'uuzi', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateVuozi() {
        $template = 'vu|ozi (-vven, -ottu; -ozii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['vuozi', 'vuvve', 'vuode', 'vuottu', 'vuozi', 'vuozi'], '', 'vu', 'ozi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateTozi() {
        $template = 'to|zi (-ven, -ttu; -zii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['tozi', 'tove', 'tode', 'tottu', 'tozi', 'tozi'], '', 'to', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateReizi() {
        $template = 'rei|zi (-jen, -sty; -zii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['reizi', 'reije', 'reide', 'reisty', 'reizi', 'reizi'], '', 'rei', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateMagi() {
        $template = 'mä|gi (-in, -gie; -gilöi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['mägi', 'mäi', 'mäge', 'mägie', 'mägilöi', 'mägilöi'], '', 'mä', 'gi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateLagi() {
        $template = 'la|gi (-in, -gie; -giloi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['lagi', 'lai', 'lage', 'lagie', 'lagiloi', 'lagiloi'], '', 'la', 'gi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateKazi() {
        $template = 'kä|zi (-in, -tty; -zii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['käzi', 'käi', 'käde', 'kätty', 'käzi', 'käzi'], '', 'kä', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePedai() {
        $template = 'pedä|i (-jän, -jiä; -jii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['pedäi', 'pedäjä', 'pedäjä', 'pedäjiä', 'pedäji', 'pedäji'], '', 'pedä', 'i'];
        $this->assertEquals( $expected, $result);                
    }
 
    public function testStemsFromTemplateParzi() {
        $template = 'par|zi (-ren, -tu; -zii/-ziloi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['parzi', 'parre', 'parde', 'partu', 'parzi/parziloi', 'parzi/parziloi'], '', 'par', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateKanzi() {
        $template = 'kan|zi (-nen, -tu; -zii/-ziloi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['kanzi', 'kanne', 'kande', 'kantu', 'kanzi/kanziloi', 'kanzi/kanziloi'], '', 'kan', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePezovezi() {
        $template = 'pezov|ezi (-ien, -etty; -ezii/-ezilöi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['pezovezi', 'pezovie', 'pezovede', 'pezovetty', 'pezovezi/pezovezilöi', 'pezovezi/pezovezilöi'], '', 'pezov', 'ezi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateKuzi() {
        $template = 'ku|zi (-zen, -stu; -zii/-ziloi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['kuzi', 'kuze', 'kuze', 'kustu', 'kuzi/kuziloi', 'kuzi/kuziloi'], '', 'ku', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePordahat() {
        $template = 'pordah|at (-ien, -ii)';
        $name_num = 'pl';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['pordahat', '', '', '', 'pordahi', 'pordahi'], 'pl', 'pordah', 'at'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePualikkupordahat() {
        $template = 'pualikkupordah|at (-ien, -ii)';
        $name_num = 'pl';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['pualikkupordahat', '', '', '', 'pualikkupordahi', 'pualikkupordahi'], 'pl', 'pualikkupordah', 'at'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateKodihuolet() {
        $template = 'kodihuol|et (-ien/-iloin, -ii/-iloi)';
        $name_num = 'pl';

        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['kodihuolet', '', '', '', 'kodihuoli/kodihuoliloi', 'kodihuoli/kodihuoliloi'], 'pl', 'kodihuol', 'et'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testParseGenPl() {
        $base = 'kodihuol';
        $gen_pl_suf = '-ien/-iloin';
        $result = KarName::parseGenPl($base, $gen_pl_suf);

        $expected = 'kodihuoli/kodihuoliloi';
        $this->assertEquals( $expected, $result);                
    }
    
    public function testPartPlBase() {
        $base = 'kodihuol';
        $part_pl_suf = '-ii/-iloi';
        $result = KarName::partPlBase($base, $part_pl_suf);

        $expected = 'kodihuoli/kodihuoliloi';
        $this->assertEquals( $expected, $result);                
    }
    
/*    
    public function testStemsFromTemplateP() {
        $template = '';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [[''], '', '', ''];
        $this->assertEquals( $expected, $result);                
    }*/
}
