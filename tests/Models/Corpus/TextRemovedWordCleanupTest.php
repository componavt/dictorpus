<?php

namespace Tests;

use App\Traits\Modify\TextModify;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class TextRemovedWordCleanupTest extends \PHPUnit_Framework_TestCase
{
    private $capsule;
    private $previousResolver;
    private $previousFacadeApplication;
    private $previousFacadeInstances;
    private $previousLibxmlMode;
    private $globalsInstalled = false;

    /**
     * Создаёт отдельную SQLite-БД в памяти без загрузки приложения.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('В контейнере отсутствует PDO SQLite.');
        }

        $this->previousResolver = Model::getConnectionResolver();

        $this->previousFacadeApplication = DB::getFacadeApplication();

        $instancesProperty = new \ReflectionProperty(
            \Illuminate\Support\Facades\Facade::class,
            'resolvedInstance'
        );
        $instancesProperty->setAccessible(true);

        $this->previousFacadeInstances = $instancesProperty->getValue();

        $this->previousLibxmlMode = libxml_use_internal_errors();

        $this->capsule = new Capsule();

        $this->capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $manager = $this->capsule->getDatabaseManager();

        $container = $this->capsule->getContainer();
        $container->instance('db', $manager);

        $this->globalsInstalled = true;

        DB::setFacadeApplication($container);
        DB::clearResolvedInstance('db');

        Model::setConnectionResolver($manager);

        $connection = $manager->connection();

        $this->assertSame(
            'sqlite',
            $connection->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME)
        );

        $schema = $connection->getSchemaBuilder();

        $schema->create('words', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('text_id');
            $table->integer('sentence_id');
            $table->integer('s_id');
            $table->integer('w_id');
            $table->string('word');
            $table->integer('checked')->default(0);
            $table->integer('word_number');
        });

        $schema->create('meaning_text', function (Blueprint $table) {
            $table->integer('meaning_id');
            $table->integer('text_id');
            $table->integer('s_id');
            $table->integer('word_id');
            $table->integer('relevance')->default(1);
            $table->integer('w_id');
        });

        $schema->create('text_wordform', function (Blueprint $table) {
            $table->integer('text_id');
            $table->integer('w_id');
            $table->integer('wordform_id');
            $table->integer('gramset_id');
            $table->integer('relevance')->default(1);
            $table->integer('word_id');

            $table->primary([
                'text_id',
                'w_id',
                'wordform_id',
                'gramset_id',
            ]);
        });

        $schema->create('sentences', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('text_id');
            $table->integer('s_id');
            $table->text('text_xml');
        });

        $schema->create('texts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lang_id');
        });

        DB::table('texts')->insert([
            'id' => 1880,
            'lang_id' => 6,
        ]);
    }

    /**
     * Восстанавливает соединения, контейнер и кеш фасадов.
     *
     * @return void
     */
    protected function tearDown()
    {
        try {
            if ($this->globalsInstalled) {
                try {
                    $this->capsule->getDatabaseManager()->disconnect();
                } finally {
                    if ($this->previousResolver === null) {
                        Model::unsetConnectionResolver();
                    } else {
                        Model::setConnectionResolver(
                            $this->previousResolver
                        );
                    }

                    DB::setFacadeApplication(
                        $this->previousFacadeApplication
                    );

                    $instancesProperty = new \ReflectionProperty(
                        \Illuminate\Support\Facades\Facade::class,
                        'resolvedInstance'
                    );
                    $instancesProperty->setAccessible(true);
                    $instancesProperty->setValue(
                        null,
                        $this->previousFacadeInstances
                    );

                    libxml_use_internal_errors(
                        $this->previousLibxmlMode
                    );
                }
            }
        } finally {
            parent::tearDown();
        }
    }

    /**
     * Штатное удаление позиции должно очищать её аннотации.
     *
     * @return void
     */
    public function testRemovedWordWithExistingWordsRowIsCleaned()
    {
        $this->runCleanupScenario(true);
    }

    /**
     * Регрессионное требование: учитывать исчезнувшую позицию
     * старого XML, даже если её строка words уже отсутствует.
     *
     * @return void
     */
    public function testRemovedWordMissingFromWordsIsAlsoCleaned()
    {
        $this->runCleanupScenario(false);
    }

    /**
     * Новый XML не должен забирать w_id другого предложения.
     *
     * @return void
     */
    public function testNewWordIdOwnedByAnotherSentenceIsRejected()
    {
        $this->seedWordIdConflictFixture();

        $this->assertWordIdConflictLeavesTablesUnchanged(
            '<s id="16"><w id="137">other</w></s>',
            'Word ids conflict with another sentence. '
                . 'No changes were committed.'
        );
    }

    /**
     * Удаляемый w_id не должен очищаться, если он используется
     * строкой words другого предложения.
     *
     * Проверяет откат удаления исходного слова и его значений.
     *
     * @return void
     */
    public function testRemovedWordIdUsedByAnotherSentenceRollsBack()
    {
        $this->seedWordIdConflictFixture();

        // Повреждённое состояние: один w_id используется
        // двумя предложениями одного текста.
        DB::table('words')->insert([
            'id' => 103,
            'text_id' => 1880,
            'sentence_id' => 51469,
            's_id' => 18,
            'w_id' => 136,
            'word' => 'foreign',
            'checked' => 0,
            'word_number' => 1,
        ]);

        $this->assertWordIdConflictLeavesTablesUnchanged(
            '<s id="16"></s>',
            'Removed word ids are still used by another sentence. '
                . 'No changes were committed.'
        );
    }

    /**
     * Пересечение старого w_id с XML другого предложения
     * должно приводить к отказу даже при отсутствии words.
     *
     * Проверяет сохранность осиротевших аннотаций:
     * при неоднозначной принадлежности удалять их нельзя.
     *
     * @return void
     */
    public function testOldWordIdSharedWithAnotherSentenceXmlIsRejected()
    {
        $this->seedWordIdConflictFixture();

        // Удаляем строку слова, но оставляем его аннотации.
        // Проверка конфликтов только через words теперь
        // не сможет обнаружить использование w136.
        DB::table('words')
            ->where('text_id', 1880)
            ->where('w_id', 136)
            ->delete();

        DB::table('sentences')->insert([
            'id' => 51467,
            'text_id' => 1880,
            's_id' => 16,
            'text_xml' => '<s id="16">'
                . '<w id="136">ilmad</w>'
                . '</s>',
        ]);

        // То же w136 встречается в XML другого предложения.
        // Отдельной строки words для него здесь тоже нет.
        DB::table('sentences')->insert([
            'id' => 51468,
            'text_id' => 1880,
            's_id' => 17,
            'text_xml' => '<s id="17">'
                . '<w id="137">other</w>'
                . '<w id="136">foreign</w>'
                . '</s>',
        ]);

        $this->assertWordIdConflictLeavesTablesUnchanged(
            '<s id="16"></s>',
            'Old word ids occur in another sentence XML. '
                . 'No changes were committed.'
        );
    }

    /**
     * Совпадение w_id в другом тексте не должно блокировать
     * очистку или приводить к изменению чужих данных.
     *
     * @return void
     */
    public function testSameWordIdInAnotherTextDoesNotBlockCleanup()
    {
        $this->seedWordIdConflictFixture();

        // В текущем тексте оставляем аннотации без строки words:
        // принадлежность исчезнувшей позиции берётся из старого XML.
        DB::table('words')
            ->where('text_id', 1880)
            ->where('w_id', 136)
            ->delete();

        DB::table('sentences')->insert([
            'id' => 51467,
            'text_id' => 1880,
            's_id' => 16,
            'text_xml' => '<s id="16">'
                . '<w id="136">ilmad</w>'
                . '</s>',
        ]);

        DB::table('sentences')->insert([
            'id' => 51468,
            'text_id' => 1880,
            's_id' => 17,
            'text_xml' => '<s id="17">'
                . '<w id="137">other</w>'
                . '</s>',
        ]);

        // В другом тексте совпадают и w_id, и s_id.
        // Разделять эти позиции должен именно text_id.
        DB::table('sentences')->insert([
            'id' => 60001,
            'text_id' => 1999,
            's_id' => 16,
            'text_xml' => '<s id="16">'
                . '<w id="136">foreign</w>'
                . '</s>',
        ]);

        DB::table('words')->insert([
            'id' => 201,
            'text_id' => 1999,
            'sentence_id' => 60001,
            's_id' => 16,
            'w_id' => 136,
            'word' => 'foreign',
            'checked' => 1,
            'word_number' => 1,
        ]);

        // Совпадают также идентификаторы словоформы и грамсета:
        // чужую связь должен защищать фильтр по text_id.
        DB::table('text_wordform')->insert([
            'text_id' => 1999,
            'w_id' => 136,
            'wordform_id' => 2312958,
            'gramset_id' => 2,
            'relevance' => 0,
            'word_id' => 201,
        ]);

        DB::table('meaning_text')->insert([
            'meaning_id' => 554,
            'text_id' => 1999,
            's_id' => 16,
            'word_id' => 201,
            'relevance' => 0,
            'w_id' => 136,
        ]);

        $before = $this->snapshotWordIdConflictTables();

        // Разрешены только удаления аннотаций и возможной строки
        // words для исчезнувшей позиции текущего текста.
        $expected = $before;

        foreach (['words', 'meaning_text', 'text_wordform'] as $table) {
            $expected[$table] = array_values(array_filter(
                $before[$table],
                function ($row) {
                    return !(
                        (int) $row['text_id'] === 1880
                        && (int) $row['w_id'] === 136
                    );
                }
            ));
        }

        $subject = new TextRemovedWordCleanupSubject();

        $sentence = (object) [
            'id' => 51467,
            's_id' => 16,
            'text_xml' => '<s id="16"><w id="136">ilmad</w></s>',
        ];

        $result = $subject->updateMeaningAndWordformText(
            $sentence,
            '<s id="16"></s>'
        );

        $this->assertNull(
            $result,
            'Совпадение w_id в другом тексте не должно блокировать очистку.'
        );

        $this->assertEquals(
            0,
            DB::table('words')
                ->where('text_id', 1880)
                ->where('w_id', 136)
                ->count()
        );

        $this->assertEquals(
            0,
            DB::table('text_wordform')
                ->where('text_id', 1880)
                ->where('w_id', 136)
                ->count(),
            'Осиротевшие словоформные связи текущего текста должны удалиться.'
        );

        $this->assertEquals(
            0,
            DB::table('meaning_text')
                ->where('text_id', 1880)
                ->where('w_id', 136)
                ->count(),
            'Смысловые связи исчезнувшей позиции должны удалиться.'
        );

        $this->assertSame(
            $expected,
            $this->snapshotWordIdConflictTables(),
            'Допустима только очистка исчезнувшей позиции текущего текста; '
                . 'остальные данные должны сохраниться без изменений.'
        );
    }

    /**
     * Новый w_id не должен забирать позицию из XML другого
     * предложения, даже если соответствующая words отсутствует.
     *
     * @return void
     */
    public function testNewWordIdOwnedOnlyByAnotherSentenceXmlIsRejected()
    {
        $this->seedWordIdConflictFixture();

        // У другого предложения остаются XML и аннотации,
        // но строка words для w137 отсутствует.
        DB::table('words')
            ->where('text_id', 1880)
            ->where('w_id', 137)
            ->delete();

        DB::table('sentences')->insert([
            'id' => 51467,
            'text_id' => 1880,
            's_id' => 16,
            'text_xml' => '<s id="16">'
                . '<w id="136">ilmad</w>'
                . '</s>',
        ]);

        DB::table('sentences')->insert([
            'id' => 51468,
            'text_id' => 1880,
            's_id' => 17,
            'text_xml' => '<s id="17">'
                . '<w id="137">other</w>'
                . '</s>',
        ]);

        $this->assertWordIdConflictLeavesTablesUnchanged(
            '<s id="16"><w id="137">replacement</w></s>',
            'New word ids occur in another sentence XML. '
                . 'No changes were committed.',
            new TextNewWordIdConflictSubject()
        );
    }

    /**
     * Слияние двух XML-слов должно создать одно новое вхождение,
     * его смысловую и словоформную аннотации.
     *
     * Первый интеграционный сценарий выполняется без переноса
     * прежних оценок: without_check = true.
     *
     * @return void
     */
    public function testTwoWordsAreMergedWithFreshAnnotations()
    {
        // Статический кеш не должен переносить словоформы
        // между изолированной БД теста и другими сценариями.
        $cacheProperty = new \ReflectionProperty(
            \App\Models\Corpus\Text::class,
            'wordformsCache'
        );
        $cacheProperty->setAccessible(true);

        $previousCache = $cacheProperty->getValue();

        try {
            $cacheProperty->setValue(null, []);

            $this->runRealWordMergeScenario();
        } finally {
            $cacheProperty->setValue(null, $previousCache);
        }
    }

    /**
     * Обычное сохранение должно пересоздать объединённое слово
     * и удалить разборы исходных частей.
     *
     * Выполняет настоящее чтение сохранённых оценок.
     *
     * @return void
     */
    public function testTwoWordsAreMergedInNormalSaveMode()
    {
        $cacheProperty = new \ReflectionProperty(
            \App\Models\Corpus\Text::class,
            'wordformsCache'
        );
        $cacheProperty->setAccessible(true);

        $previousCache = $cacheProperty->getValue();

        try {
            $cacheProperty->setValue(null, []);

            $this->runRealWordMergeScenario(false);
        } finally {
            $cacheProperty->setValue(null, $previousCache);
        }
    }

    /**
     * Создаёт минимальные таблицы для настоящего словарного поиска.
     *
     * @return void
     */
    private function createWordMergeDictionarySchema()
    {
        $schema = $this->capsule
            ->getDatabaseManager()
            ->connection()
            ->getSchemaBuilder();

        $schema->create('lemmas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lang_id');
            $table->string('lemma');
            $table->string('lemma_for_search');
        });

        $schema->create('meanings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lemma_id');
        });

        $schema->create('wordforms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('wordform');
            $table->string('wordform_for_search');
        });

        $schema->create('lemma_wordform', function (Blueprint $table) {
            $table->integer('lemma_id');
            $table->integer('wordform_id');
            $table->integer('lang_id');
            $table->string('wordform_for_search');
            $table->integer('gramset_id');
        });
    }

    /**
     * Выполняет настоящий путь пересоздания слов и аннотаций.
     *
     * Словарные данные искусственные и принадлежат только тесту.
     *
     * @param bool $withoutCheck Не переносить прежние оценки.
     * @return void
     */
    private function runRealWordMergeScenario($withoutCheck = true)
    {
        $this->createWordMergeDictionarySchema();
        $this->seedWordIdConflictFixture();

        // Фикстура уже содержит:
        // id101 / w136 / ilmad — текущее предложение;
        // id102 / w137 / other — соседнее предложение.
        DB::table('words')->insert([
            'id' => 100,
            'text_id' => 1880,
            'sentence_id' => 51467,
            's_id' => 16,
            'w_id' => 135,
            'word' => 'mua',
            'checked' => 0,
            'word_number' => 1,
        ]);

        DB::table('words')
            ->where('id', 101)
            ->update(['word_number' => 2]);

        DB::table('text_wordform')->insert([
            'text_id' => 1880,
            'w_id' => 135,
            'wordform_id' => 8000,
            'gramset_id' => 1,
            'relevance' => 2,
            'word_id' => 100,
        ]);

        DB::table('meaning_text')->insert([
            'meaning_id' => 553,
            'text_id' => 1880,
            's_id' => 16,
            'word_id' => 100,
            'relevance' => 2,
            'w_id' => 135,
        ]);

        $oldXml = '<s id="16">'
            . '<w id="135">mua</w> '
            . '<w id="136">ilmad</w>'
            . '</s>';

        $newXml = '<s id="16">'
            . '<w id="135">muailmad</w>'
            . '</s>';

        DB::table('sentences')->insert([
            'id' => 51467,
            'text_id' => 1880,
            's_id' => 16,
            'text_xml' => $oldXml,
        ]);

        DB::table('sentences')->insert([
            'id' => 51468,
            'text_id' => 1880,
            's_id' => 17,
            'text_xml' => '<s id="17">'
                . '<w id="137">other</w>'
                . '</s>',
        ]);

        // Искусственный словарь для объединённого слова.
        DB::table('lemmas')->insert([
            'id' => 9000,
            'lang_id' => 6,
            'lemma' => 'muailmad',
            'lemma_for_search' => 'muailmad',
        ]);

        DB::table('meanings')->insert([
            'id' => 9001,
            'lemma_id' => 9000,
        ]);

        DB::table('wordforms')->insert([
            'id' => 9002,
            'wordform' => 'muailmad',
            'wordform_for_search' => 'muailmad',
        ]);

        DB::table('lemma_wordform')->insert([
            'lemma_id' => 9000,
            'wordform_id' => 9002,
            'lang_id' => 6,
            'wordform_for_search' => 'muailmad',
            'gramset_id' => 2,
        ]);

        $before = $this->snapshotWordIdConflictTables();

        // Настоящая модель Text, а не тестовый носитель трейта.
        $text = \App\Models\Corpus\Text::find(1880);

        $this->assertInstanceOf(
            \App\Models\Corpus\Text::class,
            $text
        );

        $sentence = (object) [
            'id' => 51467,
            's_id' => 16,
            'text_xml' => $oldXml,
        ];

        // Один сценарий проверяем в двух режимах:
        // с чтением прежних оценок и без их переноса.
        $result = $text->updateMeaningAndWordformText(
            $sentence,
            $newXml,
            $withoutCheck
        );

        $this->assertNull($result);

        $this->assertEquals(
            1,
            DB::table('words')
                ->where('text_id', 1880)
                ->where('s_id', 16)
                ->count(),
            'После слияния в предложении должно остаться одно слово.'
        );

        $mergedRow = DB::table('words')
            ->where('text_id', 1880)
            ->where('w_id', 135)
            ->first();

        $this->assertNotNull($mergedRow);

        $mergedRow = (array) $mergedRow;
        $newWordId = (int) $mergedRow['id'];

        $this->assertFalse(
            in_array($newWordId, [100, 101, 102], true),
            'Объединённое слово должно получить новый words.id.'
        );

        $this->assertSame('muailmad', $mergedRow['word']);
        $this->assertEquals(51467, $mergedRow['sentence_id']);
        $this->assertEquals(16, $mergedRow['s_id']);
        $this->assertEquals(1, $mergedRow['word_number']);

        $this->assertEquals(
            0,
            DB::table('words')
                ->whereIn('id', [100, 101])
                ->count(),
            'Исходные строки words должны быть заменены.'
        );

        foreach (['words', 'meaning_text', 'text_wordform'] as $table) {
            $this->assertEquals(
                0,
                DB::table($table)
                    ->where('text_id', 1880)
                    ->where('w_id', 136)
                    ->count(),
                'В ' . $table
                    . ' остались данные исчезнувшей позиции w136.'
            );
        }

        $this->assertEquals(
            1,
            DB::table('meaning_text')
                ->where('text_id', 1880)
                ->where('w_id', 135)
                ->count(),
            'Должно быть создано ровно одно новое значение.'
        );

        $this->assertEquals(
            1,
            DB::table('meaning_text')
                ->where('text_id', 1880)
                ->where('s_id', 16)
                ->where('w_id', 135)
                ->where('word_id', $newWordId)
                ->where('meaning_id', 9001)
                ->where('relevance', 1)
                ->count(),
            'Новое значение должно ссылаться на новое вхождение.'
        );

        $this->assertEquals(
            1,
            DB::table('text_wordform')
                ->where('text_id', 1880)
                ->where('w_id', 135)
                ->count(),
            'Должен быть создан ровно один новый разбор.'
        );

        $this->assertEquals(
            1,
            DB::table('text_wordform')
                ->where('text_id', 1880)
                ->where('w_id', 135)
                ->where('word_id', $newWordId)
                ->where('wordform_id', 9002)
                ->where('gramset_id', 2)
                ->where('relevance', 1)
                ->count(),
            'Новый разбор должен ссылаться на новое вхождение.'
        );

        // Исключаем только изменяемые позиции из обоих снимков.
        // Всё остальное должно совпасть, включая sentences.
        $after = $this->snapshotWordIdConflictTables();

        foreach (['words', 'meaning_text', 'text_wordform'] as $table) {
            foreach (['before', 'after'] as $snapshotName) {
                ${$snapshotName}[$table] = array_values(array_filter(
                    ${$snapshotName}[$table],
                    function ($row) {
                        return !(
                            (int) $row['text_id'] === 1880
                            && in_array(
                                (int) $row['w_id'],
                                [135, 136],
                                true
                            )
                        );
                    }
                ));
            }
        }

        $this->assertSame(
            $before,
            $after,
            'Соседнее предложение и остальные данные изменились.'
        );
    }

    /**
     * Неизменившееся слово должно сохранить оценки после
     * пересоздания своей строки words.
     *
     * Новые альтернативы при сохранившемся выбранном варианте
     * должны получить relevance = 0.
     *
     * @return void
     */
    public function testUnchangedWordKeepsRelevancesAfterRecreation()
    {
        $cacheProperty = new \ReflectionProperty(
            \App\Models\Corpus\Text::class,
            'wordformsCache'
        );
        $cacheProperty->setAccessible(true);

        $previousCache = $cacheProperty->getValue();

        try {
            $cacheProperty->setValue(null, []);

            $this->runUnchangedWordRelevanceScenario();
        } finally {
            $cacheProperty->setValue(null, $previousCache);
        }
    }

    /**
     * Непроверяемый XML другого предложения должен блокировать изменения.
     *
     * @dataProvider unsafeOtherSentenceXmlProvider
     * @param string $otherXml XML другого предложения.
     * @param string $expectedMessage Ожидаемая причина отказа.
     * @return void
     */
    public function testUnsafeOtherSentenceXmlLeavesTablesUnchanged($otherXml, $expectedMessage)
    {
        $this->seedWordIdConflictFixture();

        DB::table('sentences')->insert([
            'id' => 51467,
            'text_id' => 1880,
            's_id' => 16,
            'text_xml' => '<s id="16"><w id="136">ilmad</w></s>',
        ]);

        DB::table('sentences')->insert([
            'id' => 51468,
            'text_id' => 1880,
            's_id' => 17,
            'text_xml' => $otherXml,
        ]);

        $this->assertWordIdConflictLeavesTablesUnchanged(
            '<s id="16"></s>',
            $expectedMessage
        );
    }

    /**
     * Устаревший снимок предложения не должен использоваться
     * для пересоздания слов и аннотаций.
     *
     * @return void
     */
    public function testStaleSentenceXmlIsRejectedWithoutChanges()
    {
        $this->seedWordIdConflictFixture();

        // Имитируем состояние после другого сохранения:
        // в БД уже другое написание текущего слова.
        DB::table('words')
            ->where('id', 101)
            ->update(['word' => 'changed']);

        DB::table('sentences')->insert([
            'id' => 51467,
            'text_id' => 1880,
            's_id' => 16,
            'text_xml' => '<s id="16"><w id="136">changed</w></s>',
        ]);

        DB::table('sentences')->insert([
            'id' => 51468,
            'text_id' => 1880,
            's_id' => 17,
            'text_xml' => '<s id="17"><w id="137">other</w></s>',
        ]);

        // Помощник передаёт прежний снимок:
        // <s id="16"><w id="136">ilmad</w></s>.
        // Его использование теперь должно быть отклонено.
        $this->assertWordIdConflictLeavesTablesUnchanged(
            '<s id="16"></s>',
            'Sentence XML changed before annotation update. No changes were committed.'
        );
    }

    /**
     * Возвращает случаи, в которых принадлежность позиций нельзя проверить.
     *
     * @return array
     */
    public function unsafeOtherSentenceXmlProvider()
    {
        return [
            'empty XML' => [
                '',
                'Cannot safely inspect another sentence XML. No changes were committed.',
            ],
            'DOCTYPE' => [
                '<!DOCTYPE s><s id="17"><w id="137">other</w></s>',
                'Cannot safely inspect another sentence XML. No changes were committed.',
            ],
            'ENTITY declaration' => [
                '<!ENTITY example "other"><s id="17"><w id="137">other</w></s>',
                'Cannot safely inspect another sentence XML. No changes were committed.',
            ],
            'malformed XML' => [
                '<s id="17"><w id="137">other</s>',
                'Cannot parse another sentence XML. No changes were committed.',
            ],
            'invalid word id' => [
                '<s id="17"><w id="bad">other</w></s>',
                'Invalid word id in another sentence XML. No changes were committed.',
            ],
        ];
    }

    /**
     * Проверяет перенос оценок неизменившегося слова.
     *
     * При withMerge одновременно сливает две другие позиции
     * того же предложения.
     *
     * @param bool $withMerge Добавить слияние соседних слов.
     * @return void
     */
    private function runUnchangedWordRelevanceScenario($withMerge = false)
    {
        $this->createWordMergeDictionarySchema();
        $this->seedWordIdConflictFixture();

        $xml = '<s id="16"><w id="136">ilmad</w></s>';
        $newXml = $xml;

        if ($withMerge) {
            $this->seedCombinedWordMergeFixture();

            $xml = '<s id="16">'
                . '<w id="134">mua</w> '
                . '<w id="135">ilma</w> '
                . '<w id="136">ilmad</w>'
                . '</s>';

            $newXml = '<s id="16">'
                . '<w id="134">muailma</w> '
                . '<w id="136">ilmad</w>'
                . '</s>';
        }

        DB::table('sentences')->insert([
            'id' => 51467,
            'text_id' => 1880,
            's_id' => 16,
            'text_xml' => $xml,
        ]);

        DB::table('sentences')->insert([
            'id' => 51468,
            'text_id' => 1880,
            's_id' => 17,
            'text_xml' => '<s id="17">'
                . '<w id="137">other</w>'
                . '</s>',
        ]);

        DB::table('lemmas')->insert([
            'id' => 9100,
            'lang_id' => 6,
            'lemma' => 'ilmad',
            'lemma_for_search' => 'ilmad',
        ]);

        // Значение 554 уже выбрано в исходной фикстуре.
        // Значение 556 отклонено, а 557 — новая альтернатива.
        foreach ([554, 556, 557] as $meaningId) {
            DB::table('meanings')->insert([
                'id' => $meaningId,
                'lemma_id' => 9100,
            ]);
        }

        DB::table('meaning_text')->insert([
            'meaning_id' => 556,
            'text_id' => 1880,
            's_id' => 16,
            'word_id' => 101,
            'relevance' => 0,
            'w_id' => 136,
        ]);

        DB::table('wordforms')->insert([
            'id' => 2312958,
            'wordform' => 'ilmad',
            'wordform_for_search' => 'ilmad',
        ]);

        // В исходной фикстуре:
        // грамсет 2 выбран, 4 и 57 отклонены.
        // Грамсет 58 доступен в словаре, но ещё не аннотирован.
        foreach ([2, 4, 57, 58] as $gramsetId) {
            DB::table('lemma_wordform')->insert([
                'lemma_id' => 9100,
                'wordform_id' => 2312958,
                'lang_id' => 6,
                'wordform_for_search' => 'ilmad',
                'gramset_id' => $gramsetId,
            ]);
        }

        // Проверяем, что новые альтернативы действительно
        // отсутствуют до пересоздания.
        $this->assertEquals(
            0,
            DB::table('meaning_text')
                ->where('text_id', 1880)
                ->where('w_id', 136)
                ->where('meaning_id', 557)
                ->count()
        );

        $this->assertEquals(
            0,
            DB::table('text_wordform')
                ->where('text_id', 1880)
                ->where('w_id', 136)
                ->where('wordform_id', 2312958)
                ->where('gramset_id', 58)
                ->count()
        );

        $before = $this->snapshotWordIdConflictTables();

        $text = \App\Models\Corpus\Text::find(1880);

        $this->assertInstanceOf(
            \App\Models\Corpus\Text::class,
            $text
        );

        $sentence = (object) [
            'id' => 51467,
            's_id' => 16,
            'text_xml' => $xml,
        ];

        // Обычный режим: пересоздаём предложение с переносом оценок.
        $result = $text->updateMeaningAndWordformText(
            $sentence,
            $newXml,
            false
        );

        $this->assertNull($result);

        $this->assertEquals(
            $withMerge ? 2 : 1,
            DB::table('words')
                ->where('text_id', 1880)
                ->where('s_id', 16)
                ->count()
        );

        $newWordRow = DB::table('words')
            ->where('text_id', 1880)
            ->where('w_id', 136)
            ->first();

        $this->assertNotNull($newWordRow);

        $newWord = (array) $newWordRow;
        $newWordId = (int) $newWord['id'];

        $this->assertNotEquals(
            101,
            $newWordId,
            'Тест должен проверить перенос на новый words.id.'
        );

        $this->assertEquals(
            0,
            DB::table('words')->where('id', 101)->count(),
            'Исходная строка words должна быть удалена.'
        );

        $this->assertSame('ilmad', $newWord['word']);
        $this->assertEquals(1880, $newWord['text_id']);
        $this->assertEquals(51467, $newWord['sentence_id']);
        $this->assertEquals(16, $newWord['s_id']);
        $this->assertEquals(136, $newWord['w_id']);
        $this->assertEquals(
            $withMerge ? 2 : 1,
            $newWord['word_number']
        );

        // Проверяем полные наборы альтернатив:
        // ключ — идентификатор значения,
        // данные — новый word_id и сохранённая оценка.
        $actualMeanings = [];

        foreach (
            DB::table('meaning_text')
                ->where('text_id', 1880)
                ->where('w_id', 136)
                ->get() as $row
        ) {
            $row = (array) $row;
            $meaningId = (int) $row['meaning_id'];

            $this->assertArrayNotHasKey(
                $meaningId,
                $actualMeanings,
                'Обнаружена повторная смысловая аннотация.'
            );

            $this->assertEquals(16, $row['s_id']);

            $actualMeanings[$meaningId] = [
                'word_id' => (int) $row['word_id'],
                'relevance' => (int) $row['relevance'],
            ];
        }

        ksort($actualMeanings);

        $this->assertSame([
            554 => [
                'word_id' => $newWordId,
                'relevance' => 2,
            ],
            556 => [
                'word_id' => $newWordId,
                'relevance' => 0,
            ],
            557 => [
                'word_id' => $newWordId,
                'relevance' => 0,
            ],
        ], $actualMeanings);

        // Ключ — пара wordform_id + gramset_id.
        $actualWordforms = [];

        foreach (
            DB::table('text_wordform')
                ->where('text_id', 1880)
                ->where('w_id', 136)
                ->get() as $row
        ) {
            $row = (array) $row;

            $key = (int) $row['wordform_id']
                . '_' . (int) $row['gramset_id'];

            $this->assertArrayNotHasKey(
                $key,
                $actualWordforms,
                'Обнаружена повторная словоформная аннотация.'
            );

            $actualWordforms[$key] = [
                'word_id' => (int) $row['word_id'],
                'relevance' => (int) $row['relevance'],
            ];
        }

        ksort($actualWordforms);

        $this->assertSame([
            '2312958_2' => [
                'word_id' => $newWordId,
                'relevance' => 2,
            ],
            '2312958_4' => [
                'word_id' => $newWordId,
                'relevance' => 0,
            ],
            '2312958_57' => [
                'word_id' => $newWordId,
                'relevance' => 0,
            ],
            '2312958_58' => [
                'word_id' => $newWordId,
                'relevance' => 0,
            ],
        ], $actualWordforms);

        if ($withMerge) {
            $this->assertCombinedWordMergeResult();
        }

        // Сравниваем данные за пределами проверяемых позиций:
        // они должны остаться неизменными.
        $after = $this->snapshotWordIdConflictTables();

        foreach (['words', 'meaning_text', 'text_wordform'] as $table) {
            $before[$table] = array_values(array_filter(
                $before[$table],
                function ($row) use ($withMerge) {
                    return !(
                        (int) $row['text_id'] === 1880
                        && in_array(
                            (int) $row['w_id'],
                            $withMerge ? [134, 135, 136] : [136],
                            true
                        )
                    );
                }
            ));

            $after[$table] = array_values(array_filter(
                $after[$table],
                function ($row) use ($withMerge) {
                    return !(
                        (int) $row['text_id'] === 1880
                        && in_array(
                            (int) $row['w_id'],
                            $withMerge ? [134, 135, 136] : [136],
                            true
                        )
                    );
                }
            ));
        }

        $this->assertSame(
            $before,
            $after,
            'Данные за пределами пересоздаваемой позиции изменились.'
        );
    }

    /**
     * Слияние двух слов не должно нарушать перенос оценок
     * неизменившегося слова того же предложения.
     *
     * @return void
     */
    public function testMergeKeepsUnchangedWordRelevances()
    {
        $cacheProperty = new \ReflectionProperty(
            \App\Models\Corpus\Text::class,
            'wordformsCache'
        );
        $cacheProperty->setAccessible(true);

        $previousCache = $cacheProperty->getValue();

        try {
            $cacheProperty->setValue(null, []);

            $this->runUnchangedWordRelevanceScenario(true);
        } finally {
            $cacheProperty->setValue(null, $previousCache);
        }
    }

    /**
     * Создаёт исходное слово, соседнее слово и их аннотации.
     *
     * @return void
     */
    private function seedWordIdConflictFixture()
    {
        DB::table('words')->insert([
            'id' => 101,
            'text_id' => 1880,
            'sentence_id' => 51467,
            's_id' => 16,
            'w_id' => 136,
            'word' => 'ilmad',
            'checked' => 0,
            'word_number' => 1,
        ]);

        DB::table('words')->insert([
            'id' => 102,
            'text_id' => 1880,
            'sentence_id' => 51468,
            's_id' => 17,
            'w_id' => 137,
            'word' => 'other',
            'checked' => 0,
            'word_number' => 1,
        ]);

        foreach ([2 => 2, 4 => 0, 57 => 0] as $gramsetId => $relevance) {
            DB::table('text_wordform')->insert([
                'text_id' => 1880,
                'w_id' => 136,
                'wordform_id' => 2312958,
                'gramset_id' => $gramsetId,
                'relevance' => $relevance,
                'word_id' => 101,
            ]);
        }

        DB::table('text_wordform')->insert([
            'text_id' => 1880,
            'w_id' => 137,
            'wordform_id' => 999,
            'gramset_id' => 1,
            'relevance' => 2,
            'word_id' => 102,
        ]);

        DB::table('meaning_text')->insert([
            'meaning_id' => 554,
            'text_id' => 1880,
            's_id' => 16,
            'word_id' => 101,
            'relevance' => 2,
            'w_id' => 136,
        ]);

        DB::table('meaning_text')->insert([
            'meaning_id' => 555,
            'text_id' => 1880,
            's_id' => 17,
            'word_id' => 102,
            'relevance' => 2,
            'w_id' => 137,
        ]);
    }

    /**
     * Проверяет ожидаемый отказ и полную неизменность таблиц.
     *
     * @param string $newXml Новый XML предложения.
     * @param string $expectedMessage Ожидаемое сообщение исключения.
     * @param TextRemovedWordCleanupSubject|null $subject Тестовый объект.
     * @return void
     */
    private function assertWordIdConflictLeavesTablesUnchanged(
        $newXml,
        $expectedMessage,
        $subject = null
    ) {
        if (!DB::table('sentences')->where('id', 51467)->exists()) {
            DB::table('sentences')->insert([
                'id' => 51467,
                'text_id' => 1880,
                's_id' => 16,
                'text_xml' => '<s id="16"><w id="136">ilmad</w></s>',
            ]);
        }

        $before = $this->snapshotWordIdConflictTables();

        if ($subject === null) {
            $subject = new TextRemovedWordCleanupSubject();
        }

        $sentence = (object) [
            'id' => 51467,
            's_id' => 16,
            'text_xml' => '<s id="16"><w id="136">ilmad</w></s>',
        ];

        $caught = null;

        try {
            $subject->updateMeaningAndWordformText(
                $sentence,
                $newXml
            );
        } catch (\RuntimeException $exception) {
            $caught = $exception;
        }

        $this->assertInstanceOf(
            \RuntimeException::class,
            $caught,
            'Конфликт w_id должен приводить к отказу.'
        );

        $this->assertSame(
            $expectedMessage,
            $caught->getMessage(),
            'Причиной отказа должен быть ожидаемый конфликт w_id.'
        );

        $after = $this->snapshotWordIdConflictTables();

        foreach ($before as $table => $rows) {
            $this->assertSame(
                $rows,
                $after[$table],
                'Таблица ' . $table
                    . ' изменилась после отказа из-за конфликта w_id.'
            );
        }
    }

    /**
     * Снимает содержимое таблиц с устойчивым порядком строк.
     *
     * Сохраняет все столбцы, включая word_id и relevance.
     *
     * @return array
     */
    private function snapshotWordIdConflictTables()
    {
        $orders = [
            'sentences' => [
                'id',
            ],
            'words' => [
                'id',
            ],
            'meaning_text' => [
                'text_id',
                's_id',
                'w_id',
                'meaning_id',
                'word_id',
            ],
            'text_wordform' => [
                'text_id',
                'w_id',
                'wordform_id',
                'gramset_id',
            ],
        ];

        $snapshot = [];

        foreach ($orders as $table => $columns) {
            $query = DB::table($table);

            foreach ($columns as $column) {
                $query->orderBy($column);
            }

            $snapshot[$table] = [];

            foreach ($query->get() as $row) {
                $row = (array) $row;
                ksort($row);

                $snapshot[$table][] = $row;
            }
        }

        return $snapshot;
    }

    /**
     * Выполняет настоящий метод очистки на изолированной БД.
     *
     * Новое предложение не содержит слов: словарный поиск
     * и updateWordformText() в этом узком сценарии не вызываются.
     *
     * @param bool $withOldWord Создавать ли исходную строку words.
     * @return void
     */
    private function runCleanupScenario($withOldWord)
    {
        if ($withOldWord) {
            DB::table('words')->insert([
                'id' => 101,
                'text_id' => 1880,
                'sentence_id' => 51467,
                's_id' => 16,
                'w_id' => 136,
                'word' => 'ilmad',
                'checked' => 0,
                'word_number' => 1,
            ]);
        }

        DB::table('words')->insert([
            'id' => 102,
            'text_id' => 1880,
            'sentence_id' => 51468,
            's_id' => 17,
            'w_id' => 137,
            'word' => 'other',
            'checked' => 0,
            'word_number' => 1,
        ]);

        foreach ([2, 4, 57] as $gramsetId) {
            DB::table('text_wordform')->insert([
                'text_id' => 1880,
                'w_id' => 136,
                'wordform_id' => 2312958,
                'gramset_id' => $gramsetId,
                'relevance' => 1,
                'word_id' => 101,
            ]);
        }

        DB::table('text_wordform')->insert([
            'text_id' => 1880,
            'w_id' => 137,
            'wordform_id' => 999,
            'gramset_id' => 1,
            'relevance' => 2,
            'word_id' => 102,
        ]);

        DB::table('meaning_text')->insert([
            'meaning_id' => 555,
            'text_id' => 1880,
            's_id' => 17,
            'word_id' => 102,
            'relevance' => 2,
            'w_id' => 137,
        ]);

        $neighborWordBefore = DB::table('words')
            ->where('id', 102)
            ->first();

        $neighborLinksBefore = DB::table('text_wordform')
            ->where('text_id', 1880)
            ->where('w_id', 137)
            ->get();

        $neighborMeaningsBefore = DB::table('meaning_text')
            ->where('text_id', 1880)
            ->where('w_id', 137)
            ->get();

        DB::table('sentences')->insert([
            'id' => 51467,
            'text_id' => 1880,
            's_id' => 16,
            'text_xml' => '<s id="16"><w id="136">ilmad</w></s>',
        ]);

        $subject = new TextRemovedWordCleanupSubject();

        $sentence = (object) [
            'id' => 51467,
            's_id' => 16,
            'text_xml' => '<s id="16"><w id="136">ilmad</w></s>',
        ];

        $result = $subject->updateMeaningAndWordformText(
            $sentence,
            '<s id="16"></s>'
        );

        $this->assertNull($result);

        $this->assertEquals(
            0,
            DB::table('words')
                ->where('text_id', 1880)
                ->where('w_id', 136)
                ->count()
        );

        $this->assertEquals(
            $neighborWordBefore,
            DB::table('words')->where('id', 102)->first()
        );

        $this->assertEquals(
            $neighborLinksBefore,
            DB::table('text_wordform')
                ->where('text_id', 1880)
                ->where('w_id', 137)
                ->get()
        );

        $this->assertEquals(
            $neighborMeaningsBefore,
            DB::table('meaning_text')
                ->where('text_id', 1880)
                ->where('w_id', 137)
                ->get()
        );

        $this->assertEquals(
            0,
            DB::table('text_wordform')
                ->where('text_id', 1880)
                ->where('w_id', 136)
                ->count(),
            'Аннотации исчезнувшего XML-слова должны быть удалены.'
        );
    }

    /**
     * Добавляет две сливаемые позиции и словарь результата.
     *
     * @return void
     */
    private function seedCombinedWordMergeFixture()
    {
        DB::table('words')->insert([
            [
                'id' => 100,
                'text_id' => 1880,
                'sentence_id' => 51467,
                's_id' => 16,
                'w_id' => 134,
                'word' => 'mua',
                'checked' => 0,
                'word_number' => 1,
            ],
            [
                'id' => 103,
                'text_id' => 1880,
                'sentence_id' => 51467,
                's_id' => 16,
                'w_id' => 135,
                'word' => 'ilma',
                'checked' => 0,
                'word_number' => 2,
            ],
        ]);

        DB::table('words')
            ->where('id', 101)
            ->update(['word_number' => 3]);

        DB::table('meaning_text')->insert([
            [
                'meaning_id' => 550,
                'text_id' => 1880,
                's_id' => 16,
                'word_id' => 100,
                'relevance' => 2,
                'w_id' => 134,
            ],
            [
                'meaning_id' => 551,
                'text_id' => 1880,
                's_id' => 16,
                'word_id' => 103,
                'relevance' => 0,
                'w_id' => 135,
            ],
        ]);

        DB::table('text_wordform')->insert([
            [
                'text_id' => 1880,
                'w_id' => 134,
                'wordform_id' => 8200,
                'gramset_id' => 1,
                'relevance' => 2,
                'word_id' => 100,
            ],
            [
                'text_id' => 1880,
                'w_id' => 135,
                'wordform_id' => 8201,
                'gramset_id' => 2,
                'relevance' => 0,
                'word_id' => 103,
            ],
        ]);

        DB::table('lemmas')->insert([
            'id' => 9200,
            'lang_id' => 6,
            'lemma' => 'muailma',
            'lemma_for_search' => 'muailma',
        ]);

        DB::table('meanings')->insert([
            'id' => 9201,
            'lemma_id' => 9200,
        ]);

        DB::table('wordforms')->insert([
            'id' => 9202,
            'wordform' => 'muailma',
            'wordform_for_search' => 'muailma',
        ]);

        DB::table('lemma_wordform')->insert([
            'lemma_id' => 9200,
            'wordform_id' => 9202,
            'lang_id' => 6,
            'wordform_for_search' => 'muailma',
            'gramset_id' => 1,
        ]);
    }

    /**
     * Проверяет результат слияния независимо от оценок w136.
     *
     * @return void
     */
    private function assertCombinedWordMergeResult()
    {
        $row = DB::table('words')
            ->where('text_id', 1880)
            ->where('w_id', 134)
            ->first();

        $this->assertNotNull($row);

        $word = (array) $row;
        $wordId = (int) $word['id'];

        $this->assertSame('muailma', $word['word']);
        $this->assertEquals(51467, $word['sentence_id']);
        $this->assertEquals(16, $word['s_id']);
        $this->assertEquals(1, $word['word_number']);

        $this->assertFalse(
            in_array($wordId, [100, 101, 102, 103], true),
            'Объединённое слово должно получить новый words.id.'
        );

        $this->assertEquals(
            0,
            DB::table('words')
                ->whereIn('id', [100, 101, 103])
                ->count(),
            'Все исходные слова текущего предложения должны пересоздаться.'
        );

        foreach (['words', 'meaning_text', 'text_wordform'] as $table) {
            $this->assertEquals(
                0,
                DB::table($table)
                    ->where('text_id', 1880)
                    ->where('w_id', 135)
                    ->count(),
                'В ' . $table . ' осталась исчезнувшая позиция w135.'
            );
        }

        $this->assertEquals(
            1,
            DB::table('meaning_text')
                ->where('text_id', 1880)
                ->where('w_id', 134)
                ->count()
        );

        $this->assertEquals(
            1,
            DB::table('meaning_text')
                ->where('text_id', 1880)
                ->where('s_id', 16)
                ->where('w_id', 134)
                ->where('word_id', $wordId)
                ->where('meaning_id', 9201)
                ->where('relevance', 1)
                ->count(),
            'Объединённое слово должно получить новое значение.'
        );

        $this->assertEquals(
            1,
            DB::table('text_wordform')
                ->where('text_id', 1880)
                ->where('w_id', 134)
                ->count()
        );

        $this->assertEquals(
            1,
            DB::table('text_wordform')
                ->where('text_id', 1880)
                ->where('w_id', 134)
                ->where('word_id', $wordId)
                ->where('wordform_id', 9202)
                ->where('gramset_id', 1)
                ->where('relevance', 1)
                ->count(),
            'Объединённое слово должно получить новый разбор.'
        );
    }
}

