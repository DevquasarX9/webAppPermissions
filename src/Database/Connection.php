<?php

namespace App\Database;

use App\Config\Database as DatabaseConfig;

class Connection
{
    private static ?\PDO $instance = null;

    public static function getInstance(): \PDO
    {
        if (self::$instance === null) {
            try {
                $config = DatabaseConfig::getConfig();
                $dsn = DatabaseConfig::getDsn();

                self::$instance = new \PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                        \PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            } catch (\PDOException $e) {
                throw new \PDOException('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    public static function fetch(string $sql, array $params = []): ?array
    {
        $stmt = self::query($sql, $params);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function execute(string $sql, array $params = []): bool
    {
        $stmt = self::query($sql, $params);

        return $stmt->rowCount() > 0;
    }

    public static function lastInsertId(): string
    {
        return self::getInstance()->lastInsertId();
    }
}
