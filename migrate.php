<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

echo "Running migrations...\n";

$sqlFiles = [
    __DIR__ . '/sql/schema.sql',
    __DIR__ . '/sql/seed.sql',
];

$pdo = db();

foreach ($sqlFiles as $file) {

    if (!file_exists($file)) {
        echo "File not found: $file\n";
        continue;
    }

    echo "Executing: " . basename($file) . "\n";

    $sql = file_get_contents($file);

    if ($sql === false) {
        echo "Error reading file: $file\n";
        continue;
    }

    $pdo->exec($sql);
}

echo "Done ✅\n";
