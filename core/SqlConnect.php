<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database_config.php';

use Config\DatabaseConfig;

/**
 * PDO wrapper class for SQL databases
 */
class SqlConnect
{
    public PDO $pdo;

    /**
     * @throws PDOException
     */
    public function __construct()
    {
        $this->pdo = new PDO(
            'mysql:host=' . DatabaseConfig::HOST . ';dbname=' . DatabaseConfig::DB_NAME . ';charset=utf8mb4',
            DatabaseConfig::USER_NAME,
            DatabaseConfig::PASSWORD
        );

        // Enable PDO to throw exceptions on error
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Executes an SQL query and returns a PDOStatement object with bound values.
     *
     * @param string $query The SQL query to execute.
     * @param array|null $params An associative array of query parameters. (Optional)
     * @return PDOStatement|false Returns a PDOStatement object containing the results of the query, or false on failure.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws InvalidArgumentException If any of the parameter values are invalid.
     */
    public function prepareAndExecuteQuery(string $query, ?array $params = null): PDOStatement|false
    {
        $stmt = $this->pdo->prepare($query);

        if (!$stmt) {
            return false;
        }

        if ($params === null) {
            $stmt->execute();
            return $stmt;
        }

        foreach ($params as $key => $value) {
            if (!is_string($value) && !is_numeric($value)) {
                throw new InvalidArgumentException(
                    "Invalid parameter value for key {$key}: only strings and numbers are allowed."
                );
            }

            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $type);
        }

        $stmt->execute();
        return $stmt;
    }

    /**
     * Executes an SQL query and returns a single row as an associative array.
     * 
     * @param string $query The SQL query to execute.
     * @param array|null $params An associative array of query parameters. (Optional)
     * @return array|false Returns a single row as an associative array or false if there are no more rows.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws InvalidArgumentException If any of the parameter values are invalid.
     */
    public function fetch(string $query, ?array $params = null): ?array
    {
        $stmt = $this->prepareAndExecuteQuery($query, $params);

        if (!$stmt) {
            return null;
        }

        $fetch = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($fetch === false) {
            return null;
        }

        return $fetch;
    }

    /**
     * Executes an SQL query and returns rows as associative arrays.
     * 
     * @param string $query The SQL query to execute.
     * @param array|null $params An associative array of query parameters. (Optional)
     * @return array Returns an array of rows as associative arrays.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws InvalidArgumentException If the parameter values are invalid.
     */
    public function fetchAll(string $query, ?array $params = null): array
    {
        $stmt = $this->prepareAndExecuteQuery($query, $params);

        if (!$stmt) {
            return [];
        }

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result === false) {
            return [];
        }

