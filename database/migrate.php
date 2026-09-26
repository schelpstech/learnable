<?php

require dirname(__DIR__) . '/config/database.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$pdo = database_pdo();
$directory = __DIR__ . '/migrations';
$files = glob($directory . '/*.sql');
sort($files, SORT_STRING);

foreach ($files as $file) {
    $migration = basename($file, '.sql');
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS cbt_schema_migrations (
            migration VARCHAR(190) NOT NULL PRIMARY KEY,
            applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
    $check = $pdo->prepare('SELECT 1 FROM cbt_schema_migrations WHERE migration = ? LIMIT 1');
    $check->execute(array($migration));
    if ($check->fetchColumn()) {
        echo "Already applied: {$migration}\n";
        continue;
    }
    $sql = file_get_contents($file);
    if ($sql === false) {
        throw new RuntimeException('Unable to read migration: ' . $migration);
    }
    $pdo->exec($sql);
    $record = $pdo->prepare('INSERT IGNORE INTO cbt_schema_migrations (migration) VALUES (?)');
    $record->execute(array($migration));
    echo "Applied: {$migration}\n";
}
