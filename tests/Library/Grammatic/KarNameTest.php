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
