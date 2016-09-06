-- set names utf8; SOURCE /data/all/projects/git/dictorpus/vepsian_2015_insert.sql;

-- mysqldump -uroot -p vepkar --default-character-set=utf8 --max_allowed_packet=1M > vepkar_20160830_v01.sql

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


INSERT INTO `langs` VALUES (1,'Vepsian','вепсский','vep'),(2,'Russian','русский','ru'),(3,'English','английский','en');
INSERT INTO `langs` VALUES (4,'Karelian','карельский','krl');
/*move to dialects
INSERT INTO `langs` VALUES (5,'Livvi-Karelian','ливвиковское наречие','olo');
INSERT INTO `langs` VALUES (6,'Ludic','людиковское наречие','lud');
*/




-- PART_OF_SPEECH ---------------------------------------
-- php artisan make:model 'Models\Dict\PartOfSpeech' --migration
-- php artisan make:controller 'Dict\PartOfSpeechController'

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
-- php artisan make:controller 'Dict\DialectController'

INSERT INTO `dialects` VALUES (1, 1, 'Northern Veps','северновепсский диалект', 'veps-north'),(2,1,'Central Veps','средневепсский диалект','veps-centr'),(3,1,'Southern Veps','южновепсский диалект','veps-south'),(4,1,'Eastern dialects','восточные говоры','veps-east'),(5,1,'Western dialects','западные говоры','veps-west');
INSERT INTO `dialects` VALUES (6,4,'Livvi-Karelian','ливвиковское наречие','olo');
INSERT INTO `dialects` VALUES (7,4,'Ludic','людиковское наречие','lud');
INSERT INTO `dialects` VALUES (8,4,'Karelian Proper','собственно-карельский','krl');


-- GRAM_CATEGORY ---------------------------------------
-- php artisan make:model 'Models\Dict\GramCategory' --migration

INSERT INTO `gram_categories` VALUES (1, 'case','падеж'),(2,'number','число'),(3,'tense','время');


-- GRAM ---------------------------------------
-- php artisan make:model 'Models\Dict\Gram' --migration
-- php artisan make:controller 'Dict\GramController'

INSERT INTO `grams` VALUES (1, 2, 'sg','singular','ед. ч.','единственное число',1),(2, 2,'pl','plural','мн. ч.','множественное число',2),(3,1,'nom','nominative','','номинатив',1),(4,1,'gen','genitive','','генитив',2),(5,1,'part','partitive','','партитив',3),(6,1,'trans','translative','','транслатив',4),(7,1,'abes','abessive','','абессив',5),(8,1,'ess-inst','essive','','эссив-инструктив',6),(9,1,'ines','inessive','','инессив',7),(10,1,'elat','elative','','элатив',8),(11,1,'ill','illative','','иллатив',9),(12,1,'ades','adessive','','адессив',10),(13,1,'abl','ablative','','аблатив',11),(14,1,'all','allative','','аллатив',12),(15,1,'com','comitative','','комитатив',13),(16,1,'prol','prolative','','пролатив',14),(17,1,'term','terminative','','терминатив',15),(18,1,'appr','approximative','','аппроксиматив',16),(19,1,'adit','aditive','','адитив',17),(20,1,'egr','egressive','','эгрессив',18);

-- GRAMSET ---------------------------------------
-- php artisan make:model 'Models\Dict\Gramset' --migration
-- php artisan make:controller 'Dict\GramsetController'
-- mysqldump -p -uroot --skip-extended-insert vepkar gramsets > vepkar_20160826_gramsets.sql

ALTER TABLE gramsets DROP FOREIGN KEY gramsets_pos_id_foreign;
ALTER TABLE gramsets DROP KEY gramsets_pos_id_foreign;
ALTER TABLE gramsets CHANGE pos_id pos_id_debug tinyint(3) unsigned DEFAULT NULL;


