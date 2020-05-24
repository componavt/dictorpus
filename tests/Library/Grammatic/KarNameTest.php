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
        $stems = ['aluššovat', '', '', '', 'aluššovi', 'aluššobi', 10=>TRUE];
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
        $stems = ['aluššovat', '', '', '', 'aluššovi', 'aluššobi', 10=>TRUE];
        $result = KarName::wordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num);
        
        $expected = 'aluššovat';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testStemsFromTemplatePaikku() {
        $template = 'paik|ku (-an, -kua; -koi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['paikku', 'paika', 'paikka', 'paikkua', 'paikoi', 'paikkoi', 10=>TRUE], '', 'paik', 'ku'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePajojoukko() {
        $template = 'pajojouk|ko (-on, -kuo; -koloi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['pajojoukko', 'pajojouko', 'pajojoukko', 'pajojoukkuo', 'pajojoukkoloi', 'pajojoukkoloi', 10=>TRUE], '', 'pajojouk', 'ko'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePuoli() {
        $template = 'puol|i (-en, -du; -ii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['puoli', 'puole', 'puole', 'puoldu', 'puoli', 'puoli', 10=>TRUE], '', 'puol', 'i'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePappi() {
        $template = 'päp|pi (-in, -pii; -pilöi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['päppi', 'päpi', 'päppi', 'päppii', 'päppilöi', 'päppilöi', 10=>FALSE], '', 'päp', 'pi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePia() {
        $template = 'piä (-n, -dy; -löi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['piä', 'piä', 'piä', 'piädy', 'piälöi', 'piälöi', 10=>FALSE], '', 'piä', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePackeh() {
        $template = 'pačkeh (-en, -tu; -ii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['pačkeh', 'pačkehe', 'pačkehe', 'pačkehtu', 'pačkehi', 'pačkehi', 10=>TRUE], '', 'pačkeh', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePada() {
        $template = 'pada (puan, padua; padoi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['pada', 'pua', 'pada', 'padua', 'pavoi', 'padoi', 10=>TRUE], '', 'pada', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePoudu() {
        $template = 'po|udu (-vvan, -udua; -udii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['poudu', 'povva', 'pouda', 'poudua', 'povvi', 'poudi', 10=>TRUE], '', 'po', 'udu'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePaganrengi() {
        $template = 'paganǁrengi (-n, -i; -löi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['paganrengi', 'paganrengi', 'paganrengi', 'paganrengii', 'paganrengilöi', 'paganrengilöi', 10=>FALSE], '', 'paganǁrengi', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateKolmaspaivy() {
        $template = 'kolma|späivy (-npiän, -ttupäiviä; -nziipäivii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['kolmaspäivy', 'kolmanpiä', 'kolmattupäivä', 'kolmattupäiviä', 'kolmanziipäivi', 'kolmanziipäivi', 10=>TRUE], '', 'kolma', 'späivy'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePahavuozi() {
        $template = 'pahaǁvu|ozi (-vven, -ottu; -ozii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['pahavuozi', 'pahavuvve', 'pahavuode', 'pahavuottu', 'pahavuozi', 'pahavuozi', 10=>TRUE], '', 'pahaǁvu', 'ozi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateAbei() {
        $template = 'ab|ei (-ien, -iedu; -ieloi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['abei', 'abie', 'abie', 'abiedu', 'abieloi', 'abieloi', 10=>TRUE], '', 'ab', 'ei'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateUuzi() {
        $template = 'uuzi (uvven, uuttu; uuzii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['uuzi', 'uvve', 'uude', 'uuttu', 'uuzi', 'uuzi', 10=>TRUE], '', 'uuzi', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateVuozi() {
        $template = 'vu|ozi (-vven, -ottu; -ozii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['vuozi', 'vuvve', 'vuode', 'vuottu', 'vuozi', 'vuozi', 10=>TRUE], '', 'vu', 'ozi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateTozi() {
        $template = 'to|zi (-ven, -ttu; -zii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['tozi', 'tove', 'tode', 'tottu', 'tozi', 'tozi', 10=>TRUE], '', 'to', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateReizi() {
        $template = 'rei|zi (-jen, -sty; -zii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['reizi', 'reije', 'reide', 'reisty', 'reizi', 'reizi', 10=>FALSE], '', 'rei', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateMagi() {
        $template = 'mä|gi (-in, -gie; -gilöi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['mägi', 'mäi', 'mäge', 'mägie', 'mägilöi', 'mägilöi', 10=>FALSE], '', 'mä', 'gi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateLagi() {
        $template = 'la|gi (-in, -gie; -giloi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['lagi', 'lai', 'lage', 'lagie', 'lagiloi', 'lagiloi', 10=>TRUE], '', 'la', 'gi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateKazi() {
        $template = 'kä|zi (-in, -tty; -zii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['käzi', 'käi', 'käde', 'kätty', 'käzi', 'käzi', 10=>FALSE], '', 'kä', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePedai() {
        $template = 'pedä|i (-jän, -jiä; -jii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['pedäi', 'pedäjä', 'pedäjä', 'pedäjiä', 'pedäji', 'pedäji', 10=>FALSE], '', 'pedä', 'i'];
        $this->assertEquals( $expected, $result);                
    }
 
    public function testStemsFromTemplateParzi() {
        $template = 'par|zi (-ren, -tu; -zii/-ziloi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['parzi', 'parre', 'parde', 'partu', 'parzi/parziloi', 'parzi/parziloi', 10=>true], '', 'par', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateKanzi() {
        $template = 'kan|zi (-nen, -tu; -zii/-ziloi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['kanzi', 'kanne', 'kande', 'kantu', 'kanzi/kanziloi', 'kanzi/kanziloi', 10=>true], '', 'kan', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePezovezi() {
        $template = 'pezoǁv|ezi (-ien, -etty; -ezii/-ezilöi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['pezovezi', 'pezovie', 'pezovede', 'pezovetty', 'pezovezi/pezovezilöi', 'pezovezi/pezovezilöi', 10=>FALSE], '', 'pezoǁv', 'ezi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateKuzi() {
        $template = 'ku|zi (-zen, -stu; -zii/-ziloi)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['kuzi', 'kuze', 'kuze', 'kustu', 'kuzi/kuziloi', 'kuzi/kuziloi', 10=>true], '', 'ku', 'zi'];
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
    
    public function testStemsFromTemplateMarrattavy() {
        $template = 'märrättäv|y (-än, -iä; -ii)';
        $name_num = '';
        $result = KarName::stemsFromTemplate($template, $name_num);

        $expected = [['märrättävy', 'märrättävä', 'märrättävä', 'märrättäviä', 'märrättävi', 'märrättävi', 10=>false], '', 'märrättäv', 'y'];
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
    }
 */
}