/**
 * Носитель настоящего трейта без загрузки приложения.
 *
 * Использует настоящий XML-парсер модели Text.
 * Чтение выбранных разборов отключено: тест проверяет
 * только очистку исчезнувших позиций.
 */
class TextRemovedWordCleanupSubject
{
    use TextModify;

    public $id = 1880;
    public $lang_id = 6;

    /**
     * Передаёт разбор XML настоящему методу модели Text.
     *
     * @param string $xml XML для разбора.
     * @param int|null $textId Идентификатор текста.
     * @return array
     */
    public static function toXML($xml, $textId = null)
    {
        return \App\Models\Corpus\Text::toXML($xml, $textId);
    }

    /**
     * Отключает чтение выбранных разборов в узком тесте очистки.
     *
     * @param string $xml XML предложения.
     * @param bool $forMeanings Читать выбранные значения.
     * @param bool $forWordforms Читать выбранные словоформы.
     * @return array
     */
    public function checkedWords(
        $xml,
        $forMeanings = true,
        $forWordforms = true
    ) {
        return [];
    }
}

/**
 * Останавливает тест, если конфликт пропущен до пересоздания слов.
 *
 * Не позволяет уходить в словарные зависимости, отсутствующие
 * в изолированной тестовой БД.
 */
class TextNewWordIdConflictSubject extends TextRemovedWordCleanupSubject
{
    /**
     * При корректной защите этот метод не должен вызываться.
     *
     * @param object $sentence Предложение.
     * @param array $words XML-элементы слов.
     * @param array|null $checked_words Сохранённые оценки.
     * @return void
     */
    public function updateMeaningAndWordformSentence(
        $sentence,
        $words,
        $checked_words = null
    ) {
        throw new \RuntimeException(
            'TEST GUARD: word recreation started before '
                . 'the new XML word id conflict was rejected.'
        );
    }
}
