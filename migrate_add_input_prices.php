<?php

require_once __DIR__ . '/app/Core/Database.php';

header('Content-Type: text/plain; charset=UTF-8');

try {
    $pdo = Database::connection();
    $databaseName = $pdo->query('SELECT DATABASE()')->fetchColumn();

    if (!$databaseName) {
        throw new RuntimeException('Aktif veritabanı bulunamadı.');
    }

    $columns = existingColumns($pdo, $databaseName, 'fields');
    $queries = [];

    if (!in_array('gubre_fiyat', $columns, true)) {
        $queries[] = 'ALTER TABLE fields ADD COLUMN gubre_fiyat DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER gubre';
    }

    if (!in_array('mazot_fiyat', $columns, true)) {
        $queries[] = 'ALTER TABLE fields ADD COLUMN mazot_fiyat DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER mazot';
    }

    if (empty($queries)) {
        echo "Kolonlar zaten mevcut. İşlem yapılmadı.\n";
        exit;
    }

    foreach ($queries as $query) {
        $pdo->exec($query);
        echo "Çalıştı: {$query}\n";
    }

    echo "Tamamlandı. Artık detaylı kayıt tekrar denenebilir.\n";
} catch (Throwable $error) {
    http_response_code(500);
    echo "Hata: " . $error->getMessage() . "\n";
}

function existingColumns(PDO $pdo, string $databaseName, string $tableName): array
{
    $stmt = $pdo->prepare(
        'SELECT COLUMN_NAME
         FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = :database_name
           AND TABLE_NAME = :table_name'
    );

    $stmt->execute([
        'database_name' => $databaseName,
        'table_name' => $tableName,
    ]);

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
