<?php

declare(strict_types=1);

/**
 * PDO wrapper class
 */
class SqlConnect
{
    private const HOST = '';
    private const DB_NAME = '';
    private const USER_NAME = '';
    private const PASSWORD = '';

    public PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO(
            'mysql:host=' . self::HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4',
            self::USER_NAME,
            self::PASSWORD
        );

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
    public function prepareAndExecuteQuery(string $query, ?array $params = null): PDOStatement
    {
        $statement = $this->pdo->prepare($query);

        if ($params === null) {
            $statement->execute();
            return $statement;
        }

        foreach ($params as $key => $value) {
            if (!is_string($value) && !is_numeric($value)) {
                throw new InvalidArgumentException("Invalid parameter value for key {$key}: only strings and numbers are allowed.");
            }

            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $statement->bindValue($key, $value, $type);
        }

        $statement->execute();
        return $statement;
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
        try {
            return $this->prepareAndExecuteQuery($query, $params)->rowCount();
        } catch (PDOException $e) {
            throw $e;
            return 0;
        }
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
    public function fetchColumn(string $query, ?array $params = null): mixed
    {
        try {
            return $this->prepareAndExecuteQuery($query, $params)->fetchColumn();
        } catch (PDOException $e) {
            throw $e;
            return false;
        }
    }


}
