-- set names utf8; SOURCE /data/all/projects/git/dictorpus/vepsian_2015_insert.sql;

-- mysqldump -uroot -p vepkar --default-character-set=utf8 --max_allowed_packet=1M > vepkar_20160815_v01.sql

-- php artisan migrate:install
-- php artisan migrate:rollback

-- LANG ---------------------------------------
-- php artisan make:migration create_langs_table
-- php artisan make:model 'Models\Dict\Lang'
-- composer dump-autoload

-- ONE COMMAND:
-- php artisan make:model 'Models\Dict\Lang' --migration
-- php artisan make:controller 'Dict\LangController'



-- i18n ---------------------------------------
-- localization and install mcamara: url: http://web-programming.com.ua/lokalizaciya-v-laravel-5-1/
-- see composer.json: "mcamara/laravel-localization": "1.1.*"


--
-- Dumping data for table `langs`
--

/*
INSERT INTO `langs` VALUES (1,'Vepsian','вепсский','vep'),(2,'Russian','русский','ru'),(3,'English','английский','en');
INSERT INTO `langs` VALUES (4,'Karelian','карельский','krl');
INSERT INTO `langs` VALUES (5,'Livvi-Karelian','ливвиковское наречие','olo');
INSERT INTO `langs` VALUES (6,'Ludic','людиковское наречие','lud');
*/




-- PART_OF_SPEECH ---------------------------------------
-- php artisan make:model 'Models\Dict\PartOfSpeech' --migration


--
-- Dumping data for table `part_of_speech`
--

-- INSERT INTO `parts_of_speech` VALUES (1,'Adjective','прилагательное','ADJ'),(2,'Adverb','наречие','ADV'),(3,'Conjunction','союз','CONJ'),(4,'Interjection','междометие','INTER'),(5,'Noun','существительное','N'),(6,'Numeral','числительное','NUM'),(7,'Particle','частица','PART'),(8,'Postposition','послелог','POSTP'),(9,'Preposition','предлог','PREP'),(10,'Pronoun','местоимение','PRON'),(11,'Verb','глагол','V');



-- LEMMA ---------------------------------------
-- php artisan make:model 'Models\Dict\Lemma' --migration
-- php artisan make:controller 'Dict\LemmaController' --resource




-- MEANING and MEANING_TEXT ---------------------------------------
-- php artisan make:model 'Models\Dict\Meaning' --migration
-- php artisan make:controller 'Dict\MeaningController' --resource


-- php artisan make:model 'Models\Dict\MeaningText' --migration
-- php artisan make:controller 'Dict\MeaningTextController' --resource


-- DIALECT ---------------------------------------
-- php artisan make:model 'Models\Dict\Dialect' --migration

INSERT INTO `dialects` VALUES (1, 1, 'Northern Veps','северновепсский диалект', 'veps-north'),(2,1,'Central Veps','средневепсский диалект','veps-centr'),(3,1,'Southern Veps','южновепсский диалект','veps-south'),(4,1,'Eastern dialects','восточные говоры','veps-east'),(5,1,'Western dialects','западные говоры','veps-west');


-- GRAM_CATEGORY ---------------------------------------
-- php artisan make:model 'Models\Dict\GramCategory' --migration

INSERT INTO `gram_categories` VALUES (1, 'case','падеж'),(2,'number','число'),(3,'tense','время');


-- GRAM ---------------------------------------
-- php artisan make:model 'Models\Dict\Gram' --migration


INSERT INTO `grams` VALUES (1, 2, 'sg','singular','ед. ч.','единственное число',1),(2, 2,'pl','plural','мн. ч.','множественное число',2),(3,1,'nom','nominative','','номинатив',1),(4,1,'gen','genitive','','генитив',2),(5,1,'part','partitive','','партитив',3),(6,1,'trans','translative','','транслатив',4),(7,1,'abes','abessive','','абессив',5),(8,1,'ess-inst','essive','','эссив-инструктив',6),(9,1,'ines','inessive','','инессив',7),(10,1,'elat','elative','','элатив',8),(11,1,'ill','illative','','иллатив',9),(12,1,'ades','adessive','','адессив',10),(13,1,'abl','ablative','','аблатив',11),(14,1,'all','allative','','аллатив',12),(15,1,'com','comitative','','комитатив',13),(16,1,'prol','prolative','','пролатив',14),(17,1,'term','terminative','','терминатив',15),(18,1,'appr','approximative','','аппроксиматив',16),(19,1,'adit','aditive','','адитив',17),(20,1,'egr','egressive','','эгрессив',18);

-- GRAMSET ---------------------------------------
-- php artisan make:model 'Models\Dict\Gramset' --migration

-- WORDFORM ---------------------------------------
-- php artisan make:model 'Models\Dict\Wordform' --migration

-- LEMMA_WORDFORM ---------------------------------------
-- php artisan make:migration create_lemma_wordform_table 

