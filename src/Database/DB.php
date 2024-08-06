<?php

namespace Bubblegum\Database;

use PDO;
use PDOStatement;

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
            "%s:host=%s;dbname=%s;",
            env('DATABASE_CONNECTION', 'pgsql'),
            env('DATABASE_HOST', 'localhost'),
            env('DATABASE_DB', 'database')
        );
    }

    /**
     * @return void
     */
    public static function initPDO()
    {
        self::$pdo = new PDO(self::getDatabasePropertiesFromEnv(), 'bubblegum', 'password');
    }

    /**
     * @param $sqlStatement
     * @return void
     */
    public static function exec($sqlStatement): void
    {
        self::$pdo->exec($sqlStatement);
    }

    /**
     * @param string $tableName
     * @param string[] $columnSqlParts
     * @return void
     */
    public static function createTable(string $tableName, array $columnSqlParts=[]): void
    {
        $columnsSql = implode(',', $columnSqlParts);
        self::exec("CREATE TABLE $tableName($columnsSql);");
    }

    /**
     * @param string $tableName
     * @return void
     */
    public static function dropTable(string $tableName): void
    {
        self::exec("DROP TABLE $tableName;");
    }

    /**
     * @param string $tableName
     * @param string $columnName
     * @return void
     */
    public static function dropColumn(string $tableName, string $columnName): void
    {
        self::exec("ALTER TABLE $tableName DROP COLUMN $columnName;");
    }

    public static function select(string $tableName, ?array $columns=null, array $whereSqlParts=[]): false|PDOStatement
    {
        $columns = $columns ? implode(',', $columns) : '*';
        return self::$pdo->prepare("SELECT $columns FROM $tableName" . ($whereSqlParts ? ' WHERE ' . implode(' AND ', $whereSqlParts) : ''));
    }
}