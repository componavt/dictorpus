<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;

/** Test-only application: no configured external database connections. */
abstract class IsolatedSqliteTestCase extends TestCase
{
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';
        $app->afterBootstrapping(LoadConfiguration::class, function ($app) {
            if (!$app->environment('testing')) {
                throw new \RuntimeException('SQLite tests require APP_ENV=testing. No fixture was loaded.');
            }
            $app['config']->set('database.default', 'sqlite_testing');
            $app['config']->set('database.connections', [
                'sqlite_testing' => [
                    'driver' => 'sqlite',
                    'database' => ':memory:',
                    'prefix' => '',
                ],
            ]);
        });
        $app->make(Kernel::class)->bootstrap();
        self::assertSqliteIsolation($app);
        return $app;
    }

    /** Throw before fixture DDL or inserts if the Laravel connection is unsafe. */
    public static function assertSqliteIsolation($app)
    {
        $config = $app['config'];
        $connections = $config->get('database.connections');
        if (!$app->environment('testing')
            || $config->get('database.default') !== 'sqlite_testing'
            || !is_array($connections)
            || array_keys($connections) !== ['sqlite_testing']) {
            throw new \RuntimeException('Unsafe test database configuration. Only sqlite_testing is permitted.');
        }
        $connection_config = $connections['sqlite_testing'];
        if (($connection_config['driver'] ?? null) !== 'sqlite'
            || ($connection_config['database'] ?? null) !== ':memory:'
            || !empty($connection_config['prefix'])
            || !empty($connection_config['read'])
            || !empty($connection_config['write'])) {
            throw new \RuntimeException('Tests may use only SQLite :memory: without prefixes or read/write splitting.');
        }
        $manager = $app['db'];
        foreach ($manager->getConnections() as $name => $connection) {
            if ($name !== 'sqlite_testing') {
                throw new \RuntimeException('Unexpected existing database connection: ' . $name);
            }
        }
        $connection = $manager->connection('sqlite_testing');
        if ($connection->getConfig('driver') !== 'sqlite'
            || $connection->getConfig('database') !== ':memory:'
            || $connection->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME) !== 'sqlite') {
            throw new \RuntimeException('The actual test connection is not SQLite :memory:.');
        }
        $databases = $connection->getPdo()->query('PRAGMA database_list')->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($databases as $database) {
            if (!in_array($database['name'], ['main', 'temp'], true)
                || $database['file'] !== '') {
                throw new \RuntimeException('A file-backed or attached database was found. Fixture loading refused.');
            }
        }
        if (\Illuminate\Database\Eloquent\Model::getConnectionResolver() !== $manager) {
            throw new \RuntimeException('Eloquent is using a different connection resolver.');
        }
        return $connection;
    }

    protected function tearDown()
    {
        try {
            if ($this->app && $this->app->bound('db')) {
                $manager = $this->app['db'];
                foreach (array_keys($manager->getConnections()) as $name) {
                    $manager->purge($name);
                }
            }
        } finally {
            parent::tearDown();
        }
    }
}
