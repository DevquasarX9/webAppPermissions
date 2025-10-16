<?php

namespace App\Config;

class Database
{
    public static function getConfig(): array
    {
        return [
            'host' => $_ENV['DB_HOST'] ?? 'postgres',
            'port' => $_ENV['DB_PORT'] ?? '5432',
            'database' => $_ENV['DB_DATABASE'] ?? 'webApp',
            'username' => $_ENV['DB_USERNAME'] ?? 'permissions',
            'password' => $_ENV['DB_PASSWORD'] ?? 'projects',
            'charset' => 'utf8',
        ];
    }

    public static function getDsn(): string
    {
        $config = self::getConfig();

        return sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $config['host'],
            $config['port'],
            $config['database']
        );
    }
}
