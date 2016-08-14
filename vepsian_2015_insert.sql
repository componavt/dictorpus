-- set names utf8; SOURCE /data/all/projects/git/dictorpus/vepsian_2015_insert.sql;

-- mysqldump -uroot -p vepkar --default-character-set=utf8 --max_allowed_packet=1M > vepkar_2016014_v01.sql

-- php artisan migrate:install

-- LANG ---------------------------------------
-- php artisan make:migration create_langs_table
-- php artisan make:model 'Models\Dict\Lang'
-- composer dump-autoload

-- ONE COMMAND:
-- php artisan make:model 'Models\Dict\Lang' --migration
-- php artisan make:controller 'Dict\LangController'


-- LEMMA ---------------------------------------
-- php artisan make:model 'Models\Dict\Lemma' --migration


-- i18n ---------------------------------------
-- localization and install mcamara: url: http://web-programming.com.ua/lokalizaciya-v-laravel-5-1/
-- see composer.json: "mcamara/laravel-localization": "1.1.*"


--
-- Dumping data for table `langs`
--

LOCK TABLES `langs` WRITE;
/*!40000 ALTER TABLE `langs` DISABLE KEYS */;
INSERT INTO `langs` VALUES (1,'Vepsian','вепсский','vep'),(2,'Russian','русский','ru'),(3,'English','английский','en');
INSERT INTO `langs` VALUES (4,'Karelian','карельский','krl');
INSERT INTO `langs` VALUES (5,'Livvi-Karelian','ливвиковское наречие','olo');
INSERT INTO `langs` VALUES (6,'Ludic','людиковское наречие','lud');

/*!40000 ALTER TABLE `langs` ENABLE KEYS */;
UNLOCK TABLES;
