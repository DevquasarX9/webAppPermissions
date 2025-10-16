<?php

namespace App\Models;

use App\Database\Connection;

abstract class Model
{
    protected static string $table;
    protected static string $primaryKey = 'id';

    public static function all(): array
    {
        $table = static::$table;
        $sql = "SELECT * FROM {$table}";

        return Connection::fetchAll($sql);
    }

    public static function find(int $id): ?array
    {
        $table = static::$table;
        $primaryKey = static::$primaryKey;
        $sql = "SELECT * FROM {$table} WHERE {$primaryKey} = :id LIMIT 1";

        return Connection::fetch($sql, ['id' => $id]);
    }

    public static function findBy(string $column, mixed $value): ?array
    {
        $table = static::$table;
        $sql = "SELECT * FROM {$table} WHERE {$column} = :value LIMIT 1";

        return Connection::fetch($sql, ['value' => $value]);
    }

    public static function where(string $column, mixed $value): array
    {
        $table = static::$table;
        $sql = "SELECT * FROM {$table} WHERE {$column} = :value";

        return Connection::fetchAll($sql, ['value' => $value]);
    }

    public static function create(array $data): ?int
    {
        $table = static::$table;
        $columns = array_keys($data);
        $placeholders = array_map(fn ($col) => ":{$col}", $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        Connection::execute($sql, $data);

        return (int)Connection::lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $table = static::$table;
        $primaryKey = static::$primaryKey;
        $sets = array_map(fn ($col) => "{$col} = :{$col}", array_keys($data));

        $sql = sprintf(
            "UPDATE %s SET %s WHERE {$primaryKey} = :id",
            $table,
            implode(', ', $sets)
        );

        $data['id'] = $id;

        return Connection::execute($sql, $data);
    }

    public static function delete(int $id): bool
    {
        $table = static::$table;
        $primaryKey = static::$primaryKey;
        $sql = "DELETE FROM {$table} WHERE {$primaryKey} = :id";

        return Connection::execute($sql, ['id' => $id]);
    }

    public static function count(): int
    {
        $table = static::$table;
        $sql = "SELECT COUNT(*) as count FROM {$table}";
        $result = Connection::fetch($sql);

        return (int)($result['count'] ?? 0);
    }
}
