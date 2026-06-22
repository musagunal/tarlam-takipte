<?php

class Field
{
    public static function allForUser(int $userId, string $search = ''): array
    {
        $params = ['user_id' => $userId];
        $sql = 'SELECT * FROM fields WHERE user_id = :user_id';

        if ($search !== '') {
            $sql .= ' AND (
                tarla_adi LIKE :search
                OR lokasyon LIKE :search
                OR urun LIKE :search
                OR CONVERT(tur USING utf8mb4) LIKE :search
            )';
            $params['search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY created_at DESC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function findForUser(int $id, int $userId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM fields WHERE id = :id AND user_id = :user_id LIMIT 1'
        );
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $field = $stmt->fetch();

        return $field ?: null;
    }

    public static function create(int $userId, array $data): void
    {
        self::ensureInputPriceColumns();

        $payload = self::payload($userId, $data);
        $columns = self::writableColumns(array_keys($payload));
        $columnList = implode(', ', $columns);
        $valueList = ':' . implode(', :', $columns);
        $stmt = Database::connection()->prepare("INSERT INTO fields ({$columnList}) VALUES ({$valueList})");

        $stmt->execute(array_intersect_key($payload, array_flip($columns)));
    }

    public static function update(int $id, int $userId, array $data): void
    {
        self::ensureInputPriceColumns();
        $payload = self::payload($userId, $data);
        $columns = array_values(array_diff(self::writableColumns(array_keys($payload)), ['user_id']));
        $setSql = implode(', ', array_map(fn($column) => "{$column} = :{$column}", $columns));
        $payload['id'] = $id;
        $payload = array_intersect_key($payload, array_flip(array_merge($columns, ['id', 'user_id'])));
        $stmt = Database::connection()->prepare("UPDATE fields SET {$setSql} WHERE id = :id AND user_id = :user_id");
        $stmt->execute($payload);
    }

    public static function delete(int $id, int $userId): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM fields WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    public static function statsForUser(int $userId): array
    {
        $stmt = Database::connection()->prepare(
            "SELECT
              COUNT(*) AS total_tarla,
              COALESCE(SUM(alan), 0) AS total_alan,
              SUM(CASE WHEN CONVERT(tur USING utf8mb4) = :detayli_tur THEN 1 ELSE 0 END) AS detayli_count
            FROM fields
            WHERE user_id = :user_id"
        );
        $stmt->execute([
            'user_id' => $userId,
            'detayli_tur' => 'Detaylı Kayıt',
        ]);

        return $stmt->fetch() ?: ['total_tarla' => 0, 'total_alan' => 0, 'detayli_count' => 0];
    }

    private static function payload(int $userId, array $data): array
    {
        return [
            'user_id' => $userId,
            'tarla_adi' => trim($data['tarla_adi'] ?? ''),
            'lokasyon' => trim($data['lokasyon'] ?? ''),
            'alan' => (float) ($data['alan'] ?? 0),
            'urun' => trim($data['urun'] ?? ''),
            'gubre' => (float) ($data['gubre'] ?? 0),
            'gubre_fiyat' => (float) ($data['gubre_fiyat'] ?? 0),
            'mazot' => (float) ($data['mazot'] ?? 0),
            'mazot_fiyat' => (float) ($data['mazot_fiyat'] ?? 0),
            'verim' => (float) ($data['verim'] ?? 0),
            'satis' => (float) ($data['satis'] ?? 0),
            'tur' => $data['tur'] ?? 'Hızlı Kayıt',
        ];
    }

    private static function ensureInputPriceColumns(): void
    {
        $pdo = Database::connection();
        $columns = self::tableColumns();

        if (!in_array('gubre_fiyat', $columns, true)) {
            self::tryAlter('ALTER TABLE fields ADD COLUMN gubre_fiyat DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER gubre');
        }

        $columns = self::tableColumns();
        if (!in_array('mazot_fiyat', $columns, true)) {
            self::tryAlter('ALTER TABLE fields ADD COLUMN mazot_fiyat DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER mazot');
        }
    }

    private static function writableColumns(array $wantedColumns): array
    {
        $existingColumns = self::tableColumns();

        return array_values(array_filter($wantedColumns, fn($column) => in_array($column, $existingColumns, true)));
    }

    private static function tableColumns(): array
    {
        $pdo = Database::connection();
        $databaseName = $pdo->query('SELECT DATABASE()')->fetchColumn();
        $stmt = $pdo->prepare(
            'SELECT COLUMN_NAME
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = :database_name
               AND TABLE_NAME = :table_name'
        );

        $stmt->execute([
            'database_name' => $databaseName,
            'table_name' => 'fields',
        ]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private static function tryAlter(string $sql): void
    {
        try {
            Database::connection()->exec($sql);
        } catch (PDOException $error) {
            // If hosting blocks ALTER, create/update continues with the columns that already exist.
        }
    }
}
