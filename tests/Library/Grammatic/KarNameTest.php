<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Library\Grammatic\KarName;

// ./vendor/bin/phpunit tests/Library/Grammatic/KarNameTest

class KarNameTest extends TestCase
{
    public function testStemsFromTemplateMittuine() {
        $template = 'mittu|ine (-zen/-man, -stu/-mua; -zii/-mii)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['mittuine', 'mittuze/mittuma', 'mittuze/mittuma', 'mittustu/mittumua', 'mittuzi/mittumi', 'mittuzi/mittumi', 10=>TRUE], '', 'mittu', 'ine'];
        $this->assertEquals( $expected, $result);     
    }
    
    public function testStemsFromTemplateAika() {
        $template = 'ai|ka [ja] ';
        $name_num = '';
        $pos_id=5;
        $lang_id=4;
        $dialect_id=46; // северно-карельский
        
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);

        $expected = [['aika', 'aija', 'aika', 'aikua', 'aijoi', 'aikoi', 'aika', 10=>TRUE], null, 'ai', 'ka'];
        $this->assertEquals( $expected, $result);     
    }

    public function testWordformByStemsGenPlAika()
    {
        $template = 'ai|ka [ja] ';
        $dialect_id=46; // северно-карельский
        $lang_id=4;
        $pos_id=5;
        $gramset_id = 24; // ген. мн.ч.
        $name_num = '';
        list($stems, $name_num, $stem, $affix) = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);
        $result = KarName::wordformByStems($stems, $gramset_id, $lang_id, $dialect_id, $name_num);
        
        $expected = 'aikojen';
        $this->assertEquals( $expected, $result);        
    }
    
    // plural noun, nom sg
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
    
    // plural noun, nom sg
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
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['paikku', 'paika', 'paikka', 'paikkua', 'paikoi', 'paikkoi', 10=>TRUE], '', 'paik', 'ku'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePajojoukko() {
        $template = 'pajojouk|ko (-on, -kuo; -koloi)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['pajojoukko', 'pajojouko', 'pajojoukko', 'pajojoukkuo', 'pajojoukkoloi', 'pajojoukkoloi', 10=>TRUE], '', 'pajojouk', 'ko'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePuoli() {
        $template = 'puol|i (-en, -du; -ii)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['puoli', 'puole', 'puole', 'puoldu', 'puoli', 'puoli', 10=>TRUE], '', 'puol', 'i'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePappi() {
        $template = 'päp|pi (-in, -pii; -pilöi)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['päppi', 'päpi', 'päppi', 'päppii', 'päppilöi', 'päppilöi', 10=>FALSE], '', 'päp', 'pi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePia() {
        $template = 'piä (-n, -dy; -löi)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['piä', 'piä', 'piä', 'piädy', 'piälöi', 'piälöi', 10=>FALSE], '', 'piä', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePackeh() {
        $template = 'pačkeh (-en, -tu; -ii)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['pačkeh', 'pačkehe', 'pačkehe', 'pačkehtu', 'pačkehi', 'pačkehi', 10=>TRUE], '', 'pačkeh', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePada() {
        $template = 'pada (puan, padua; padoi)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['pada', 'pua', 'pada', 'padua', 'pavoi', 'padoi', 10=>TRUE], '', 'pada', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePoudu() {
        $template = 'po|udu (-vvan, -udua; -udii)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['poudu', 'povva', 'pouda', 'poudua', 'povvi', 'poudi', 10=>TRUE], '', 'po', 'udu'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePaganrengi() {
        $template = 'paganǁrengi (-n, -i; -löi)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['paganrengi', 'paganrengi', 'paganrengi', 'paganrengii', 'paganrengilöi', 'paganrengilöi', 10=>FALSE], '', 'paganǁrengi', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateKolmaspaivy() {
        $template = 'kolma|späivy (-npiän, -ttupäiviä; -nziipäivii)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['kolmaspäivy', 'kolmanpiä', 'kolmattupäivä', 'kolmattupäiviä', 'kolmanziipäivi', 'kolmanziipäivi', 10=>TRUE], '', 'kolma', 'späivy'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePahavuozi() {
        $template = 'pahaǁvu|ozi (-vven, -ottu; -ozii)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['pahavuozi', 'pahavuvve', 'pahavuode', 'pahavuottu', 'pahavuozi', 'pahavuozi', 10=>TRUE], '', 'pahaǁvu', 'ozi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateAbei() {
        $template = 'ab|ei (-ien, -iedu; -ieloi)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['abei', 'abie', 'abie', 'abiedu', 'abieloi', 'abieloi', 10=>TRUE], '', 'ab', 'ei'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateUuzi() {
        $template = 'uuzi (uvven, uuttu; uuzii)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['uuzi', 'uvve', 'uude', 'uuttu', 'uuzi', 'uuzi', 10=>TRUE], '', 'uuzi', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateVuozi() {
        $template = 'vu|ozi (-vven, -ottu; -ozii)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['vuozi', 'vuvve', 'vuode', 'vuottu', 'vuozi', 'vuozi', 10=>TRUE], '', 'vu', 'ozi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateTozi() {
        $template = 'to|zi (-ven, -ttu; -zii)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['tozi', 'tove', 'tode', 'tottu', 'tozi', 'tozi', 10=>TRUE], '', 'to', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateReizi() {
        $template = 'rei|zi (-jen, -sty; -zii)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['reizi', 'reije', 'reide', 'reisty', 'reizi', 'reizi', 10=>FALSE], '', 'rei', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateMagi() {
        $template = 'mä|gi (-in, -gie; -gilöi)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['mägi', 'mäi', 'mäge', 'mägie', 'mägilöi', 'mägilöi', 10=>FALSE], '', 'mä', 'gi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateLagi() {
        $template = 'la|gi (-in, -gie; -giloi)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['lagi', 'lai', 'lage', 'lagie', 'lagiloi', 'lagiloi', 10=>TRUE], '', 'la', 'gi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateKazi() {
        $template = 'kä|zi (-in, -tty; -zii)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['käzi', 'käi', 'käde', 'kätty', 'käzi', 'käzi', 10=>FALSE], '', 'kä', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePedai() {
        $template = 'pedä|i (-jän, -jiä; -jii)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['pedäi', 'pedäjä', 'pedäjä', 'pedäjiä', 'pedäji', 'pedäji', 10=>FALSE], '', 'pedä', 'i'];
        $this->assertEquals( $expected, $result);                
    }
 
    public function testStemsFromTemplateParzi() {
        $template = 'par|zi (-ren, -tu; -zii/-ziloi)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['parzi', 'parre', 'parde', 'partu', 'parzi/parziloi', 'parzi/parziloi', 10=>true], '', 'par', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateKanzi() {
        $template = 'kan|zi (-nen, -tu; -zii/-ziloi)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['kanzi', 'kanne', 'kande', 'kantu', 'kanzi/kanziloi', 'kanzi/kanziloi', 10=>true], '', 'kan', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePezovezi() {
        $template = 'pezoǁv|ezi (-ien, -etty; -ezii/-ezilöi)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['pezovezi', 'pezovie', 'pezovede', 'pezovetty', 'pezovezi/pezovezilöi', 'pezovezi/pezovezilöi', 10=>FALSE], '', 'pezoǁv', 'ezi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateKuzi() {
        $template = 'ku|zi (-zen, -stu; -zii/-ziloi)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['kuzi', 'kuze', 'kuze', 'kustu', 'kuzi/kuziloi', 'kuzi/kuziloi', 10=>true], '', 'ku', 'zi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePordahat() {
        $template = 'pordah|at (-ien, -ii)';
        $name_num = 'pl';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['pordahat', '', '', '', 'pordahi', 'pordahi'], 'pl', 'pordah', 'at'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplatePualikkupordahat() {
        $template = 'pualikkupordah|at (-ien, -ii)';
        $name_num = 'pl';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['pualikkupordahat', '', '', '', 'pualikkupordahi', 'pualikkupordahi'], 'pl', 'pualikkupordah', 'at'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateKodihuolet() {
        $template = 'kodihuol|et (-ien/-iloin, -ii/-iloi)';
        $name_num = 'pl';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['kodihuolet', '', '', '', 'kodihuoli/kodihuoliloi', 'kodihuoli/kodihuoliloi'], 'pl', 'kodihuol', 'et'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromTemplateMarrattavy() {
        $template = 'märrättäv|y (-än, -iä; -ii)';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [['märrättävy', 'märrättävä', 'märrättävä', 'märrättäviä', 'märrättävi', 'märrättävi', 10=>false], '', 'märrättäv', 'y'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateRanta() {
        $template = 'ran|ta [na]';
        $name_num = '';
        $pos_id=5;
        $lang_id=4;
        $dialect_id = 46;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);

        $expected = [[0=>'ranta', 1=>'ranna', 2=>'ranta', 3=>'rantua', 4=>'rannoi', 5=>'rantoi', 6=>'ranta', 10=>TRUE], '', 'ran', 'ta'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateRandu() {
        $template = 'ran|du [na]';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [[0=>'randu', 1=>'ranna', 2=>'randa', 3=>'randua', 4=>'rannoi', 5=>'randoi', 6=>'randa', 10=>TRUE], '', 'ran', 'du'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplatePelto() {
        $template = 'pel|to [lo]';
        $name_num = '';
        $pos_id=5;
        $lang_id=4;
        $dialect_id = 46;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);

        $expected = [[0=>'pelto', 1=>'pello', 2=>'pelto', 3=>'peltuo', 4=>'peltoloi', 5=>'peltoloi', 6=>'pelto', 10=>TRUE], '', 'pel', 'to'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplatePeldo() {
        $template = 'pel|do [lo]';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [[0=>'peldo', 1=>'pello', 2=>'peldo', 3=>'pelduo', 4=>'peldoloi', 5=>'peldoloi', 6=>'peldo', 10=>TRUE], '', 'pel', 'do'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateMuaProper() {
        $template = 'mua []';
        $name_num = '';
        $pos_id=5;
        $lang_id=4;
        $dialect_id=46;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, $dialect_id);

        $expected = [[0=>'mua', 1=>'mua', 2=>'mua', 3=>'muata', 4=>'mai', 5=>'mai', 6=>'mua', 10=>TRUE], '', 'mua', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateMuaOlo() {
        $template = 'mua []';
        $name_num = '';
        $pos_id=5;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num, null);

        $expected = [[0=>'mua', 1=>'mua', 2=>'mua', 3=>'muadu', 4=>'mualoi', 5=>'mualoi', 6=>'mua', 10=>TRUE], '', 'mua', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateNuoriProper() {
        $template = 'nuor|i [e, ]';
        $name_num = '';
        $pos_id=1;
        $lang_id=4;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'nuori', 1=>'nuore', 2=>'nuore', 3=>'nuorta', 4=>'nuori', 5=>'nuori', 6=>'nuor', 10=>TRUE], '', 'nuor', 'i'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateNuoriOlo() {
        $template = 'nuor|i [e, ]';
        $name_num = '';
        $pos_id=1;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'nuori', 1=>'nuore', 2=>'nuore', 3=>'nuordu', 4=>'nuori', 5=>'nuori', 6=>'nuor', 10=>TRUE], '', 'nuor', 'i'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateLyhytProper() {
        $template = 'lyhy|t [ö, t]';
        $name_num = '';
        $pos_id=1;
        $lang_id=4;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'lyhyt', 1=>'lyhyö', 2=>'lyhyö', 3=>'lyhyttä', 4=>'lyhyi', 5=>'lyhyi', 6=>'lyhyt', 10=>FALSE], '', 'lyhy', 't'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateLyhytOlo() {
        $template = 'lyhy|t [ö, t]';
        $name_num = '';
        $pos_id=1;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'lyhyt', 1=>'lyhyö', 2=>'lyhyö', 3=>'lyhytty', 4=>'lyhyzi', 5=>'lyhyzi', 6=>'lyhyt', 10=>FALSE], '', 'lyhy', 't'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateVesi() {
        $template = 've|si [je/te, t]';
        $name_num = '';
        $pos_id=1;
        $lang_id=4;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'vesi', 1=>'veje', 2=>'vete', 3=>'vettä', 4=>'vesi', 5=>'vesi', 6=>'vet', 10=>FALSE], '', 've', 'si'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateVezi() {
        $template = 'v|ezi [ie/ede, et]';
        $name_num = '';
        $pos_id=1;
        $lang_id=5;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'vezi', 1=>'vie', 2=>'vede', 3=>'vetty', 4=>'vezi', 5=>'vezi', 6=>'vet', 10=>FALSE], '', 'v', 'ezi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateKeitinleipa() {
        $template = 'keitinlei|pä [vä]';
        $name_num = '';
        $pos_id=1;
        $lang_id=4;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'keitinleipä', 1=>'keitinleivä', 2=>'keitinleipä', 3=>'keitinleipyä', 4=>'keitinleivi', 5=>'keitinleipi', 6=>'keitinleipä', 10=>FALSE], '', 'keitinlei', 'pä'];
        $this->assertEquals( $expected, $result);                
    }
 
    public function testStemsFromMiniTemplateHenatukku() {
        $template = 'henätuk|ku [u]';
        $name_num = '';
        $pos_id=1;
        $lang_id=4;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'henätukku', 
                      1=>'henätuku', 
                      2=>'henätukku', 
                      3=>'henätukkuo', 
                      4=>'henätukkuloi', 
                      5=>'henätukkuloi', 
                      6=>'henätukku', 
                     10=>TRUE], 
                    null, 'henätuk', 'ku'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateKate() {
        $template = 'kat|e [tie, et]';
        $name_num = '';
        $pos_id=1;
        $lang_id=4;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'kate', 
                      1=>'kattie', 
                      2=>'kattie', 
                      3=>'katetta', 
                      4=>'kattei', 
                      5=>'kattei', 
                      6=>'katet', 
                     10=>TRUE], 
                    null, 'kat', 'e'];
        $this->assertEquals( $expected, $result);                
    }

    public function testStemsFromMiniTemplateElama() {
        $template = 'elämä[]';
        $name_num = '';
        $pos_id=1;
        $lang_id=4;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'elämä', 
                      1=>'elämä', 
                      2=>'elämä', 
                      3=>'elämyä', 
                      4=>'elämi', 
                      5=>'elämi', 
                      6=>'elämä', 
                     10=>FALSE], 
                    null, 'elämä', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateAhkivo() {
        $template = 'ahkiv|o [o]';
        $name_num = '';
        $pos_id=1;
        $lang_id=4;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'ahkivo', 
                      1=>'ahkivo', 
                      2=>'ahkivo', 
                      3=>'ahkivuo', 
                      4=>'ahkivoi', 
                      5=>'ahkivoi', 
                      6=>'ahkivo', 
                     10=>TRUE], 
                    null, 'ahkiv', 'o'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateAnoppi() {
        $template = 'anop|pi [i/pi]';
        $name_num = '';
        $pos_id=1;
        $lang_id=4;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'anoppi', 
                      1=>'anopi', 
                      2=>'anoppi', 
                      3=>'anoppie', 
                      4=>'anoppiloi', 
                      5=>'anoppiloi', 
                      6=>'anoppi', 
                     10=>TRUE], 
                    null, 'anop', 'pi'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateKirja() {
        $template = 'kirj|a [a]';
        $name_num = '';
        $pos_id=1;
        $lang_id=4;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'kirja', 
                      1=>'kirja', 
                      2=>'kirja', 
                      3=>'kirjua', 
                      4=>'kirjoi', 
                      5=>'kirjoi', 
                      6=>'kirja', 
                     10=>TRUE], 
                    null, 'kirj', 'a'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateAhanta() {
        $template = 'ahan|ta [na/ta]';
        $name_num = '';
        $pos_id=1;
        $lang_id=4;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'ahanta', 
                      1=>'ahanna', 
                      2=>'ahanta', 
                      3=>'ahantua', 
                      4=>'ahannoi', 
                      5=>'ahantoi', 
                      6=>'ahanta', 
                     10=>TRUE], 
                    null, 'ahan', 'ta'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateTai() {
        $template = 'täi []';
        $name_num = '';
        $pos_id=1;
        $lang_id=4;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'täi', 
                      1=>'täi', 
                      2=>'täi', 
                      3=>'täitä', 
                      4=>'täilöi', 
                      5=>'täilöi', 
                      6=>'täi', 
                     10=>FALSE], 
                    null, 'täi', ''];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplatePata() {
        $template = 'p|ata [ua/ata]';
        $name_num = '';
        $pos_id=1;
        $lang_id=4;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'pata', 
                      1=>'pua', 
                      2=>'pata', 
                      3=>'patua', 
                      4=>'pavoi', 
                      5=>'patoi', 
                      6=>'pata', 
                     10=>TRUE], 
                    null, 'p', 'ata'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateHikilehti() {
        $template = 'hikileh|ti [e/te]';
        $name_num = '';
        $pos_id=1;
        $lang_id=4;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'hikilehti', 
                      1=>'hikilehe', 
                      2=>'hikilehte', 
                      3=>'hikilehtie', 
                      4=>'hikilehi', 
                      5=>'hikilehti', 
                      6=>'hikilehte', 
                     10=>FALSE], 
                    null, 'hikileh', 'ti'];
        $this->assertEquals( $expected, $result);                
    }
    
    public function testStemsFromMiniTemplateUlkoaitta() {
        $template = 'ulkoǁait|ta [a/ta]';
        $name_num = '';
        $pos_id=5;
        $lang_id=4;
        $result = KarName::stemsFromTemplate($template, $lang_id, $pos_id, $name_num);

        $expected = [[0=>'ulkoaitta', 
                      1=>'ulkoaita', 
                      2=>'ulkoaitta', 
                      3=>'ulkoaittua', 
                      4=>'ulkoaitoi', 
                      5=>'ulkoaittoi', 
                      6=>'ulkoaitta', 
                     10=>TRUE], 
                    null, 'ulkoǁait', 'ta'];
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
    
    public function testIllSgBaseOloPartSY()
    {
        $dialect_id=44; // New written Livvic
        $lang_id=5;
        $stem0 = 'iänestämine';
        $stem1 = 'iänestämize';
        $stem3 = 'iänestämisty';
        $result = KarName::illSgBase($stem0, $stem1, $stem3);
        
        $expected = 'iänestämize';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testIllSgBaseOloPartUO()
    {
        $dialect_id=44; // New written Livvic
        $lang_id=5;
        $stem0 = 'rahvasjoukko';
        $stem1 = 'rahvasjouko';
        $stem3 = 'rahvasjoukkuo';
        $result = KarName::illSgBase($stem0, $stem1, $stem3);
        
        $expected = 'rahvasjoukko';
        $this->assertEquals( $expected, $result);        
    }
    
    public function testSuggestTemplates() {
        $lang_id=4;
        $words = [
            1 => 'hil’l’a',
            2 => 'alušpuu',
            3 => 'šyvä',
            4 => 'kel’l’a',
            5 => 'työ',
            6 => 'penker',
            7 => 'jouhi',
            8 => 'muččo',
            9 => 'očča',
            10 => 'appi',
            11 => 'elänneh',
            12 => 'eväš',
            13 => 'alačuš',
            14 => 'kulta',
            15 => 'pelto',
            16 => 'eisto',
            17 => 'muisti',
            18 => 'lämpö',
            19 => 'alapirtti',
            20 => 'hautapačaš',
            21 => 'veičči',
            22 => 'kallita',
            23 => 'joucen',
            24 => 'kaklačin',
            25 => 'šuvi',
            26 => 'koški',
            27 => 'lehti',
            28 => 'ahventuppi',
            29 => 'kapris',
            30 => 'autuoš',
            31 => 'kankaš',
            32 => 'mieš',
            33 => 'kiärmis',
            34 => 'lakši',
            35 => 'yksi',
            36 => 'vahti',
            37 => 'käsi',
            38 => 'henkenhätä',
            39 => 'holve',
            40 => 'iltakoite',
            41 => 'reki',
            42 => 'apposet',
            43 => 'hieta',
            44 => 'hiisi',
            45 => 'hiki',
            46 => 'jiäkäš',
            47 => 'aijeh',
            48 => 'eläkeh',
            49 => 'ruis',
            50 => 'ašel',
            51 => 'ijen',
            52 => 'jänis',
            53 => 'kajaš',
            54 => 'šukši',
            55 => 'kätyt',
            56 => 'huuhaltua',
            57 => 'alačoin',
            58 => 'kampa',
            59 => 'nuorembi',
            60 => 'vašempi',
            61 => 'kaššin',
            62 => 'elin',
            63 => 'huokain',
            64 => 'liemi',
            65 => 'lampi',
            66 => 'kynši',
            67 => 'kanši',
            68 => 'ankaruš',
            69 => 'katonut',
            70 => 'pahuš',
            71 => 'hammaš',
            72 => 'jiäruopaš',
            73 => 'karvaš',
            74 => 'hapan',
            75 => 'helveh',
            76 => 'tarvis',
            77 => 'vemmel',
            78 => 'haven',
            79 => 'ape',
            80 => 'lämmin',
            81 => 'lapši',
            82 => 'immyt',
            83 => 'karši',
            84 => 'itkuvirši',
            85 => 'hirši',
            86 => 'paharaiska',
            87 => 'koivuni',
            88 => 'čirkkuni',
            89 => 'šyömine',
            90 => 'tytär',
            91 => 'puhaš',
            92 => 'mätäš',
            93 => 'hijaš',
            94 => 'allaš',
            95 => 'kinnaš',
            96 => 'parraš',
            97 => 'kajeh',
            98 => 'huuvveh',
            99 => 'kannel',
            100 => 'manner',
            101 => 'voije',
            102 => 'kare',
            103 => 'košše',
            104 => 'kaute',
            105 => 'jänne',
            106 => 'kašše',
            107 => 'jiäte',
            108 => 'vuajin',
            109 => 'kannin',
            110 => 'jaluššin',
            111 => 'šoitin',
            112 => 'hävitöin',
            113 => 'issuin',
            114 => 'kevät',
            115 => 'haka',
            116 => 'holvata',
            117 => 'ahjota',
            118 => 'vuoši',
            119 => 'kuukauši',
            120 => 'joki',
            121 => 'hauki',
            122 => 'kapi',
            123 => 'keltatauti',
            124 => 'uuši',
            125 => 'anti',
            126 => 'čirškua',
            127 => 'juokšuttua',
            128 => 'pitkä',
            129 => 'jatko',
            130 => 'itku',
            131 => 'parta',
            132 => 'tieto',
            133 => 'reikä',
            134 => 'koti',
            135 => 'aika',
            136 => 'hoito',
            137 => 'joiku',
            138 => 'lintu',
            139 => 'luota',
            140 => 'hupa',
            141 => 'hako',
            142 => 'huuto',
            143 => 'šuku',
            144 => 'kalanpyytö',
            145 => 'näköni'
        ];
        $result = [];
        foreach ($words as $lemma_id=>$word) {
            $result[$lemma_id] = KarName::suggestTemplates($lang_id, $word);
        }
        $expected = [
            1 => ['hil’l’a [, ]', 'hil’l’a []'],
            2 => ['alušpuu [, ]', 'alušpuu []'],
            3 => ['šyvä []'],
            4 => ['kel’l’a [, ]', 'kel’l’a []'],
            5 => ['työ []'],
            6 => ['penker [e, ]'],
            7 => ['jouhi []', 'jouh|i [e]', 'jou|hi [e, ]'],
            8 => ['muč|čo [o]', 'muččo []'],
            9 => ['oč|ča [a]', 'očča []'],
            10 => ['appi []', 'app|i [e]', 'ap|pi [e]', 'ap|pi [i]', 'ap|pi [ve]', 'ap|pi [vi]'],
            11 => ['elänneh [e, ]', 'elän|neh [tehe, neh]'],
            12 => ['evä|š [hä, š]', 'evä|š [kše, š]', 'e|väš [pähä, väš]'],
            13 => ['alaču|š [kše, š]', 'alač|uš [čuo, ut]'],
            14 => ['kulta []', 'kul|ta [ja]', 'kul|ta [la]', 'kul|ta [va]'],
            15 => ['pelto []', 'pel|to [jo]', 'pel|to [lo]', 'pel|to [vo]'],
            16 => ['eisto []', 'eis|to [jo]', 'eis|to [so]', 'eis|to [vo]'],
            17 => ['muisti []', 'muist|i [e]', 'muis|ti [je]', 'muis|ti [ji]', 'muis|ti [si]', 'muis|ti [ve]', 'muis|ti [vi]'],
            18 => ['lämpö []', 'läm|pö [mö]', 'läm|pö [vö]'],
            19 => ['alapirtti []', 'alapirtt|i [e]', 'alapirt|ti [a]', 'alapirt|ti [e]', 'alapirt|ti [i]', 'alapirt|ti [je]', 'alapirt|ti [ji]', 'alapirt|ti [ve]', 'alapirt|ti [vi]'],
            20 => ['hautapača|š [ha, š]', 'hautapača|š [kše, š]', 'hautapač|aš [čahe, aš]'],
            21 => ['vei|čči [če, s]', 'veič|či [e]', 'veič|či [i]', 'veičči []', 'veičč|i [e]'],
            22 => ['kallita []', 'kalli|ta [ja]', 'kalli|ta [va]', 'kalli|ta [če]'],
            23 => ['joucen [e, ]', 'jouc|en [cene, en]'],
            24 => ['kaklačin [e, ]', 'kaklači|n [me, n]', 'kaklač|in [ma, in]', 'kaklač|in [čime, in]'],
            25 => ['šuvi []', 'šuv|i [e]'],
            26 => ['koški []', 'koš|ki [e]', 'koš|ki [je]', 'koš|ki [ji]', 'koš|ki [ve]', 'koš|ki [vi]'],
            27 => ['lehti []', 'leh|ti [e]', 'leh|ti [i]', 'leh|ti [je]', 'leh|ti [ji]', 'leh|ti [ve]', 'leh|ti [vi]'],
            28 => ['ahventuppi []', 'ahventupp|i [e]', 'ahventup|pi [e]', 'ahventup|pi [i]', 'ahventup|pi [ve]', 'ahventup|pi [vi]'],
            29 => ['kapri|s [he, s]', 'kapri|s [kse, s]', 'kapr|is [ehe, is]'],
            30 => ['autuo|š [ha, š]', 'autuo|š [kše, š]'],
            31 => ['kanka|š [ha, š]', 'kanka|š [kše, š]', 'kank|aš [kaha, aš]'],
            32 => ['mie|š [he, š]', 'mie|š [hä, š]', 'mie|š [kše, š]'],
            33 => ['kiärmi|s [he, s]', 'kiärmi|s [kse, s]', 'kiärm|is [ehe, is]'],
            34 => ['lak|ši [ne, t]', 'lak|ši [ne/te, t]', 'lakši []', 'lakš|i [e]', 'la|kši [he]', 'la|kši [kše, š]'],
            35 => ['yksi []', 'yks|i [e]', 'yk|si [je, t]', 'y|ksi [he/hte, h]'],
            36 => ['vahti []', 'vah|ti [e]', 'vah|ti [i]', 'vah|ti [je]', 'vah|ti [ji]', 'vah|ti [ve]', 'vah|ti [vi]'],
            37 => ['k|äsi [iä, ät]', 'käsi []', 'käs|i [e]', 'kä|si [je, t]'],
            38 => ['henkenh|ätä [iä]', 'henkenh|ätä [yä]', 'henkenhätä []', 'henkenhä|tä [jä]', 'henkenhä|tä [vä]'],
            39 => ['holv|e [ie, et]'],
            40 => ['iltakoit|e [ie, et]', 'iltakoit|e [ie]', 'iltakoit|e [tie, et]'],
            41 => ['reki []', 'rek|i [e]', 're|ki [je]', 're|ki [ji]', 're|ki [ve]', 're|ki [vi]', 'r|eki [ie]'],
            42 => ['appos|et [ie]'],
            43 => ['hieta []', 'hie|ta [ja]', 'hie|ta [va]', 'hi|eta [ija]'],
            44 => ['hiisi []', 'hiis|i [e]', 'hii|si [je, t]'],
            45 => ['hiki []', 'hik|i [e]', 'hi|ki [je]', 'hi|ki [ji]', 'hi|ki [ve]', 'hi|ki [vi]'],
            46 => ['jiäk|äš [kähä, äš]', 'jiäkä|š [hä, š]', 'jiäkä|š [kše, š]'],
            47 => ['aijeh [e, ]', 'ai|jeh [kehe, jeh]', 'ai|jeh [tehe, jeh]'],
            48 => ['eläkeh [e, ]', 'eläk|eh [kehe, eh]'],
            49 => ['rui|s [he, s]', 'rui|s [kse, s]', 'ru|is [kehe, is]'],
            50 => ['ašel [e, ]', 'aš|el [kele, el]'],
            51 => ['ijen [e, ]', 'i|jen [kene, jen]'],
            52 => ['jäni|s [he, s]', 'jäni|s [kse, s]', 'jän|is [ehe, is]'],
            53 => ['kaja|š [ha, š]', 'kaja|š [kše, š]', 'ka|jaš [taha, jaš]'],
            54 => ['šuk|ši [ne, t]', 'šuk|ši [ne/te, t]', 'šukši []', 'šukš|i [e]', 'šu|kši [he]', 'šu|kši [kše, š]'],
            55 => ['käty|t [ö, t]', 'kät|yt [kyö, yt]'],
            56 => ['huuhaltua []', 'huuhalt|ua [a, ua]', 'huuhal|tua [la, tua]'],
            57 => ['alačoin [e, ]', 'alačoi|n [me, n]', 'alačo|in [ma, in]'],
            58 => ['kampa []', 'kam|pa [ma]', 'kam|pa [va]'],
            59 => ['nuorembi []', 'nuoremb|i [e]', 'nuorem|bi [ma]'],
            60 => ['vašempi []', 'vašemp|i [e]', 'vašem|pi [ma]', 'vašem|pi [me]', 'vašem|pi [mi]', 'vašem|pi [ve]', 'vašem|pi [vi]'],
            61 => ['kaš|šin [time, šin]', 'kaššin [e, ]', 'kašši|n [me, n]', 'kašš|in [ma, in]'],
            62 => ['elin [e, ]', 'eli|n [me, n]', 'el|in [mä, in]'],
            63 => ['huokain [e, ]', 'huokai|n [me, n]', 'huoka|in [ma, in]'],
            64 => ['liemi []', 'liem|i [e]', 'lie|mi [me, n]'],
            65 => ['lampi []', 'lamp|i [e]', 'lam|pi [ma]', 'lam|pi [me]', 'lam|pi [mi]', 'lam|pi [ve]', 'lam|pi [vi]'],
            66 => ['kyn|ši [ne, t]', 'kyn|ši [ne/te, t]', 'kynši []', 'kynš|i [e]'],
            67 => ['kan|ši [ne, t]', 'kan|ši [ne/te, t]', 'kanši []', 'kanš|i [e]'],
            68 => ['ankaru|š [kše, š]', 'ankaru|š [o, š]'],
            69 => ['katonu|t [o, t]'],
            70 => ['pahu|š [kše, š]', 'pahu|š [o, t]'],
            71 => ['hamma|š [ha, š]', 'hamma|š [kše, š]', 'ham|maš [paha, maš]'],
            72 => ['jiäruopa|š [ha, š]', 'jiäruopa|š [kše, š]', 'jiäruop|aš [paha, aš]'],
            73 => ['karva|š [ha, š]', 'karva|š [kše, š]', 'kar|vaš [paha, vaš]'],
            74 => ['hapan [e, ]', 'hapa|n [me, n]', 'hap|an [pame, an]'],
            75 => ['helveh [e, ]', 'hel|veh [pehe, veh]'],
            76 => ['tarvi|s [he, s]', 'tarvi|s [kse, s]', 'tarv|is [ehe, is]', 'tar|vis [pehe, vis]'],
            77 => ['vemmel [e, ]', 'vem|mel [pele, mel]'],
            78 => ['haven [e, ]', 'ha|ven [pene, ven]'],
            79 => ['ap|e [ie, et]', 'ap|e [pie, et]', 'ap|e [tie, et]'],
            80 => ['lämmin [e, ]', 'lämmi|n [me, n]', 'lämm|in [mä, in]', 'läm|min [pimä, min]'],
            81 => ['lap|ši [ne, t]', 'lap|ši [ne/te, t]', 'lapši []', 'lapš|i [e]', 'la|pši [pše, š]'],
            82 => ['immy|t [ö, t]', 'im|myt [pyö, myt]'],
            83 => ['kar|ši [ne, t]', 'kar|ši [ne/te, t]', 'kar|ši [re, t]', 'kar|ši [re/te, t]', 'kar|ši [re]', 'karši []', 'karš|i [e]'],
            84 => ['itkuvir|ši [ne, t]', 'itkuvir|ši [ne/te, t]', 'itkuvir|ši [re, t]', 'itkuvir|ši [re/te, t]', 'itkuvir|ši [re]', 'itkuvirši []', 'itkuvirš|i [e]'],
            85 => ['hir|ši [ne, t]', 'hir|ši [ne/te, t]', 'hir|ši [re, t]', 'hir|ši [re/te, t]', 'hir|ši [re]', 'hirši []', 'hirš|i [e]'],
            86 => ['paharaiska []', 'paharais|ka [ja]', 'paharais|ka [sa]', 'paharais|ka [va]'],
            87 => ['koivuni []', 'koivun|i [e]', 'koivu|ni [e, ]', 'koivu|ni [se, is]', 'koivu|ni [se, s]'],
            88 => ['čirkkuni []', 'čirkkun|i [e]', 'čirkku|ni [e, ]', 'čirkku|ni [se, is]', 'čirkku|ni [se, s]'],
            89 => ['šyömin|e [ie, et]', 'šyömin|e [ie]', 'šyömin|e [tie, et]', 'šyömi|ne [se, s]'],
            90 => ['tyt|är [täre]', 'tytär [e, ]'],
            91 => ['puha|š [ha, š]', 'puha|š [kše, š]', 'puh|aš [taha, aš]'],
            92 => ['mät|äš [tähä, äš]', 'mätä|š [hä, š]', 'mätä|š [kše, š]'],
            93 => ['hija|š [ha, š]', 'hija|š [kše, š]', 'hi|jaš [taha, jaš]'],
            94 => ['alla|š [ha, š]', 'alla|š [kše, š]', 'al|laš [taha, laš]'],
            95 => ['kinna|š [ha, š]', 'kinna|š [kše, š]', 'kin|naš [taha, naš]'],
            96 => ['parra|š [ha, š]', 'parra|š [kše, š]', 'par|raš [taha, raš]'],
            97 => ['kajeh [e, ]', 'ka|jeh [kehe, jeh]', 'ka|jeh [tehe, jeh]'],
            98 => ['huuvveh [e, ]', 'huuv|veh [pehe, veh]', 'huuv|veh [tehe, veh]', 'huu|vveh [pehe, vveh]'],
            99 => ['kannel [e, ]', 'kan|nel [tele, nel]'],
            100 => ['manner [e, ]', 'man|ner [tere, ner]'],
            101 => ['voij|e [ie, et]', 'voij|e [ie]', 'voi|je [tie, jet]'],
            102 => ['kar|e [ie, et]', 'kar|e [ie]', 'kar|e [tie, et]'],
            103 => ['koš|še [tie, šet]', 'košš|e [ie, et]'],
            104 => ['kaut|e [ie, et]', 'kaut|e [ie]', 'kaut|e [tie, et]'],
            105 => ['jänn|e [ie, et]', 'jän|ne [se, s]', 'jän|ne [tie, net]'],
            106 => ['kaš|še [tie]', 'kašš|e [ie, et]'],
            107 => ['jiät|e [ie, et]', 'jiät|e [ie]', 'jiät|e [tie, et]', 'jiät|e [tie]'],
            108 => ['vuajin [e, ]', 'vuaji|n [me, n]', 'vuaj|in [ma, in]', 'vua|jin [time, jin]'],
            109 => ['kannin [e, ]', 'kanni|n [me, n]', 'kann|in [ma, in]', 'kan|nin [time, nin]'],
            110 => ['jaluš|šin [time, šin]', 'jaluššin [e, ]', 'jalušši|n [me, n]', 'jalušš|in [ma, in]'],
            111 => ['šoitin [e, ]', 'šoiti|n [me, n]', 'šoit|in [ma, in]', 'šoit|in [time, in]'],
            112 => ['hävit|öin [tömä, öin]', 'hävitöin [e, ]', 'hävitöi|n [me, n]', 'hävitö|in [mä, in]'],
            113 => ['issuin [e, ]', 'issui|n [me, n]', 'issu|in [ma, in]', 'is|suin [tuime, suin]'],
            114 => ['kev|ät [yä, ät]'],
            115 => ['haka []', 'ha|ka [ja]', 'ha|ka [va]', 'h|aka [ua]'],
            116 => ['holvata []', 'holva|ta [ja]', 'holva|ta [va]', 'holv|ata [ia]', 'holv|ata [ua]'],
            117 => ['ahjota []', 'ahjo|ta [ja]', 'ahjo|ta [va]', 'ahj|ota [uo]'],
            118 => ['vuo|ši [ne, t]', 'vuo|ši [ne/te, t]', 'vuoši []', 'vuoš|i [e]', 'vu|oši [uvve/ote, ot]'],
            119 => ['kuukau|ši [ne, t]', 'kuukau|ši [ne/te, t]', 'kuukau|ši [ve, t]', 'kuukauši []', 'kuukauš|i [e]'],
            120 => ['joki []', 'jok|i [e]', 'jo|ki [je]', 'jo|ki [ji]', 'jo|ki [ve]', 'jo|ki [vi]'],
            121 => ['hauki []', 'hauk|i [e]', 'hau|ki [je]', 'hau|ki [ji]', 'hau|ki [ve]', 'hau|ki [vi]'],
            122 => ['kapi []', 'kap|i [e]', 'ka|pi [ve]', 'ka|pi [vi]'],
            123 => ['keltatauti []', 'keltataut|i [e]', 'keltatau|ti [je]', 'keltatau|ti [ji]', 'keltatau|ti [ve]', 'keltatau|ti [vi]'],
            124 => ['uu|ši [ne, t]', 'uu|ši [ne/te, t]', 'uu|ši [ve, t]', 'uu|ši [vve, t]', 'uuši []', 'uuš|i [e]'],
            125 => ['anti []', 'ant|i [e]', 'an|ti [je]', 'an|ti [ji]', 'an|ti [ni]', 'an|ti [ve]', 'an|ti [vi]'],
            126 => ['čirškua []', 'čiršk|ua [a, ua]'],
            127 => ['juokšuttua []', 'juokšut|tua [a, tua]'],
            128 => ['pitkä []', 'pit|kä [jä]', 'pit|kä [vä]', 'pit|kä [ä]'],
            129 => ['jatko []', 'jat|ko [jo]', 'jat|ko [o]', 'jat|ko [vo]'],
            130 => ['itku []', 'it|ku [ju]', 'it|ku [u]', 'it|ku [vu]'],
            131 => ['parta []', 'par|ta [a]', 'par|ta [ja]', 'par|ta [ra]', 'par|ta [va]'],
            132 => ['tieto []', 'tie|to [jo]', 'tie|to [vo]', 'ti|eto [ijo]'],
            133 => ['reikä []', 'rei|kä [jä]', 'rei|kä [vä]'],
            134 => ['koti []', 'kot|i [e]', 'ko|ti [je]', 'ko|ti [ji]', 'ko|ti [ve]', 'ko|ti [vi]'],
            135 => ['aika []', 'ai|ka [ja]', 'ai|ka [va]'],
            136 => ['hoito []', 'hoi|to [jo]', 'hoi|to [vo]'],
            137 => ['joiku []', 'joi|ku [ju]', 'joi|ku [vu]'],
            138 => ['lintu []', 'lin|tu [ju]', 'lin|tu [nu]', 'lin|tu [vu]'],
            139 => ['luota []', 'luo|ta [ja]', 'luo|ta [va]', 'lu|ota [uo]', 'lu|ota [uvva]'],
            140 => ['hupa []', 'hu|pa [va]'],
            141 => ['hako []', 'ha|ko [jo]', 'ha|ko [vo]'],
            142 => ['huuto []', 'huu|to [jo]', 'huu|to [vo]', 'huu|to [vvo]'],
            143 => ['šuku []', 'šu|ku [ju]', 'šu|ku [vu]'],
            144 => ['kalanpyytö []', 'kalanpyy|tö [jö]', 'kalanpyy|tö [vvö]', 'kalanpyy|tö [vö]'],
            145 => ['näköni []', 'näkön|i [e]', 'näkö|ni [e, ]', 'näkö|ni [se, is]', 'näkö|ni [se, s]', 'nä|köni [köse, vöis]']
        ];
 
        $this->assertEquals( $expected, $result);        
    }
}
