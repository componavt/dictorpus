<?php

namespace Tests\Support;

use Tests\IsolatedSqliteTestCase;

/**
 * Minimal SQLite schema for ORM grammar tests, not a MySQL schema replica.
 * Loads columns, nullability and primary keys. MySQL defaults, secondary
 * indexes, generated expressions and foreign keys are deliberately not copied.
 */
class GrammarFixture
{
    const SHA256 = 'f674fdfef69201134fd43aedb237b6346e5912d8f16a4a5527d2d5bf1ec510cf';
    private static $snapshot;

    public static function load($app)
    {
        $connection = IsolatedSqliteTestCase::assertSqliteIsolation($app);
        $pdo = $connection->getPdo();
        $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")
            ->fetchAll(\PDO::FETCH_COLUMN);
        if ($tables || $pdo->inTransaction()) {
            throw new \RuntimeException('Grammar fixtures require a fresh, empty SQLite database outside a transaction.');
        }
        $snapshot = self::snapshot();
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->beginTransaction();
        try {
            foreach ($snapshot['tables'] as $name => $table) {
                self::createTable($pdo, $name, $table);
                self::insertRows($pdo, $name, $table);
            }
            $pdo->commit();
        } catch (\Throwable $error) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $error;
        }
    }

    /** Load the reviewed abai example only for tests that explicitly need it. */
    public static function loadAbai($app)
    {
        $connection = IsolatedSqliteTestCase::assertSqliteIsolation($app);
        $pdo = $connection->getPdo();
        if ($pdo->inTransaction()) {
            throw new \RuntimeException('Load the abai fixture outside a transaction.');
        }
        $path = dirname(__DIR__) . '/Fixtures/grammar-remaining.json';
        if (!is_file($path) || !is_readable($path)) {
            throw new \RuntimeException('Missing tests/Fixtures/grammar-remaining.json.');
        }
        $json = str_replace("\r\n", "\n", file_get_contents($path));
        if (!hash_equals('8ab2640cd8d58cf15f96904ca4226ee6ec9178ff20180cd4b3939d073b914ea7', hash('sha256', $json))) {
            throw new \RuntimeException('Abai fixture SHA256 mismatch. Do not regenerate it silently.');
        }
        $snapshot = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (($snapshot['format_version'] ?? null) !== 1
            || ($snapshot['scope'] ?? null) !== 'grammar-remaining-diagnostics'
            || ($snapshot['lemma_id'] ?? null) !== 3539
            || !isset($snapshot['tables']) || !is_array($snapshot['tables'])) {
            throw new \RuntimeException('Unsupported abai fixture format.');
        }
        $expected = ['lemmas', 'lemma_features', 'lemma_wordform', 'wordforms'];
        $actual = array_keys($snapshot['tables']);
        sort($expected);
        sort($actual);
        if ($actual !== $expected) {
            throw new \RuntimeException('Unexpected tables in the abai fixture.');
        }
        $existing = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")
            ->fetchAll(\PDO::FETCH_COLUMN);
        foreach (['lemmas', 'lemma_wordform', 'wordforms'] as $name) {
            if (!in_array($name, $existing, true)) {
                throw new \RuntimeException('Load the base grammar fixture before the abai fixture.');
            }
        }
        if (in_array('lemma_features', $existing, true)) {
            throw new \RuntimeException('The abai fixture requires a fresh lemma_features table.');
        }
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->beginTransaction();
        try {
            self::createTable($pdo, 'lemma_features', $snapshot['tables']['lemma_features']);
            foreach (['lemmas', 'lemma_features', 'wordforms', 'lemma_wordform'] as $name) {
                self::insertRows($pdo, $name, $snapshot['tables'][$name]);
            }
            $pdo->commit();
        } catch (\Throwable $error) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $error;
        }
    }

    private static function snapshot()
    {
        if (self::$snapshot !== null) {
            return self::$snapshot;
        }
        $path = dirname(__DIR__) . '/Fixtures/grammar.json';
        if (!is_file($path) || !is_readable($path)) {
            throw new \RuntimeException('Missing tests/Fixtures/grammar.json. Copy the reviewed fixture snapshot first.');
        }
        // Git may check out JSON using CRLF on Windows.
        $json = str_replace("\r\n", "\n", file_get_contents($path));
        if (!hash_equals(self::SHA256, hash('sha256', $json))) {
            throw new \RuntimeException('Grammar fixture SHA256 mismatch. Do not silently regenerate or replace the snapshot.');
        }
        $snapshot = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (($snapshot['format_version'] ?? null) !== 1
            || ($snapshot['scope'] ?? null) !== 'grammar-reference-and-ei'
            || !isset($snapshot['tables']) || !is_array($snapshot['tables'])) {
            throw new \RuntimeException('Unsupported grammar fixture format.');
        }
        $expected = [
            'parts_of_speech', 'langs', 'dialects', 'gram_categories',
            'grams', 'gramset_categories', 'gramsets', 'gramset_pos',
            'lemmas', 'lemma_wordform', 'wordforms',
        ];
        $actual = array_keys($snapshot['tables']);
        sort($expected);
        sort($actual);
        if ($actual !== $expected) {
            throw new \RuntimeException('Unexpected tables in the grammar fixture.');
        }
        self::$snapshot = $snapshot;
        return self::$snapshot;
    }

    private static function identifier($name)
    {
        if (!is_string($name) || !preg_match('/\A[a-zA-Z_][a-zA-Z0-9_]*\z/', $name)) {
            throw new \RuntimeException('Invalid fixture identifier.');
        }
        return '"' . $name . '"';
    }

    private static function sqliteType($mysql_type)
    {
        if (preg_match('/^(tinyint|smallint|mediumint|int|integer|bigint|bool|boolean|bit)\b/i', $mysql_type)) {
            return 'INTEGER';
        }
        if (preg_match('/^(float|double|real)\b/i', $mysql_type)) {
            return 'REAL';
        }
        if (preg_match('/^(decimal|numeric)\b/i', $mysql_type)) {
            return 'NUMERIC';
        }
        if (preg_match('/^(tinyblob|blob|mediumblob|longblob|binary|varbinary)\b/i', $mysql_type)) {
            return 'BLOB';
        }
        return 'TEXT';
    }

    private static function createTable(\PDO $pdo, $name, array $table)
    {
        $primary = $table['primary_key'];
        $definitions = [];
        $names = [];
        $inline_primary = false;
        foreach ($table['columns'] as $column) {
            $field = $column['name'];
            $names[] = $field;
            $type = self::sqliteType($column['mysql_type']);
            $definition = self::identifier($field) . ' ' . $type;
            if (count($primary) === 1 && $primary[0] === $field
                && $type === 'INTEGER' && $column['auto_increment']) {
                $definition .= ' PRIMARY KEY AUTOINCREMENT';
                $inline_primary = true;
            } elseif (!$column['nullable']) {
                $definition .= ' NOT NULL';
            }
            $definitions[] = $definition;
        }
        if (!$names || count(array_unique($names)) !== count($names)
            || array_diff($primary, $names)) {
            throw new \RuntimeException('Invalid column or primary key metadata in ' . $name);
        }
        if ($primary && !$inline_primary) {
            $definitions[] = 'PRIMARY KEY (' . implode(', ', array_map([self::class, 'identifier'], $primary)) . ')';
        }
        $pdo->exec('CREATE TABLE ' . self::identifier($name) . ' (' . implode(', ', $definitions) . ')');
    }

    private static function insertRows(\PDO $pdo, $name, array $table)
    {
        $names = array_column($table['columns'], 'name');
        $sql = 'INSERT INTO ' . self::identifier($name)
            . ' (' . implode(', ', array_map([self::class, 'identifier'], $names)) . ')'
            . ' VALUES (' . implode(', ', array_fill(0, count($names), '?')) . ')';
        $statement = $pdo->prepare($sql);
        foreach ($table['rows'] as $row) {
            $keys = array_keys($row);
            $expected = $names;
            sort($keys);
            sort($expected);
            if ($keys !== $expected) {
                throw new \RuntimeException('Fixture row does not match the columns in ' . $name);
            }
            $values = [];
            foreach ($names as $field) {
                $values[] = $row[$field];
            }
            $statement->execute($values);
        }
    }
}
