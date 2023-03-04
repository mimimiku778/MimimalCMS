<?php

declare(strict_types=1);

/**
 * PDOをラップしてSQLの操作を行うクラス
 */
class SqlConnect
{
    private const HOST = 'localhost';
    private const DB_NAME = '';
    private const USER_NAME = '';
    private const PASSWORD = '';

    public PDO $dbh;

    public function __construct()
    {
        $this->dbh = new PDO(
            'mysql:host=' . self::HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4',
            self::USER_NAME,
            self::PASSWORD
        );
    }

    /**
     * SQLクエリを実行し、PDOStatementオブジェクトを返す
     * 
     * @param string $query
     * @param array|null $params
     * @return PDOStatement|false
     */
    public function execute(string $query, array $params = null): PDOStatement|false
    {
        $stmt = $this->dbh->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * 最後に挿入されたIDを返す
     * 
     * @param string $query
     * @param array|null $params
     * @return int
     */
    public function getLastInsertId(string $query, array $params = null): int
    {
        $this->execute($query, $params);
        return (int) $this->dbh->lastInsertId();
    }

    /**
     * 行数を返す
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
     * 全ての結果を連想配列で取得する
     * 
     * @param string $query
     * @param array|null $params
     * @return array|false
     */
    public function fetchAll(string $query, array $params = null): array|false
    {
        return $this->execute($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 一つのカラム値を返す
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
     * 一つの行を連想配列で取得する
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
     * 文字列をハッシュ化する
     * 
     * @param string $string
     * @return string
     */
    public function hash(string $string): string
    {
        return hash('sha3-256', $string);
    }
}
