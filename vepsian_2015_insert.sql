-- set names utf8; SOURCE /data/all/projects/git/dictorpus/vepsian_2015_insert.sql;

-- mysqldump -uroot -p vepkar --default-character-set=utf8 --max_allowed_packet=1M > vepkar_20160827_v01.sql

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


