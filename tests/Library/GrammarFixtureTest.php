<?php

namespace Tests;

class GrammarFixtureTest extends GrammarTestCase
{
    public function testUsesOnlyMemorySqlite()
    {
        $this->assertSame('sqlite_testing', $this->app['db']->getDefaultConnection());
        $this->assertSame(['sqlite_testing'], array_keys($this->app['config']->get('database.connections')));
        $this->assertSame('sqlite', $this->app['db']->connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME));
    }

    public function testMysqlConnectionIsNotConfigured()
    {
        try {
            $this->app['db']->connection('mysql');
        } catch (\InvalidArgumentException $error) {
            $this->assertContains('not configured', $error->getMessage());
            return;
        }
        $this->fail('The mysql connection must not be available in an isolated grammar test.');
    }

    /** @dataProvider unsafeSettingsProvider */
    public function testIsolationGuardRejectsUnsafeSettings($key, $value, $message)
    {
        $config = $this->app['config'];
        $original = $config->get('database');
        try {
            $config->set($key, $value);
            try {
                IsolatedSqliteTestCase::assertSqliteIsolation($this->app);
            } catch (\RuntimeException $error) {
                $this->assertContains($message, $error->getMessage());
                return;
            }
            $this->fail('The unsafe database setting was not rejected.');
        } finally {
            $config->set('database', $original);
        }
    }

    public function unsafeSettingsProvider()
    {
        return [
            ['database.default', 'mysql', 'Unsafe test database configuration'],
            ['database.connections.sqlite_testing.database', 'testing.sqlite', 'only SQLite :memory:'],
            ['database.connections.mysql', ['driver' => 'mysql'], 'Unsafe test database configuration'],
        ];
    }

    public function testFixtureRowCounts()
    {
        $expected = [
            'parts_of_speech' => 21, 'langs' => 7, 'dialects' => 58,
            'gram_categories' => 9, 'grams' => 44, 'gramset_categories' => 28,
            'gramsets' => 213, 'gramset_pos' => 1704, 'lemmas' => 4,
            'lemma_wordform' => 58, 'wordforms' => 27,
        ];
        foreach ($expected as $table => $count) {
            $this->assertSame($count, $this->app['db']->table($table)->count(), $table);
        }
    }

    /** @dataProvider freshDatabaseProvider */
    public function testEachTestReceivesFreshData($iteration)
    {
        $db = $this->app['db'];
        $this->assertSame(4, $db->table('lemmas')->count(), 'Iteration ' . $iteration);
        $db->table('lemmas')->delete();
        $this->assertSame(0, $db->table('lemmas')->count());
    }

    public function freshDatabaseProvider()
    {
        return [['first'], ['second']];
    }
}
