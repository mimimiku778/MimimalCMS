<?php

namespace Shadow;

use Shadow\JsonStorage;

class FileNameService extends \stdClass implements FileNameServiceInterface
{
    public function getFileName(?string $propertyName = null, bool $includeVersionQuery = true): string|array|null
    {
        if ($propertyName === null) {
            $fileNames = [];
            foreach(get_object_vars($this) as $name => $property) {
                $fileNames[] = $this->generateFileName($property, $name, $includeVersionQuery);
            }

            return $fileNames;
        }

        return $this->generateFileName($this->$propertyName ?? null, $propertyName, $includeVersionQuery);
    }

    private function generateFileName(mixed $element, string $propertyName, bool $includeVersionQuery)
    {
        if (isset($element[0], $element[1])) {
            if (!is_string($element[0]) || !is_int($element[1])) {
                throw new \RuntimeException('Invalid JSON property: ' . $propertyName);
            }

            if ($includeVersionQuery) {
                return $element[0] . '?v=' . (string)$element[1];
            } else {
                return $element[0];
            }
        }

        if (isset($element[0])) {
            if (!is_string($element[0])) {
                throw new \RuntimeException('Invalid JSON property: ' . $propertyName);
            }

            return $element[0];
        }

        if (is_string($element)) {
            return $element;
        }

        return null;
    }

    public function updateFileVersionIncrement(string|array|null $propertyName = null): void
    {
        if (is_string($propertyName)) {
            $array = $this->updateFileVersionIncrementFromPropertyName($propertyName);
        } else {
            $array = [];
            foreach ($propertyName ?? array_keys(get_object_vars($this)) as $prop) {
                $array += $this->updateFileVersionIncrementFromPropertyName($prop);
            }
        }

        (new JsonStorage)->init(self::class)
            ->updateJsonFileFromObject($array);
    }

    private function updateFileVersionIncrementFromPropertyName(string $propertyName): array
    {
        if (isset($this->$propertyName[1])) {
            if (!is_int($this->$propertyName[1])) {
                throw new \RuntimeException('Invalid JSON property: ' . $propertyName);
            }

            return [$propertyName => [$this->$propertyName[0], $this->$propertyName[1] + 1]];
        } else {
            throw new \RuntimeException('Second array element in JSON property is missing: ' . $propertyName);
        }
    }
}
