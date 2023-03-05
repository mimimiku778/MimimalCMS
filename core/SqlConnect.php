<?php

declare(strict_types=1);

/**
 * Create a PDO wrapper class for SQL manipulation.
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
    }

    /**
     * Executes SQL query and returns PDOStatement object with bound values.
     *
     * @param string $query The SQL query to execute.
     * @param array|null $intParams Associative array of integer query parameters. (Optional)
     * @param array|null $strParams Associative array of string query parameters. (Optional)
     * @return PDOStatement Returns PDOStatement object containing results of query. Returns false on failure.
     * @throws PDOException Thrown if prepare operation fails.
     */
    public function executeQuery(string $query, ?array $intParams = null, ?array $strParams = null): PDOStatement
    {
        $statement = $this->pdo->prepare($query);

        if ($intParams !== null) {
            foreach ($intParams as $key => $value) {
                $statement->bindParam($key, $value, PDO::PARAM_INT);
            }
        }

        if ($strParams !== null) {
            foreach ($strParams as $key => $value) {
                $statement->bindParam($key, $value, PDO::PARAM_STR);
            }
        }

        return $statement;
    }

    /**
     * Execute SQL query and return the row count
     * 
     * @param string $query
     * @param array|null $params
     * @return int
     */
    public function getRowCount(string $query, array $params = null): int
    {
        return (int) $this->execute($query, $params)->rowCount();
    }

    /**
     * Execute SQL query and return a single column value
     * 
     * @param string $query
     * @param array|null $params
     * @return int|string|false
     */
    public function fetchColumn(string $query, array $params = null): int|string|false
    {
        return $this->execute($query, $params)->fetchColumn();
    }

    /**
     * Execute SQL query and return a single row as an associative array
     * 
     * @param string $query
     * @param array|null $params
     * @return array|false
     */
    public function fetch(string $query, array $params = null): array|false
    {
        return $this->execute($query, $params)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Hash a string
     * 
     * @param string $string
     * @return string
     */
    public function hash(string $string): string
    {
        return hash('sha3-256', $string);
    }
}