SET FOREIGN_KEY_CHECKS=0;
DELETE FROM `gramsets`;
INSERT INTO `gramsets` VALUES (1,5,1,3,NULL,1);
INSERT INTO `gramsets` VALUES (2,5,2,3,NULL,19);
INSERT INTO `gramsets` VALUES (3,5,1,4,NULL,2);
INSERT INTO `gramsets` VALUES (4,5,1,5,NULL,3);
INSERT INTO `gramsets` VALUES (5,5,1,6,NULL,4);
INSERT INTO `gramsets` VALUES (6,5,1,7,NULL,5);
INSERT INTO `gramsets` VALUES (7,5,1,8,NULL,6);
INSERT INTO `gramsets` VALUES (8,5,1,9,NULL,7);
INSERT INTO `gramsets` VALUES (9,5,1,10,NULL,8);
INSERT INTO `gramsets` VALUES (10,5,1,11,NULL,9);
INSERT INTO `gramsets` VALUES (11,5,1,12,NULL,10);
INSERT INTO `gramsets` VALUES (12,5,1,13,NULL,11);
INSERT INTO `gramsets` VALUES (13,5,1,14,NULL,12);
INSERT INTO `gramsets` VALUES (14,5,1,15,NULL,13);
INSERT INTO `gramsets` VALUES (15,5,1,16,NULL,14);
INSERT INTO `gramsets` VALUES (16,5,1,17,NULL,15);
INSERT INTO `gramsets` VALUES (17,5,1,18,NULL,16);
INSERT INTO `gramsets` VALUES (18,5,2,18,NULL,24);
INSERT INTO `gramsets` VALUES (19,5,1,19,NULL,17);
INSERT INTO `gramsets` VALUES (20,5,1,20,NULL,18);
INSERT INTO `gramsets` VALUES (21,11,1,NULL,NULL,1);
INSERT INTO `gramsets` VALUES (22,5,2,5,NULL,21);
INSERT INTO `gramsets` VALUES (23,5,2,9,NULL,22);
INSERT INTO `gramsets` VALUES (24,5,2,4,NULL,20);
INSERT INTO `gramsets` VALUES (25,5,2,12,NULL,23);
SET FOREIGN_KEY_CHECKS=1;

-- GRAMSET_POS ---------------------------------------
-- php artisan make:migration create_gramset_pos_table 

