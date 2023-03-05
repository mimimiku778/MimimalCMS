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
}
