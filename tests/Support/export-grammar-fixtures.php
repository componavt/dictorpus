<?php

/**
 * Local, read-only export of grammar fixtures. Run explicitly, not via PHPUnit.
 * Default mode prints the configured target WITHOUT connecting to MySQL.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only.');
}

$pdo = null;
$temp = null;
try {
    $root = dirname(__DIR__, 2);
    require $root . '/bootstrap/autoload.php';
    $app = require $root . '/bootstrap/app.php';
    $app->bootstrapWith([
        \Illuminate\Foundation\Bootstrap\DetectEnvironment::class,
        \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    ]);

    $options = getopt('', ['describe', 'export', 'allow-host:', 'allow-database:']);
    $config = $app['config']->get('database.connections.mysql');
    if (!is_array($config) || ($config['driver'] ?? '') !== 'mysql') {
        throw new \RuntimeException('The mysql connection is missing or is not a MySQL connection.');
    }
    $host = (string) ($config['host'] ?? '');
    $database = (string) ($config['database'] ?? '');
    $port = (string) ($config['port'] ?? '3306');
    if ($host === '' || $database === '') {
        throw new \RuntimeException('The mysql host or database name is empty.');
    }
    echo 'Mode: ' . (isset($options['export']) ? 'EXPORT (read-only transaction)' : 'DESCRIBE (no database connection)') . PHP_EOL;
    echo 'Connection: mysql' . PHP_EOL;
    echo 'Host: ' . $host . PHP_EOL;
    echo 'Port: ' . $port . PHP_EOL;
    echo 'Database: ' . $database . PHP_EOL;
    echo 'Config cache: ' . ($app->configurationIsCached() ? 'present' : 'absent') . PHP_EOL;

    if (!isset($options['export'])) {
        exit(0);
    }
    if (isset($options['describe'])) {
        throw new \RuntimeException('Use either --describe or --export, not both.');
    }
    if (($options['allow-host'] ?? null) !== $host
        || ($options['allow-database'] ?? null) !== $database) {
        throw new \RuntimeException('Export refused. Explicit --allow-host and --allow-database must match the displayed target.');
    }
    if (!empty($config['prefix']) || !empty($config['unix_socket'])
        || !empty($config['read']) || !empty($config['write'])) {
        throw new \RuntimeException('Prefixed, socket, or split read/write connections require a separate review.');
    }

    $output = $root . '/tests/Fixtures/grammar.json';
    if (file_exists($output)) {
        throw new \RuntimeException('Export refused: tests/Fixtures/grammar.json already exists. Review it before replacing.');
    }
    $charset = $config['charset'] ?? 'utf8';
    if (!in_array($charset, ['utf8', 'utf8mb4'], true)) {
        throw new \RuntimeException('Only utf8 and utf8mb4 are supported for this export.');
    }
    $pdo = new \PDO(
        'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $database . ';charset=' . $charset,
        $config['username'] ?? '',
        $config['password'] ?? '',
        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_EMULATE_PREPARES => false]
    );
    $pdo->exec('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
    $pdo->exec('SET TRANSACTION READ ONLY');
    $pdo->beginTransaction();

    $quote = function ($identifier) {
        if (!preg_match('/\A[a-zA-Z_][a-zA-Z0-9_]*\z/', $identifier)) {
            throw new \RuntimeException('Unsupported SQL identifier.');
        }
        return '`' . $identifier . '`';
    };
    $snapshot = ['format_version' => 1, 'scope' => 'grammar-reference-and-ei', 'tables' => []];
    $read_table = function ($table, $where = '', array $bindings = []) use ($pdo, $quote, &$snapshot) {
        $quoted = $quote($table);
        $fields = $pdo->query('SHOW FULL COLUMNS FROM ' . $quoted)->fetchAll(\PDO::FETCH_ASSOC);
        $keys = $pdo->query('SHOW INDEX FROM ' . $quoted)->fetchAll(\PDO::FETCH_ASSOC);
        $primary = [];
        foreach ($keys as $key) {
            if ($key['Key_name'] === 'PRIMARY') {
                $primary[(int) $key['Seq_in_index']] = $key['Column_name'];
            }
        }
        ksort($primary);
        $primary = array_values($primary);
        $columns = [];
        foreach ($fields as $field) {
            $columns[] = [
                'name' => $field['Field'],
                'mysql_type' => $field['Type'],
                'nullable' => $field['Null'] === 'YES',
                'default' => $field['Default'],
                'auto_increment' => strpos($field['Extra'], 'auto_increment') !== false,
            ];
        }
        $order = $primary ?: array_column($columns, 'name');
        $sql = 'SELECT * FROM ' . $quoted;
        if ($where !== '') {
            $sql .= ' WHERE ' . $where;
        }
        $sql .= ' ORDER BY ' . implode(', ', array_map($quote, $order)) . ' LIMIT 50001';
        $statement = $pdo->prepare($sql);
        $statement->execute($bindings);
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (count($rows) > 50000) {
            throw new \RuntimeException('Unexpected fixture size in table ' . $table . '. Export refused.');
        }
        $snapshot['tables'][$table] = ['columns' => $columns, 'primary_key' => $primary, 'rows' => $rows];
        return $rows;
    };

    foreach ([
        'parts_of_speech', 'langs', 'dialects', 'gram_categories',
        'grams', 'gramset_categories', 'gramsets', 'gramset_pos',
    ] as $table) {
        $read_table($table);
    }
    $aux_ids = [];
    foreach ($snapshot['tables']['parts_of_speech']['rows'] as $row) {
        if ($row['code'] === 'AUX') {
            $aux_ids[] = $row['id'];
        }
    }
    if (count($aux_ids) !== 1) {
        throw new \RuntimeException('Exactly one AUX part of speech is required. No fixture file was written.');
    }
    $lemmas = $read_table('lemmas', '`lemma` = ? AND `pos_id` = ? AND `lang_id` IN (1, 4, 5, 6)', ['ei', $aux_ids[0]]);
    foreach ([1, 4] as $required_lang) {
        $found = false;
        foreach ($lemmas as $lemma) {
            if ((int) $lemma['lang_id'] === $required_lang) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            throw new \RuntimeException('No AUX lemma ei for required language ' . $required_lang . '. No fixture file was written.');
        }
    }
    $lemma_ids = array_values(array_unique(array_column($lemmas, 'id')));
    $placeholders = implode(', ', array_fill(0, count($lemma_ids), '?'));
    $links = $read_table('lemma_wordform', '`lemma_id` IN (' . $placeholders . ')', $lemma_ids);
    $wordform_ids = array_values(array_unique(array_column($links, 'wordform_id')));
    if (!$wordform_ids) {
        throw new \RuntimeException('No wordforms linked to AUX lemmas ei. No fixture file was written.');
    }
    if (count($wordform_ids) > 10000) {
        throw new \RuntimeException('Unexpected number of auxiliary wordforms. Export refused.');
    }
    $placeholders = implode(', ', array_fill(0, count($wordform_ids), '?'));
    $read_table('wordforms', '`id` IN (' . $placeholders . ')', $wordform_ids);
    $pdo->rollBack();
    $pdo = null;

    $json = json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n";
    $directory = dirname($output);
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        throw new \RuntimeException('Cannot create tests/Fixtures.');
    }
    $temp = tempnam($directory, 'grammar-');
    if ($temp === false || file_put_contents($temp, $json, LOCK_EX) !== strlen($json)) {
        throw new \RuntimeException('Cannot write the fixture file.');
    }
    if (file_exists($output) || !rename($temp, $output)) {
        throw new \RuntimeException('Cannot install the fixture file without overwriting an existing file.');
    }
    $temp = null;
    echo PHP_EOL . 'Fixture: tests/Fixtures/grammar.json' . PHP_EOL;
    foreach ($snapshot['tables'] as $name => $table) {
        echo $name . ': ' . count($table['rows']) . ' rows' . PHP_EOL;
    }
    echo 'Bytes: ' . strlen($json) . PHP_EOL;
    echo 'SHA256: ' . hash('sha256', $json) . PHP_EOL;
} catch (\Throwable $error) {
    if ($pdo instanceof \PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    if (is_string($temp) && is_file($temp)) {
        unlink($temp);
    }
    fwrite(STDERR, 'ERROR: ' . $error->getMessage() . PHP_EOL);
    exit(1);
}
