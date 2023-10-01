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
    private function setValuesFromAssocArray(array $data): void
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
        $query = $this->insertQueryBuilder($tableName ?? getClassSimpleName($this::class), $params);

        return DB::executeAndGetLastInsertId($query, $params);
    }

    private function insertQueryBuilder(string $tableName, array $params): string
    {
        $columns = implode(', ', array_keys($params));
        $placeholders = ':' . implode(', :', array_keys($params));

        $query = "INSERT INTO {$tableName} ({$columns}) VALUES ({$placeholders})";

        return $query;
    }
}
