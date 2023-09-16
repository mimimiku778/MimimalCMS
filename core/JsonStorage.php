<?php

namespace Shadow;

use App\Config\JsonStorageClassMap;

/**
 * Class JsonStorage
 *
 * Provides functionality to initialize, copy properties to an object, and update a JSON file.
 */
class JsonStorage implements JsonStorageInterface
{
    /** @var array Holds the JSON data. */
    public array $array;
    private string $filePath;
    private string $className;
    private ?object $instance;

    public function init(string|object $class): JsonStorageInterface
    {
        if (is_object($class)) {
            $this->instance = $class;
            $this->className = get_class($class);
        } else {
            $this->instance = null;
            $this->className = $class;
        }

        $fileName = JsonStorageClassMap::$map[$this->className] ?? null;
        if ($fileName === null) {
            $fileName = substr($this->className, strrpos($this->className, '\\') + 1);
        }

        $this->filePath = __DIR__ . '/../storage/json/' . $fileName;
        if (!file_exists($this->filePath)) {
            throw new \RuntimeException('JSON file does not exist: ' . $fileName);
        }

        $jsonData = file_get_contents($this->filePath);

        $this->array = json_decode($jsonData, true);
        if ($this->array === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to load JSON file: ' . $fileName);
        }

        return $this;
    }

    public function copyPropertiesToObject(?object $object = null): object
    {
        if ($this->instance !== null) {
            $object = $object ?? $this->instance;
        } else {
            $object = $object ?? new $this->className;
        }

        foreach ($this->array as $key => $value) {
            if (!property_exists($object, $key)) {
                throw new \RuntimeException('Property does not exist: ' . $key);
            }

            $object->$key = $value;
        }

        return $object;
    }

    public function updateJsonFileFromObject(object|array|null $values = null): void
    {
        if ($values === null) {
            if (!isset($this->instance)) {
                throw new \RuntimeException('No object specified to update');
            }

            $values = $this->instance;
        }

        $array = $this->array;
        foreach ($values as $key => $value) {
            if (!isset($array[$key])) {
                throw new \RuntimeException('Property does not exist: ' . $key);
            }

            $array[$key] = $value;
        }

        $this->overwriteJsonFile($array);
    }

    public function rollbackJsonFile(): void
    {
        $this->overwriteJsonFile($this->array);
    }

    /**
     * Overwrites the JSON file with the provided array.
     *
     * @param array $array The array to write to the JSON file.
     * @throws \RuntimeException If encoding the array to JSON fails.
     * @throws \LogicException If there is an error opening the file or acquiring an exclusive lock.
     */
    private function overwriteJsonFile(array $array): void
    {
        $json = json_encode($array);
        if ($json === false) {
            throw new \RuntimeException('Failed to encode JSON data.');
        }

        readWriteTextFileWithExclusiveLock($this->filePath, $json);
    }
}
