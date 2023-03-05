<?php

declare(strict_types=1);

/**
 * A PDO wrapper class for MySQL databases
 */
class SqlConnect
{
    private const HOST = ''; // TODO: Specify the hostname for the MySQL database
    private const DB_NAME = ''; // TODO: Specify the name of the MySQL database
    private const USER_NAME = ''; // TODO: Specify the username for the MySQL database
    private const PASSWORD = ''; // TODO: Specify the password for the MySQL database

    public PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO(
            'mysql:host=' . self::HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4',
            self::USER_NAME,
            self::PASSWORD
        );

        // Enable PDO to throw exceptions on error
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Executes an SQL query and returns a PDOStatement object with bound values.
     *
     * @param string $query The SQL query to execute.
     * @param array|null $params An optional associative array of query parameters.
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
     *　Executes an SQL query and returns the number of rows affected by the last SQL statement.
     * 
     * @param string $query The SQL query to execute.
     * @param array|null $params Associative array of query parameters. (Optional)
     * @return int Returns the number of rows affected by the last SQL statement.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws InvalidArgumentException If any of the parameter values are invalid.
     */
    public function getRowCount(string $query, ?array $params = null): int
    {
        $stmt = $this->prepareAndExecuteQuery($query, $params);

        if (!$stmt) {
            return 0;
        }

        return $stmt->rowCount();
    }

    /**
     * Executes an SQL query and returns a single column value from the next row of the result set.
     * 
     * @param string $query The SQL query to execute.
     * @param array|null $params Associative array of query parameters. (Optional)
     * @return mixed Returns a single column value or false if there are no more rows.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws InvalidArgumentException If any of the parameter values are invalid.
     */
    public function fetchColumn(string $query, ?array $params = null): string|int|false
    {
        $stmt = $this->prepareAndExecuteQuery($query, $params);

        if (!$stmt) {
            return false;
        }

        return $stmt->fetchColumn();
    }

    /**
     * Executes an SQL query and returns a single row as an associative array.
     * 
     * @param string $query The SQL query to execute.
     * @param array|null $params Associative array of query parameters. (Optional)
     * @return array|false Returns a single row as an associative array or false if there are no more rows.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws InvalidArgumentException If any of the parameter values are invalid.
     */
    public function fetch(string $query, ?array $params = null): array|false
    {
        $stmt = $this->prepareAndExecuteQuery($query, $params);

        if (!$stmt) {
            return false;
        }

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Executes an SQL query and returns rows as associative arrays.
     * 
     * @param string $query The SQL query to execute.
     * @param array|null $params Optional. Associative array of query parameters.
     * @return array|false An array of rows as associative arrays, or false if there are no more rows.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws InvalidArgumentException If the parameter values are invalid.
     */
    public function fetchAll(string $query, ?array $params = null): array|false
    {
        $stmt = $this->prepareAndExecuteQuery($query, $params);

        if (!$stmt) {
            return false;
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