        return $result;
    }

    /**
     * Executes an SQL query and returns a single column value from the next row of the result set.
     * 
     * @param string $query The SQL query to execute.
     * @param array|null $params An associative array of query parameters. (Optional)
     * @return string|int|null Returns a single column value or null if there are no more rows.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws InvalidArgumentException If any of the parameter values are invalid.
     */
    public function fetchColumn(string $query, ?array $params = null): string|int|null
    {
        $stmt = $this->prepareAndExecuteQuery($query, $params);

        if (!$stmt) {
            return null;
        }

        $result = $stmt->fetchColumn();

        if ($result === false) {
            return null;
        }

        return $result;
    }

    /**
     *　Executes an SQL query and returns the number of rows affected by the last SQL statement.
     * 
     * @param string $query The SQL query to execute.
     * @param array|null $params An associative array of query parameters. (Optional)
     * @return int Returns the number of rows affected by the last SQL statement.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws InvalidArgumentException If any of the parameter values are invalid.
     */
    public function executeAndGetRowCount(string $query, ?array $params = null): int
    {
        $stmt = $this->prepareAndExecuteQuery($query, $params);

        if (!$stmt) {
            return 0;
        }

        return $stmt->rowCount();
    }

    /**
     *　Executes an SQL query and returns the ID of the last inserted row or sequence value.
     * 
     * @param string $query The SQL query to execute.
     * @param array|null $params An associative array of query parameters. (Optional)
     * @param string $name Name of the sequence object from which the ID should be returned. (Optional)
     * @return int|null If a sequence name was not specified for the name parameter, 
     *  PDO::lastInsertId returns a string representing the row ID of the last row that was inserted into the database.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws InvalidArgumentException If any of the parameter values are invalid.
     */
    public function executeAndGetLastInsertId(string $query, ?array $params = null, ?string $name = null): ?int
    {
        $stmt = $this->prepareAndExecuteQuery($query, $params);

        if (!$stmt) {
            return null;
        }

        $lastInsertId = $this->pdo->lastInsertId($name);

        if ($lastInsertId === false) {
            return null;
        }

        return (int) $lastInsertId;
    }

    /**
     * Executes a LIKE search query and returns a PDOStatement object with bound values.
     * 
     * @param callable $query A function that returns a string representing the SQL query. 
     * * ***Example:*** fn ($where) => "SELECT * FROM table {$where} LIMIT :offset, :limit"
     * 
     * @param callable $whereClauseQuery A function that returns a string representing the WHERE clause.
     * Use ":keyword{$i}" as the placeholder for the binding value.
     * * ***Example:*** fn ($i) => "(title LIKE :keyword{$i} OR text LIKE :keyword{$i})"
     * 
     * @param string $keyword The keyword(s) to search for.
     * * ***Example:*** $keyword = 'Split keywords by whitespace and search with LIKE';
     * 
     * @param array|null $params An associative array of query parameters. (Optional)
     * @return PDOStatement|false Returns a PDOStatement object containing the results of the query, or false on failure.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws LogicException If any of the given callbacks are invalid.
     * @throws InvalidArgumentException If any of the parameter values are invalid.
     */
    public function prepareAndExecuteLikeSearchQuery(callable $query, callable $whereClauseQuery, string $keyword, ?array $params = null): PDOStatement|false
    {
        $convertedKeyword = preg_replace('/　/u', ' ', mb_convert_encoding($keyword, 'UTF-8', 'auto'));
        $keywords = explode(' ', $convertedKeyword);

        $whereClause = 'WHERE ';
        for ($i = 0, $size = count($keywords); $i < $size; $i++) {
            if ($i > 0) {
                $whereClause .= ' AND ';
            }

            $whereClauseQueryResult = $whereClauseQuery($i);

            if (!is_string($whereClauseQueryResult)) {
                throw new LogicException('$whereClauseQuery must return a string');
            }

            $whereClause .= $whereClauseQueryResult;
        }

        $queryResult = $query($whereClause);

        if (!is_string($queryResult)) {
            throw new LogicException('$query must return a string');
        }

        $stmt = $this->pdo->prepare($queryResult);

        if (!$stmt) {
            return false;
        }

        foreach ($keywords as $i => $keyword) {
            $stmt->bindValue(":keyword{$i}", "%{$this->escapeLike($keyword)}%", PDO::PARAM_STR);
        }

        if ($params === null) {
            $stmt->execute();
            return $stmt;
        }

        foreach ($params as $key => $value) {
            if (!is_string($value) && !is_numeric($value)) {
                throw new InvalidArgumentException(
                    "Invalid parameter value for key {$key}: only strings and numbers are allowed."
                );
            }

            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $type);
        }

        $stmt->execute();
        return $stmt;
    }

    /**
     * Escapes special characters in a string for use in a MySQL LIKE clause.
     *
     * @param string $value The string to be escaped.
     * @param string $char The escape character to use (defaults to backslash).
     * @return string The escaped string.
     */
    public function escapeLike(string $value, string $char = '\\'): string
    {
        $search  = [$char, '%', '_'];
        $replace = [$char . $char, $char . '%', $char . '_'];

        return str_replace($search, $replace, $value);
    }
}
