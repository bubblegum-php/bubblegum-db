<?php

namespace Bubblegum\Database;

use PDO;
use PDOStatement;

/**
 * Facade for database connection
 */
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
        if (isset(static::$pdo)) {
            return;
        }
        static::$pdo = new PDO(
            static::getDatabasePropertiesFromEnv(),
            env('DATABASE_USER'),
            env('DATABASE_PASSWORD')
        );
    }

    /**
     * @param $sqlStatement
     * @return void
     */
    public static function exec($sqlStatement): void
    {
        static::$pdo->exec($sqlStatement);
    }

    /**
     * @param string $statement
     * @return false|PDOStatement
     */
    public static function prepare(string $statement): false|PDOStatement
    {
        return static::$pdo->prepare($statement);
    }

    /**
     * @param string $tableName
     * @param string[] $columnSqlParts
     * @return void
     */
    public static function createTable(string $tableName, array $columnSqlParts=[]): void
    {
        $columnsSql = implode(',', $columnSqlParts);
        static::exec("CREATE TABLE $tableName($columnsSql);");
    }

    /**
     * @param string $tableName
     * @return void
     */
    public static function dropTable(string $tableName): void
    {
        static::exec("DROP TABLE $tableName;");
    }

    /**
     * @param string $tableName
     * @param string $columnName
     * @return void
     */
    public static function dropColumn(string $tableName, string $columnName): void
    {
        static::exec("ALTER TABLE $tableName DROP COLUMN $columnName;");
    }

    /**
     * @param string $tableName
     * @param ConditionsUnion $conditionUnion
     * @param array|null $columns
     * @return false|PDOStatement
     */
    public static function select(string $tableName, ConditionsUnion $conditionUnion, ?array $columns = null): false|PDOStatement
    {
        $sqlConditionsPart = $conditionUnion->getSqlPart();
        $sqlColumnsPart = $columns ? implode(',', $columns) : '*';
        return static::prepare("SELECT $sqlColumnsPart FROM $tableName WHERE $sqlConditionsPart;");
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected static function valueToSql(mixed $value): string
    {
        return match (gettype($value)) {
            'string' => "'$value'",
            default => (string) $value,
        };
    }

    /**
     * @param string $tableName
     * @param array $values
     * @return int
     */
    public static function insert(string $tableName, array $values): int
     {
         $sqlColumnsPart = implode(',', array_keys($values));
         $sqlValuesPart = implode(',',
             array_map(function ($value) {
                 return static::valueToSql($value);
             }, array_values($values)));
         static::exec("INSERT INTO $tableName ($sqlColumnsPart) VALUES ($sqlValuesPart);");
         return static::$pdo->lastInsertId();
     }

    /**
     * @param string $tableName
     * @param array $data
     * @param ConditionsUnion $findConditions
     * @return void
     */
    public static function update(string $tableName, array $data, ConditionsUnion $findConditions): void
     {
         $findConditionsSqlPart = $findConditions->getSqlPart();
         $updateValuesSqlPart = array_map(
             function ($key, $value) {
                 $value = static::valueToSql($value);
                 return "$key = $value";
             },
             array_keys($data), array_values($data)
         );
         $updateValuesSqlPart = implode(',', $updateValuesSqlPart);
         static::exec("UPDATE $tableName SET $updateValuesSqlPart WHERE $findConditionsSqlPart;");
     }
}