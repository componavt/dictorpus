<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Long and very long texts
    |--------------------------------------------------------------------------
    |
    |
    */

    // “Veps language corpus”
    
    'about_karelians' => '',
    'about_veps' => '',
    'permission' => '<ul>'
                  . '<li><a href="/docs/permission_bible.pdf">The permission of the Bible Translation Institute to use the Veps publications published by the Institute for the Veps Corps</a></li><br>'
                  . '<li><a href="/docs/permission_bible_karelian.pdf">The permission of the Bible Translation Institute to use publications published by the Institute on Livvik and North Karelian dialects of the Karelian language for the VepKar project</a></li><br>'
                  . '<li><a href="/docs/permission_periodika.pdf">Permission of the Periodica publishing house to use the materials of the Karelian newspaper Oma Mua, the Vep newspaper Kodima, the almanac Taival in Karelian and the Verez tullei almanac in the Veps language for use in the VepKar project</a></li>'
                  . '</ul>',
    'choice_articles' => 'article|articles|articles',
    'choice_texts' => 'text|texts|texts',
    'corpus_means_title' => 'What is "the language corpus"',
    'corpus_means_text' => 'The corpus is an information and reference system based on the collection of texts in electronic form.
                       This linguistic corpus includes texts and dictionaries stored in a database, and a computer program (corpus manager) for searching and processing data.',
    'developers' => 'The site was created by the&nbsp;staff of the <a href="http://www.krc.karelia.ru">Karelian Research Center of the&nbsp;Russian Academy of&nbsp;Sciences</a>',
    'in_numbers_title' => 'VepKar in numbers',
    'in_numbers_text' => 'The Open corpus of Veps and Karelian languages was opened on July 24, 2016. At the moment in the corpus:
                         <div class="in_numbers-b">
                            <span class="in_numbers-n"><a href="/en/stats">:total_lemmas</a></span>
                            <span>:lemmas<br>about words</span>
                         </div>
                         <div class="in_numbers-b">
                            <span class="in_numbers-n"><a href="/en/stats">:total_texts</a></span>
                            <span>:texts on <a href="/en/dict/dialect">:total_dialects</a> dialects</span>
                         </div>',        
    'grants' => '<p>The project was supported by grants:</p>
                 <p>the Russian Foundation for the Humanities, grant No. 15-04-12006, "Veps thesaurus constructing based on word sense disambiguation in multilingual dictionary", 2015-2016.</p>
                 <p>the Russian Foundation for Basic Research, grant No. 18-012-00117, "Problems of construction of text corpus of minor peoples of Russia in the case of Open corpus of Veps and Karelian languages", 2018-2020.</p>',
    'license' => 'You can freely use the materials of our site and share them, most importantly - indicate authorship and a link to our resource.
                  <br><a href="https://creativecommons.org/licenses/by/4.0/">More about the CC BY 4.0 license</a>',
    'our_publications' => '<p><a class="publ-title" href="http://mathem.krc.karelia.ru/publ.php?id=18548&plang=e">LowResourceEval-2019: a shared task on morphological analysis for low-resource languages</a><br>
                    <i>Klyachko E.L., Sorokin A.A., Krizhanovskaya N.B., Krizhanovsky A.A., Ryazanskaya G.M.</i>
                    Computational Linguistics and Intellectual Technologies: papers from the Annual conference “Dialogue” (Moscow, May 29— June 1, 2019) Issue 18 (25). 2019</p>
                    
                    <p><a class="publ-title" href="http://mathem.krc.karelia.ru/publ.php?id=18516&plang=e">Semi-automatic methods for adding words to the dictionary of VepKar corpus based on inflectional rules extracted from Wiktionary</a><br>
                    <i>N.B. Krizhanovskaya, A.A. Krizhanovsky</i>. Proceedings of the international conference "Corpus linguistics ‒ 2019". Saint Petersburg. 2019. Pp. 211-217</p>
                    
                    <p><a class="publ-title" href="http://mathem.krc.karelia.ru/publ.php?id=19646&plang=r">Part of speech and gramset tagging algorithms for unknown words based on morphological dictionaries of the Veps and Karelian languages</a><br>
                    <i>Krizhanovsky A., Krizhanovskaya N., Novak I.</i> Data Analytics and Management in Data Intensive Domains, 13-16 October 2020, Voronezh State University. 2020.</p>
                    ',
    'participants' => '<dl>'
                    . '<dt><a href="http://mathem.krc.karelia.ru/member.php?id=804&plang=e">Andrey Krizhanovsky</a></dt>'
                    . '<dd>PhD, Leading Research Associate in the Laboratory for Information Computer Technologies, Institute of Applied Mathematical Research, KarRC, RAS</dd><br>'
                    . '<dt><a href="http://illhportal.krc.karelia.ru/member.php?id=61&plang=e">Tatyana Boiko</a></dt>'
                    . '<dd>Research Associate in the Linguistics Section, Institute of Linguistics, Literature and History, KarRC, RAS</dd><br>'
                    . '<dt><a href="http://mathem.krc.karelia.ru/member.php?id=22&plang=e">Natalia Krizhanovskaya</a></dt>'
                    . '<dd>Leading Research Engineer in the Laboratory for Information Computer Technologies, Institute of Applied Mathematical Research, KarRC, RAS</dd><br>'
                    . '<dt><a href="http://illhportal.krc.karelia.ru/member.php?id=745&plang=e">Irina Novak</a></dt>'
                    . '<dd>PhD, Junior Research Associate in the Linguistics Section, Institute of Linguistics, Literature and History, KarRC, RAS</dd><br>'
                    . '<dt><a href="http://illhportal.krc.karelia.ru/member.php?id=743&plang=e">Natalia Pellinen</a></dt>'
                    . '<dd>PhD, Junior Research Associate in the Linguistics Section, Institute of Linguistics, Literature and History, KarRC, RAS</dd><br>'
                    . '<dt><a href="http://illhportal.krc.karelia.ru/member.php?id=597&plang=e">Alexandra Rodionova</a></dt>'
                    . '<dd>PhD, Research Associate in the Linguistics Section, Institute of Linguistics, Literature and History, KarRC, RAS</dd><br>'
                    . '<dt><a href="http://illhportal.krc.karelia.ru/member.php?id=99&plang=e">Nina Shibanova</a></dt>'
                    . '<dd>Chief Specialist on Information Technology, Institute of Linguistics, Literature and History, KarRC, RAS</dd><br>'
                    . '<dt><a href="http://mathem.krc.karelia.ru/member.php?id=21&plang=e">Valentina Starkova</a></dt>'
                    . '<dd>Senior Research Engineer in the Laboratory of Natural-Technical System Modelling, Institute of Applied Mathematical Research, KarRC, RAS</dd><br>'
                    . '<dt><a href="http://illhportal.krc.karelia.ru/member.php?id=69&plang=e">Nina Zaitseva</a></dt>'
                    . '<dd>DSc, Leading Research Associate in the Linguistics Section, Institute of Linguistics, Literature and History, KarRC, RAS</dd><br>'
                    . '<dt><a href="http://illhportal.krc.karelia.ru/member.php?id=820&plang=e">Ekaterina Zakharova</a></dt>'
                    . '<dd>PhD, Junior Research Associate in the Linguistics Section, Institute of Linguistics, Literature and History, KarRC, RAS</dd><br>'
                    . '<dt><a href="http://illhportal.krc.karelia.ru/member.php?id=892&plang=e">Olga Zhukova</a></dt>'
                    . '<dd>PhD, Junior Research Associate in the Linguistics Section, Institute of Linguistics, Literature and History, KarRC, RAS</dd><br>'
                    . '</dl>',
    'permission' => '',
    'publications_about' => '<p><a class="publ-title" href="/ru/page/publ_kaunista">Kaunista karjalua šähköresurssilla</a><br>
                    Natalie Pellinen. Oma mua №01 (1541), 2021</p>
                    
                    <p><a class="publ-title" href="/docs/karjalan_sanomat_2021-10-20_12.pdf">Kielten mallit säilyvät korpuksessa</a><br>
                    Marine Tolstyh. Karjalan Sanomat, №40 (16506), 2021</p>
                    ',
    'sources' => '<p><a href="http://resources.krc.karelia.ru/krc/doc/publ2008/onomasiolog_slovar.pdf">Comparative onomasiological dictionary of the Karelian, Veps, and Saami languages</a>. Ju. S. Eliseev, N. G. Zaiceva (eds). 2007. Petrozavodsk: Karelian Research Centre of the Russian Academy of Sciences. Institute of History, Linguistics and Literature. (In Russian)</p>',
    'welcome_text' =>  '<p>Welcome to VepKar — the Open corpus of Veps and Karelian languages 
                        containing dictionaries and corpora of the Baltic-Finnish languages of Karelia peoples.</p>

                        <p>The VepKar project is a continuation of the work 
                        on <a href="http://vepsian.krc.karelia.ru">the Veps language corpus</a>. 
                        Employees of the Karelian Research Centre 
                        of the Russian Academy of Sciences fill in the dictionary and add texts 
                        to the corpus of Veps and Karelian languages. 
                        The corpus of the Karelian language includes the Karelian Proper, 
                        Livvi-Karelian and Ludic Karelian dialects, 
                        which have newly created writing tradition (“младописьменный”, mladopis\'mennyy type of languages).</p> 

                        <p>
                        The developed corpus manager is an open source 
                        project <a href="https://github.com/componavt/dictorpus">Dictorpus</a>.
                        Also the database, including dictionaries and texts 
                        (see <a href="http://dictorpus.krc.karelia.ru/en/dumps">the list of database dumps</a>), 
                        have open license (<a href="https://creativecommons.org/licenses/by/4.0/">CC-BY</a>).</p>
                        
                        <p>
                        The name of the project "Dictorpus" indicates the union of the dictionary (DICTionary) 
                        and the corpus (cORPUS). 
                        The program Dictorpus is designed for teams of linguists working with the languages​ of the world. 
                        At the moment, the program supports and 
                        takes into account the features of Veps and Karelian languages.</p>
                        
                        <p>See <a href="/publ/">the publication list</a>.</p>',
];
