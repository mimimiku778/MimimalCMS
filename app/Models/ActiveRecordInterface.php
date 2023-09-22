<?php

namespace App\Models;

/**
 * AbstractActiveRecord is the base class for active record models.
 */
interface ActiveRecordInterface
{
    /**
     * Find records based on a query and optional parameters.
     *
     * @param string      $query  The SQL query.
     * @param array|null  $params [optional] The query parameters.
     *
     * @return array An array of records matching the query.
     */
    public static function findRecords(string $query, ?array $params = null): array;

    /**
     * Executes a LIKE search query.
     * 
     * @param callable $query A function that returns a string representing the SQL query. 
     * * *Example:* `fn (string $where): string => "SELECT * FROM table {$where} AND category = :category LIMIT :offset, :limit"`
     * 
     * @param callable $whereClauseQuery A function that returns a string representing the WHERE clause.
     * * *Example:* `fn (int $i): string => "(title LIKE :keyword{$i} OR text LIKE :keyword{$i})"`
     * 
     * @param string $keyword The keyword(s) to search for.
     * \InvalidArgumentException will be thrown if the string is empty or only contains whitespace characters.
     * * *Example:* `'Split keywords by whitespace and search with LIKE'`
     * 
     * @param array|null $params [optional] An associative array of query parameters.
     * \InvalidArgumentException will be thrown if any of the array values are not strings or numbers.
     * * *Example:* `['category' => 'foods', 'limit' => 20, 'offset' => 60]`
     * 
     * @return array An array of records matching the query.
     * 
     * @throws \PDOException If an error occurs during the query execution.
     * @throws \LogicException If any of the given callbacks are invalid.
     * @throws \InvalidArgumentException If any of the parameter values are invalid or the given callbacks are invalid.
     */
    public static function searchRecords(
        callable $query,
        callable $whereClauseQuery,
        string $keyword,
        ?array $params = null
    ): array;

    /**
     * Find a single record based on a query and optional parameters.
     *
     * @param string     $query  The SQL query.
     * @param array|null $params [optional] The query parameters.
     *
     * @return bool True if a record is found; false otherwise.
     */
    public function find(string $query, ?array $params = null): bool;

    /**
     * Insert a record into the database.
     * 
     * @param ?string [optional] $tableName The name of the table. If null, uses the name of the class.
     * 
     * @return int The last inserted ID.
     */
    public function insert(?string $tableName = null): int;
}