INSERT INTO `gramset_pos` VALUES (1,5),(2,5),(3,5),(4,5),(5,5),(6,5),(7,5),(8,5),(9,5),(10,5),(11,5),(12,5),(13,5),(14,5),(15,5),(16,5),(17,5),(18,5),(19,5),(20,5),(21,11),(22,5),(23,5),(24,5),(25,5),
(1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(22,1),(23,1),(24,1),(25,1),
(1,6),(2,6),(3,6),(4,6),(5,6),(6,6),(7,6),(8,6),(9,6),(10,6),(11,6),(12,6),(13,6),(14,6),(15,6),(16,6),(17,6),(18,6),(19,6),(20,6),(22,6),(23,6),(24,6),(25,6),
(1,10),(2,10),(3,10),(4,10),(5,10),(6,10),(7,10),(8,10),(9,10),(10,10),(11,10),(12,10),(13,10),(14,10),(15,10),(16,10),(17,10),(18,10),(19,10),(20,10),(22,10),(23,10),(24,10),(25,10);


-- WORDFORM ---------------------------------------
-- php artisan make:model 'Models\Dict\Wordform' --migration
-- php artisan make:controller 'Dict\WordformController' --resource

-- LEMMA_WORDFORM ---------------------------------------
-- php artisan make:migration create_lemma_wordform_table 
-- php artisan make:model 'Models\Dict\LemmaWordform'

-- Setting up Postfix and pipe incoming emails to Laravel
-- https://sboersma.nl/blog/setting-up-postfix-and-pipe-incoming-emails-to-laravel
-- sudo apt-get install postfix
-- sudo apt-get install postfix-pcre
-- pecl install mailparse
-- composer require php-mime-mail-parser/php-mime-mail-parser
-- php artisan make:model -m 'Models\EmailMessage'
-- php artisan migrate
-- php artisan make:console ReadEmail

-- POS
ALTER TABLE `parts_of_speech` ADD `category` tinyInt NOT NULL default 3;
UPDATE `parts_of_speech` SET category=1 WHERE id in (1,2,4,5,11);
UPDATE `parts_of_speech` SET category=2 WHERE id in (3,6,7,8,9,10);
UPDATE `parts_of_speech` SET code='NOUN' WHERE id=5;
UPDATE `parts_of_speech` SET code='INTJ' WHERE id=4;
UPDATE `parts_of_speech` SET code='VERB' WHERE id=11;
INSERT INTO `parts_of_speech` VALUES 
(12,'Auxiliary verb', 'вспомогательный глагол', 'AUX',2),(13,'Determiner', 'детерминатив', 'DET',2),
(14,'Proper noun', 'имя собственное', 'PROPN',1),(15,'Subordinating conjunction', 'подчинительный союз', 'SCONJ',2),
(16,'Punctuation', 'пунктуация', 'PUNCT',3),(17,'Symbol', 'символ', 'SYM',3),(18,'Other', 'другое', 'X',3);


Creating Policies ================================== https://www.laravel.com/docs/5.3/authorization#gates
====================================================
php artisan make:policy 'Dict\LemmaPolicy' --model='Models\Dict\Lemma'
php artisan make:policy 'Dict\LemmaPolicy'

-- CORPUS ---------------------------------------
-- php artisan make:model 'Models\Corpus\Corpus' --migration

INSERT INTO `corpuses` VALUES (1,1,'Dialectal texts','диалектные тексты'),(2,1,'Biblical texts (translated)','библейские тексты (переводные)'),(3,1,'New-writing language','младописьменный подкорпус'),(4,1,'Laments and lamentations','подкорпус вепсских причитаний'),(5,1,'Tales','подкорпус вепсских сказок');

-- GENRE ---------------------------------------
-- php artisan make:model 'Models\Corpus\Genre' --migration
-- mysqldump  --skip-extended-insert -p -uroot vepsian label >vepsian_label.sql

INSERT INTO `genres` VALUES (6,'Bridal laments','свадебные причитания',11);
INSERT INTO `genres` VALUES (7,'Burial and funeral lamentations','похоронные и поминальные причитания',12);
INSERT INTO `genres` VALUES (8,'Journalistic texts','публицистические тексты',21);
INSERT INTO `genres` VALUES (9,'Literary texts','художественные тексты',22);
INSERT INTO `genres` VALUES (10,'Texts for children','тексты для детей',23);

-- SOURCE ---------------------------------------
-- php artisan make:model 'Models\Corpus\Source' --migration
-- php artisan make:controller 'Corpus\SourceController'

-- REGION ---------------------------------------
-- php artisan make:model 'Models\Corpus\Region' --migration
INSERT INTO `regions` VALUES (1,'Vologda Oblast','Вологодская обл.');
INSERT INTO `regions` VALUES (2,'Republic of Karelia','Республика Карелия');
INSERT INTO `regions` VALUES (3,'Leningrad Oblast','Ленинградская обл.');

-- DISTRICT ---------------------------------------
-- php artisan make:model 'Models\Corpus\District' --migration
INSERT INTO `districts` VALUES (1,1,'Vytegorsky District','Вытегорский р-н');
INSERT INTO `districts` VALUES (4,1,'Babayevsky District','Бабаевский р-н');
INSERT INTO `districts` VALUES (5,3,'Volosovsky District','Волосовский р-н');
INSERT INTO `districts` VALUES (6,3,'Podporozhsky District','Подпорожский р-н');
INSERT INTO `districts` VALUES (7,3,'Vinnytsia District','Винницкий р-н');
INSERT INTO `districts` VALUES (8,3,'Boksitogorsky District','Бокситогорский р-н');
INSERT INTO `districts` VALUES (9,3,'Tikhvinsky District','Тихвинский р-н');
INSERT INTO `districts` VALUES (10,2,'Prionezhsky District','Прионежский р-н');
INSERT INTO `districts` VALUES (11,3,'Kapshinsky District','Капшинский р-н');

/*
INSERT INTO `place_region` VALUES (1,'Вытегорский р-н. Вологодская обл.');
INSERT INTO `place_region` VALUES (2,'Республика Карелия');
INSERT INTO `place_region` VALUES (4,'Бабаевский р-н, Вологодская обл.');
INSERT INTO `place_region` VALUES (5,'Волосовский р-н, Ленинградская обл.');
INSERT INTO `place_region` VALUES (6,'Подпорожский р-н, Ленинградская обл.');
INSERT INTO `place_region` VALUES (7,'Винницкий р-н, Ленинградская обл.');
INSERT INTO `place_region` VALUES (8,'Бокситогорский р-н, Ленинградская обл.');
INSERT INTO `place_region` VALUES (9,'Тихвинский р-н, Ленинградская обл.');
INSERT INTO `place_region` VALUES (10,'Прионежский р-н, Республика Карелия');
INSERT INTO `place_region` VALUES (11,'Капшинский р-н, Ленинградская обл.');
*/

-- PLACE ---------------------------------------
-- php artisan make:model 'Models\Corpus\Place' --migration
-- php artisan make:controller 'Corpus\PlaceController'

-- PLACENAME ---------------------------------------
-- php artisan make:model 'Models\Corpus\PlaceName' --migration

-- INFORMANT ---------------------------------------
-- php artisan make:model 'Models\Corpus\Informant' --migration
-- php artisan make:controller 'Corpus\InformantController' --resource

-- EVENT ---------------------------------------
-- php artisan make:model 'Models\Corpus\Event' --migration
-- mysqldump -p -uroot --skip-extended-insert vepsian event > vepsian_event.sql
INSERT INTO `events` VALUES (21,4,11,NULL);
INSERT INTO `events` VALUES (22,5,11,NULL);
INSERT INTO `events` VALUES (23,6,11,NULL);
INSERT INTO `events` VALUES (24,7,11,NULL);
INSERT INTO `events` VALUES (68,29,37,1965);
INSERT INTO `events` VALUES (69,30,38,1958);
INSERT INTO `events` VALUES (70,32,38,1955);
INSERT INTO `events` VALUES (71,33,38,1964);
INSERT INTO `events` VALUES (72,34,38,1964);
INSERT INTO `events` VALUES (77,18,53,1981);
INSERT INTO `events` VALUES (78,7,11,2013);
INSERT INTO `events` VALUES (84,41,38,1964);
INSERT INTO `events` VALUES (87,44,38,NULL);
INSERT INTO `events` VALUES (88,44,38,1958);
INSERT INTO `events` VALUES (89,45,38,1964);
INSERT INTO `events` VALUES (93,49,47,1937);
INSERT INTO `events` VALUES (97,51,54,1937);
INSERT INTO `events` VALUES (98,52,54,1937);
INSERT INTO `events` VALUES (102,56,64,1937);
INSERT INTO `events` VALUES (113,66,35,1957);
INSERT INTO `events` VALUES (116,68,36,1957);
INSERT INTO `events` VALUES (118,70,NULL,1968);
INSERT INTO `events` VALUES (119,18,53,1980);
INSERT INTO `events` VALUES (120,35,53,1956);
INSERT INTO `events` VALUES (121,47,53,1956);
INSERT INTO `events` VALUES (122,46,47,1947);
INSERT INTO `events` VALUES (123,48,47,1947);
INSERT INTO `events` VALUES (124,50,54,1956);
INSERT INTO `events` VALUES (125,53,52,1938);
INSERT INTO `events` VALUES (126,54,NULL,1957);
INSERT INTO `events` VALUES (127,55,64,1938);
INSERT INTO `events` VALUES (128,57,11,1980);
INSERT INTO `events` VALUES (129,58,11,1981);
INSERT INTO `events` VALUES (130,59,11,1981);
INSERT INTO `events` VALUES (131,60,11,1981);
INSERT INTO `events` VALUES (132,9,26,1961);
INSERT INTO `events` VALUES (133,62,23,1948);
INSERT INTO `events` VALUES (134,61,23,1948);
INSERT INTO `events` VALUES (135,63,39,1958);
INSERT INTO `events` VALUES (136,64,38,1958);
INSERT INTO `events` VALUES (137,44,38,1958);
INSERT INTO `events` VALUES (138,65,38,1958);
INSERT INTO `events` VALUES (139,66,35,1981);
INSERT INTO `events` VALUES (140,66,35,1957);
INSERT INTO `events` VALUES (141,66,35,1958);
INSERT INTO `events` VALUES (142,67,35,1962);
INSERT INTO `events` VALUES (143,8,26,1961);
INSERT INTO `events` VALUES (144,10,26,1965);
INSERT INTO `events` VALUES (145,11,26,1965);
INSERT INTO `events` VALUES (146,12,12,1965);
INSERT INTO `events` VALUES (147,13,12,1965);
INSERT INTO `events` VALUES (148,31,11,1961);
INSERT INTO `events` VALUES (149,14,11,1961);
INSERT INTO `events` VALUES (150,16,10,1961);
INSERT INTO `events` VALUES (151,17,9,1965);
INSERT INTO `events` VALUES (152,17,9,1965);
INSERT INTO `events` VALUES (153,19,42,1965);
INSERT INTO `events` VALUES (154,20,42,1965);
INSERT INTO `events` VALUES (155,21,43,1965);
INSERT INTO `events` VALUES (156,22,44,1965);
INSERT INTO `events` VALUES (157,23,30,1965);
INSERT INTO `events` VALUES (158,24,31,1962);
INSERT INTO `events` VALUES (159,25,32,1962);
INSERT INTO `events` VALUES (160,26,33,1962);
INSERT INTO `events` VALUES (161,27,34,1962);
INSERT INTO `events` VALUES (162,28,23,1948);
INSERT INTO `events` VALUES (164,37,23,1948);
INSERT INTO `events` VALUES (165,38,23,1948);
INSERT INTO `events` VALUES (166,39,23,1948);
INSERT INTO `events` VALUES (167,40,23,1948);
INSERT INTO `events` VALUES (168,71,35,1962);
INSERT INTO `events` VALUES (169,72,35,1965);
INSERT INTO `events` VALUES (170,57,11,NULL);
INSERT INTO `events` VALUES (171,66,35,NULL);
INSERT INTO `events` VALUES (172,66,35,1962);
INSERT INTO `events` VALUES (173,66,35,1963);
INSERT INTO `events` VALUES (174,10,35,1962);
INSERT INTO `events` VALUES (175,73,36,1963);
INSERT INTO `events` VALUES (176,74,36,1962);
INSERT INTO `events` VALUES (177,74,36,1963);
INSERT INTO `events` VALUES (178,75,36,1963);
INSERT INTO `events` VALUES (179,76,5,1963);
INSERT INTO `events` VALUES (180,77,5,1963);
INSERT INTO `events` VALUES (181,78,38,1964);
INSERT INTO `events` VALUES (182,79,38,1964);
INSERT INTO `events` VALUES (183,80,38,1964);
INSERT INTO `events` VALUES (184,82,38,1964);
INSERT INTO `events` VALUES (185,83,38,1964);
INSERT INTO `events` VALUES (186,64,38,1957);
INSERT INTO `events` VALUES (187,10,39,1957);
INSERT INTO `events` VALUES (188,84,40,1966);
INSERT INTO `events` VALUES (189,85,40,1966);
INSERT INTO `events` VALUES (190,86,60,1966);
INSERT INTO `events` VALUES (191,10,NULL,NULL);
INSERT INTO `events` VALUES (192,72,35,1964);
INSERT INTO `events` VALUES (193,10,35,NULL);
INSERT INTO `events` VALUES (194,10,35,1963);
INSERT INTO `events` VALUES (196,87,66,1970);
INSERT INTO `events` VALUES (197,88,67,1970);
INSERT INTO `events` VALUES (198,89,12,1968);
INSERT INTO `events` VALUES (199,90,NULL,1968);
INSERT INTO `events` VALUES (200,91,NULL,1968);
INSERT INTO `events` VALUES (201,92,NULL,1961);
INSERT INTO `events` VALUES (202,93,NULL,1961);
INSERT INTO `events` VALUES (203,94,NULL,1968);
INSERT INTO `events` VALUES (204,58,NULL,1980);
INSERT INTO `events` VALUES (205,95,NULL,1989);
INSERT INTO `events` VALUES (206,96,70,1989);
INSERT INTO `events` VALUES (207,69,71,1962);
INSERT INTO `events` VALUES (208,70,32,1968);
INSERT INTO `events` VALUES (209,1,71,1970);
INSERT INTO `events` VALUES (210,90,23,1968);
INSERT INTO `events` VALUES (211,91,68,1968);
INSERT INTO `events` VALUES (212,92,26,1961);
INSERT INTO `events` VALUES (213,93,11,1961);
INSERT INTO `events` VALUES (214,94,23,1968);
INSERT INTO `events` VALUES (215,58,11,1980);
INSERT INTO `events` VALUES (216,95,11,1989);
INSERT INTO `events` VALUES (217,97,72,1969);
INSERT INTO `events` VALUES (218,98,38,1964);
INSERT INTO `events` VALUES (219,99,74,1969);
INSERT INTO `events` VALUES (220,100,53,1962);
INSERT INTO `events` VALUES (221,66,76,1957);
INSERT INTO `events` VALUES (222,101,77,1970);
INSERT INTO `events` VALUES (223,102,78,1970);
INSERT INTO `events` VALUES (224,103,71,1962);
INSERT INTO `events` VALUES (225,102,1,1970);
INSERT INTO `events` VALUES (226,104,68,1968);
INSERT INTO `events` VALUES (227,105,11,1961);
INSERT INTO `events` VALUES (228,106,11,1968);
INSERT INTO `events` VALUES (229,107,11,1989);
INSERT INTO `events` VALUES (230,108,40,1985);
INSERT INTO `events` VALUES (231,109,53,1993);
INSERT INTO `events` VALUES (232,NULL,54,1983);
INSERT INTO `events` VALUES (233,87,78,1970);
INSERT INTO `events` VALUES (234,101,NULL,NULL);
INSERT INTO `events` VALUES (235,104,NULL,NULL);
INSERT INTO `events` VALUES (236,70,NULL,NULL);
INSERT INTO `events` VALUES (237,98,NULL,NULL);
INSERT INTO `events` VALUES (238,87,78,NULL);
INSERT INTO `events` VALUES (239,85,40,NULL);
INSERT INTO `events` VALUES (240,97,75,NULL);
INSERT INTO `events` VALUES (241,88,67,NULL);
INSERT INTO `events` VALUES (242,95,NULL,NULL);
INSERT INTO `events` VALUES (243,100,NULL,NULL);
INSERT INTO `events` VALUES (244,106,NULL,NULL);
INSERT INTO `events` VALUES (245,66,NULL,NULL);
INSERT INTO `events` VALUES (246,102,NULL,NULL);
INSERT INTO `events` VALUES (247,110,78,NULL);
INSERT INTO `events` VALUES (248,1,71,NULL);
INSERT INTO `events` VALUES (249,93,NULL,NULL);
INSERT INTO `events` VALUES (250,69,71,NULL);
INSERT INTO `events` VALUES (251,91,NULL,NULL);
INSERT INTO `events` VALUES (252,94,NULL,NULL);
INSERT INTO `events` VALUES (253,89,12,NULL);
INSERT INTO `events` VALUES (254,92,NULL,NULL);
INSERT INTO `events` VALUES (255,103,NULL,NULL);
INSERT INTO `events` VALUES (256,90,NULL,NULL);
INSERT INTO `events` VALUES (257,108,NULL,NULL);
INSERT INTO `events` VALUES (258,107,NULL,NULL);
INSERT INTO `events` VALUES (259,105,NULL,NULL);
INSERT INTO `events` VALUES (260,96,70,NULL);
INSERT INTO `events` VALUES (261,58,NULL,NULL);
INSERT INTO `events` VALUES (262,109,NULL,NULL);
INSERT INTO `events` VALUES (263,99,NULL,NULL);

-- RECORDER ---------------------------------------
-- php artisan make:model 'Models\Corpus\Recorder' --migration
-- php artisan make:controller 'Corpus\RecorderController' --resource
-- php artisan make:migration create_event_recorder_table
INSERT INTO `event_recorder` VALUES (68,6);
INSERT INTO `event_recorder` VALUES (68,9);
INSERT INTO `event_recorder` VALUES (69,4);
INSERT INTO `event_recorder` VALUES (70,4);
INSERT INTO `event_recorder` VALUES (71,6);
INSERT INTO `event_recorder` VALUES (71,9);
INSERT INTO `event_recorder` VALUES (72,6);
INSERT INTO `event_recorder` VALUES (72,9);
INSERT INTO `event_recorder` VALUES (77,1);
INSERT INTO `event_recorder` VALUES (77,13);
INSERT INTO `event_recorder` VALUES (77,14);
INSERT INTO `event_recorder` VALUES (84,6);
INSERT INTO `event_recorder` VALUES (84,9);
INSERT INTO `event_recorder` VALUES (87,6);
INSERT INTO `event_recorder` VALUES (87,9);
INSERT INTO `event_recorder` VALUES (88,4);
INSERT INTO `event_recorder` VALUES (89,6);
INSERT INTO `event_recorder` VALUES (89,9);
INSERT INTO `event_recorder` VALUES (93,15);
INSERT INTO `event_recorder` VALUES (93,16);
INSERT INTO `event_recorder` VALUES (97,15);
INSERT INTO `event_recorder` VALUES (97,16);
INSERT INTO `event_recorder` VALUES (98,15);
INSERT INTO `event_recorder` VALUES (98,16);
INSERT INTO `event_recorder` VALUES (102,15);
INSERT INTO `event_recorder` VALUES (102,16);
INSERT INTO `event_recorder` VALUES (113,17);
INSERT INTO `event_recorder` VALUES (113,18);
INSERT INTO `event_recorder` VALUES (116,4);
INSERT INTO `event_recorder` VALUES (116,17);
INSERT INTO `event_recorder` VALUES (116,18);
INSERT INTO `event_recorder` VALUES (117,12);
INSERT INTO `event_recorder` VALUES (118,6);
INSERT INTO `event_recorder` VALUES (118,9);
INSERT INTO `event_recorder` VALUES (119,1);
INSERT INTO `event_recorder` VALUES (120,3);
INSERT INTO `event_recorder` VALUES (121,4);
INSERT INTO `event_recorder` VALUES (122,5);
INSERT INTO `event_recorder` VALUES (123,5);
INSERT INTO `event_recorder` VALUES (124,6);
INSERT INTO `event_recorder` VALUES (125,7);
INSERT INTO `event_recorder` VALUES (126,3);
INSERT INTO `event_recorder` VALUES (127,8);
INSERT INTO `event_recorder` VALUES (128,1);
INSERT INTO `event_recorder` VALUES (129,1);
INSERT INTO `event_recorder` VALUES (130,1);
INSERT INTO `event_recorder` VALUES (131,1);
INSERT INTO `event_recorder` VALUES (132,9);
INSERT INTO `event_recorder` VALUES (133,10);
INSERT INTO `event_recorder` VALUES (134,10);
INSERT INTO `event_recorder` VALUES (135,4);
INSERT INTO `event_recorder` VALUES (136,4);
INSERT INTO `event_recorder` VALUES (137,4);
INSERT INTO `event_recorder` VALUES (138,4);
INSERT INTO `event_recorder` VALUES (139,1);
INSERT INTO `event_recorder` VALUES (140,11);
INSERT INTO `event_recorder` VALUES (141,6);
INSERT INTO `event_recorder` VALUES (142,12);
INSERT INTO `event_recorder` VALUES (143,9);
INSERT INTO `event_recorder` VALUES (144,3);
INSERT INTO `event_recorder` VALUES (145,3);
INSERT INTO `event_recorder` VALUES (146,3);
INSERT INTO `event_recorder` VALUES (147,3);
INSERT INTO `event_recorder` VALUES (148,9);
INSERT INTO `event_recorder` VALUES (149,9);
INSERT INTO `event_recorder` VALUES (150,9);
INSERT INTO `event_recorder` VALUES (151,6);
INSERT INTO `event_recorder` VALUES (151,9);
INSERT INTO `event_recorder` VALUES (152,6);
INSERT INTO `event_recorder` VALUES (153,6);
INSERT INTO `event_recorder` VALUES (153,9);
INSERT INTO `event_recorder` VALUES (154,6);
INSERT INTO `event_recorder` VALUES (154,9);
INSERT INTO `event_recorder` VALUES (155,6);
INSERT INTO `event_recorder` VALUES (155,9);
INSERT INTO `event_recorder` VALUES (156,6);
INSERT INTO `event_recorder` VALUES (156,9);
INSERT INTO `event_recorder` VALUES (157,9);
INSERT INTO `event_recorder` VALUES (158,12);
INSERT INTO `event_recorder` VALUES (159,12);
INSERT INTO `event_recorder` VALUES (160,12);
INSERT INTO `event_recorder` VALUES (161,12);
INSERT INTO `event_recorder` VALUES (162,10);
INSERT INTO `event_recorder` VALUES (163,10);
INSERT INTO `event_recorder` VALUES (164,10);
INSERT INTO `event_recorder` VALUES (165,10);
INSERT INTO `event_recorder` VALUES (166,10);
INSERT INTO `event_recorder` VALUES (167,10);
INSERT INTO `event_recorder` VALUES (168,6);
INSERT INTO `event_recorder` VALUES (168,9);
INSERT INTO `event_recorder` VALUES (169,6);
INSERT INTO `event_recorder` VALUES (169,9);
INSERT INTO `event_recorder` VALUES (171,4);
INSERT INTO `event_recorder` VALUES (171,6);
INSERT INTO `event_recorder` VALUES (172,6);
INSERT INTO `event_recorder` VALUES (172,9);
INSERT INTO `event_recorder` VALUES (173,6);
INSERT INTO `event_recorder` VALUES (173,9);
INSERT INTO `event_recorder` VALUES (174,12);
INSERT INTO `event_recorder` VALUES (175,6);
INSERT INTO `event_recorder` VALUES (175,9);
INSERT INTO `event_recorder` VALUES (176,6);
INSERT INTO `event_recorder` VALUES (176,9);
INSERT INTO `event_recorder` VALUES (177,6);
INSERT INTO `event_recorder` VALUES (177,9);
INSERT INTO `event_recorder` VALUES (178,6);
INSERT INTO `event_recorder` VALUES (178,9);
INSERT INTO `event_recorder` VALUES (179,6);
INSERT INTO `event_recorder` VALUES (179,9);
INSERT INTO `event_recorder` VALUES (180,6);
INSERT INTO `event_recorder` VALUES (180,9);
INSERT INTO `event_recorder` VALUES (181,6);
INSERT INTO `event_recorder` VALUES (181,9);
INSERT INTO `event_recorder` VALUES (182,6);
INSERT INTO `event_recorder` VALUES (182,9);
INSERT INTO `event_recorder` VALUES (183,6);
INSERT INTO `event_recorder` VALUES (183,9);
INSERT INTO `event_recorder` VALUES (184,6);
INSERT INTO `event_recorder` VALUES (184,9);
INSERT INTO `event_recorder` VALUES (185,6);
INSERT INTO `event_recorder` VALUES (185,9);
INSERT INTO `event_recorder` VALUES (186,4);
INSERT INTO `event_recorder` VALUES (187,4);
INSERT INTO `event_recorder` VALUES (188,6);
INSERT INTO `event_recorder` VALUES (188,9);
INSERT INTO `event_recorder` VALUES (189,6);
INSERT INTO `event_recorder` VALUES (189,9);
INSERT INTO `event_recorder` VALUES (190,6);
INSERT INTO `event_recorder` VALUES (190,9);
INSERT INTO `event_recorder` VALUES (192,6);
INSERT INTO `event_recorder` VALUES (193,6);
INSERT INTO `event_recorder` VALUES (193,9);
INSERT INTO `event_recorder` VALUES (194,6);
INSERT INTO `event_recorder` VALUES (194,9);
INSERT INTO `event_recorder` VALUES (195,9);
INSERT INTO `event_recorder` VALUES (196,9);
INSERT INTO `event_recorder` VALUES (196,19);
INSERT INTO `event_recorder` VALUES (197,9);
INSERT INTO `event_recorder` VALUES (197,19);
INSERT INTO `event_recorder` VALUES (198,6);
INSERT INTO `event_recorder` VALUES (198,9);
INSERT INTO `event_recorder` VALUES (199,6);
INSERT INTO `event_recorder` VALUES (199,9);
INSERT INTO `event_recorder` VALUES (200,6);
INSERT INTO `event_recorder` VALUES (200,9);
INSERT INTO `event_recorder` VALUES (201,9);
INSERT INTO `event_recorder` VALUES (202,9);
INSERT INTO `event_recorder` VALUES (203,9);
INSERT INTO `event_recorder` VALUES (204,1);
INSERT INTO `event_recorder` VALUES (204,20);
INSERT INTO `event_recorder` VALUES (205,21);
INSERT INTO `event_recorder` VALUES (205,22);
INSERT INTO `event_recorder` VALUES (205,23);
INSERT INTO `event_recorder` VALUES (206,21);
INSERT INTO `event_recorder` VALUES (207,12);
INSERT INTO `event_recorder` VALUES (208,6);
INSERT INTO `event_recorder` VALUES (208,9);
INSERT INTO `event_recorder` VALUES (209,9);
INSERT INTO `event_recorder` VALUES (209,19);
INSERT INTO `event_recorder` VALUES (210,6);
INSERT INTO `event_recorder` VALUES (210,9);
INSERT INTO `event_recorder` VALUES (211,6);
INSERT INTO `event_recorder` VALUES (211,9);
INSERT INTO `event_recorder` VALUES (212,9);
INSERT INTO `event_recorder` VALUES (213,9);
INSERT INTO `event_recorder` VALUES (214,9);
INSERT INTO `event_recorder` VALUES (215,1);
INSERT INTO `event_recorder` VALUES (215,20);
INSERT INTO `event_recorder` VALUES (216,21);
INSERT INTO `event_recorder` VALUES (216,24);
INSERT INTO `event_recorder` VALUES (217,6);
INSERT INTO `event_recorder` VALUES (217,9);
INSERT INTO `event_recorder` VALUES (218,6);
INSERT INTO `event_recorder` VALUES (218,9);
INSERT INTO `event_recorder` VALUES (218,25);
INSERT INTO `event_recorder` VALUES (219,6);
INSERT INTO `event_recorder` VALUES (219,9);
INSERT INTO `event_recorder` VALUES (220,3);
INSERT INTO `event_recorder` VALUES (221,4);
INSERT INTO `event_recorder` VALUES (221,6);
INSERT INTO `event_recorder` VALUES (222,9);
INSERT INTO `event_recorder` VALUES (222,19);
INSERT INTO `event_recorder` VALUES (223,9);
INSERT INTO `event_recorder` VALUES (223,19);
INSERT INTO `event_recorder` VALUES (224,12);
INSERT INTO `event_recorder` VALUES (225,9);
INSERT INTO `event_recorder` VALUES (225,19);
INSERT INTO `event_recorder` VALUES (226,6);
INSERT INTO `event_recorder` VALUES (226,9);
INSERT INTO `event_recorder` VALUES (227,9);
INSERT INTO `event_recorder` VALUES (228,6);
INSERT INTO `event_recorder` VALUES (228,9);
INSERT INTO `event_recorder` VALUES (229,21);
INSERT INTO `event_recorder` VALUES (229,24);
INSERT INTO `event_recorder` VALUES (230,19);
INSERT INTO `event_recorder` VALUES (230,22);
INSERT INTO `event_recorder` VALUES (231,3);
INSERT INTO `event_recorder` VALUES (232,26);
INSERT INTO `event_recorder` VALUES (233,9);
INSERT INTO `event_recorder` VALUES (233,19);

-- TRANSTEXT ---------------------------------------
-- php artisan make:model 'Models\Corpus\Transtext' --migration

-- TEXT ---------------------------------------
-- php artisan make:model 'Models\Corpus\Text' --migration
-- php artisan make:controller 'Corpus\TextController' --resource

-- different sources of text and transtext
-- select t1.id, t1.source_id, t2.source_id from text as t1, text as t2, text_pair where text_pair.text1_id=t1.id and text_pair.text2_id=t2.id and t1.source_id<>t2.source_id;

-- php artisan make:migration create_dialect_text_table

-- transtexts have labels too
-- select text.id, text.title, label.label_ru from text, label, text_label where text_label.text_id=text.id and text_label.label_id=label.id and text.lang_id=2;

-- compare labels of text and labels of transtext
-- select t1.id, t1.source_id, t2.source_id from text as t1, text as t2, text_pair where text_pair.text1_id=t1.id and text_pair.text2_id=t2.id and t1.source_id<>t2.source_id;

-- php artisan make:migration create_genre_text_table
