<?php

namespace Bubblegum\Database;

class DB {
    /**
     * @var PDO|null
     */
    protected static ?PDO $pdo;

    /**
     * @return string
     */
    protected static function getDatabasePropertiesFromEnv(): string
    {
        return sprintf(
            "%s:host=%s;port=%d;dbname=%s",
            env('DATABASE_CONNECTION', 'pgsql'),
            env('DATABASE_HOST', 'localhost'),
            env('DATABASE_PORT', 5432),
            env('DATABASE_DB', 'database')
        );
    }

    /**
     * @return void
     */
    public static function initPDO()
    {
        self::$pdo = new PDO(self::getDatabasePropertiesFromEnv(), env('DATABASE_USER', 'root'), env('DATABASE_PASSWORD'));
    }

    public static function exec($sqlStatement): void
    {
        self::$pdo->exec($sqlStatement);
    }

    /**
     * @param string $tableName
     * @param string[] $columnSqlParts
     * @return void
     */
    public static function createTable(string $tableName, array $columnSqlParts): void
    {
        $columnsSql = implode(',', $columnSqlParts);
        self::exec("CREATE TABLE $tableName($columnsSql);");
    }
}