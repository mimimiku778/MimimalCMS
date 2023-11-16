<?php

declare(strict_types=1);

namespace App\Models;

use Shadow\DB;

/**
 * AbstractActiveRecord is the base class for active record models.
 */
class ActiveRecord extends \stdClass implements ActiveRecordInterface
{
    public static function findRecords(
        string $query,
        ?array $params = null
    ): array {
        $stmt = DB::execute($query, $params);
        return $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, static::class);
    }

    public static function searchRecords(
        callable $query,
        callable $whereClauseQuery,
        string $keyword,
        ?array $params = null,
        ?array $affix = ['%', '%'],
        string $whereClausePlaceholder = 'keyword',
    ): array {
        return DB::executeLikeSearchQuery(
            $query,
            $whereClauseQuery,
            $keyword,
            $params,
            $affix,
            \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE,
            [static::class],
            $whereClausePlaceholder,
        );
    }

    public function find(string $query, ?array $params = null): bool
    {
        $result = DB::fetch($query, $params);
        if (!$result) {
            return false;
        }

        $this->setValuesFromAssocArray($result);
        return true;
    }

    /**
     * Set object properties from an associative array.
     *
     * @param array $data      An associative array of property values.
     */
    protected function setValuesFromAssocArray(array $data): void
    {
        foreach ($data as $propertyName => $propertyValue) {
            if ($propertyValue === null) {
                continue;
            }

            $this->$propertyName = $propertyValue;
        }
    }

    public function insert(?string $tableName = null): int
    {
        $params = get_object_vars($this);
        $query = $this->buildInsertQuery($tableName ?? getClassSimpleName($this::class), $params);

        return DB::executeAndGetLastInsertId($query, $params);
    }

    protected function buildInsertQuery(string $tableName, array $params): string
    {
        $keys = array_keys($params);
        $columns = implode(',', $keys);
        $placeholders = ':' . implode(', :', $keys);

        $query = "INSERT INTO {$tableName} ({$columns}) VALUES ({$placeholders})";

        return $query;
    }

    public function insertUpdate(?string $tableName = null): int
    {
        $params = get_object_vars($this);
        $query = $this->buildInsertUpdateQuery($tableName ?? getClassSimpleName($this::class), $params);

        return DB::executeAndGetLastInsertId($query, $params);
    }

    /**
     * Builds the query for inserting or updating a record in the specified table.
     *
     * @param string $tableName The name of the table.
     * @param array $params The associative array of column names and values.
     * @return string The constructed SQL query.
     */
    protected function buildInsertUpdateQuery(string $tableName, array $params): string
    {
        $keys = array_keys($params);
        $columns = implode(',', $keys);
        $placeholders = ':' . implode(',:', $keys);

        $updateStatments = implode(',', array_map(fn ($column) => "{$column} = VALUES({$column})", $keys));

        return "INSERT INTO {$tableName} ({$columns}) VALUES ({$placeholders}) ON DUPLICATE KEY UPDATE {$updateStatments}";
    }

    public function update(array|string $where, ?string $tableName = null): int
    {
        [$query, $params] = $this->buildUpdateQuery($tableName ?? getClassSimpleName($this::class), get_object_vars($this), $where);

        return DB::executeAndGetLastInsertId($query, $params);
    }

    /**
     * Builds the query for updating a record in the specified table based on the given WHERE clause.
     *
     * @param string $tableName The name of the table.
     * @param array $params The associative array of column names and values to be updated.
     * @param array|string $where The WHERE clause to identify the record to be updated. If an array, it should be associative.
     * 
     * @return array An array containing the constructed SQL query and the corresponding parameters.
     */
    protected function buildUpdateQuery(string $tableName, array $params, array|string $where): array
    {
        $keys = array_keys($params);
        $updateStatements = implode(',', array_map(fn ($column) => "{$column} = :{$column}", $keys));

        if (is_array($where)) {
            if (array_is_list($where)) {
                throw new \LogicException('Where clause argument requires an associative array');
            }

            $result = [];
            foreach (array_keys($where) as $i => $key) {
                $placeholder = "whereClausePlaceholder{$i}";
                $params[$placeholder] = $where[$key];
                $result[] = "{$key} = :{$placeholder}";
            }

            $where = implode('AND', $result);
        }

        return ["UPDATE {$tableName} SET {$updateStatements} WHERE {$where}", $params];
    }
}
