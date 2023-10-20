<?php

namespace Shadow;

/**
 * Class JsonStorage
 *
 * Provides functionality to initialize, copy properties to an object, and update a JSON file.
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class JsonStorage implements JsonStorageInterface
{
    /** @var array Holds the JSON data. */
    public array $array;
    public string $filePath;
    public string $className;
    public ?object $instance;

    public function init(string|object $class, ?string $jsonFilePath = null): JsonStorageInterface
    {
        if (is_object($class)) {
            $this->instance = $class;
            $this->className = get_class($class);
        } else {
            $this->instance = null;
            $this->className = $class;
        }

        if ($this->className === \App\Config\ConfigJson::class) {
            $jsonFilePath = CONFIG_JSON_FILE_PATH;
        }

        if ($jsonFilePath === null) {
            $fileName = substr($this->className, strrpos($this->className, '\\') + 1);
            $this->filePath = JSON_STORAGE_DIR . '/' . $fileName . '.json';
        } else {
            $this->filePath = $jsonFilePath;
        }

        if (!file_exists($this->filePath)) {
            throw new \RuntimeException('JSON file does not exist: ' . $this->filePath);
        }

        $jsonData = file_get_contents($this->filePath);

        $this->array = json_decode($jsonData, true);
        if ($this->array === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to load JSON file: ' . $this->filePath);
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

        $isStdClass = $object instanceof \stdClass;
        foreach ($this->array as $key => $value) {
            if (!$isStdClass && !property_exists($object, $key)) {
                continue;
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
        $isStdClass = in_array('stdClass', class_parents($this->className));

        foreach ($values as $key => $value) {
            if (!$isStdClass && !isset($array[$key])) {
                continue;
            }

            $array[$key] = $value;
        }

        $this->overwriteJsonFile($array);
    }

    public function rollbackJsonFile(): void
    {
        $this->overwriteJsonFile($this->array);
    }

    public function overwriteJsonFile(array $array): void
    {
        $json = json_encode($array);
        if ($json === false) {
            throw new \RuntimeException('Failed to encode JSON data.');
        }

        $this->writeTextFileWithExclusiveLock($this->filePath, $json);
    }

    /**
     * Write to a text file with exclusive lock and optional new content.
     *
     * @param string $filePath The path of the file to read or write.
     * @param string $newContent The new content to write.
     * @throws \RuntimeException If there is an error opening the file or acquiring an exclusive lock.
     */
    protected function writeTextFileWithExclusiveLock(string $filePath, string $newContent): void
    {
        $mode = $newContent === null ? 'r' : 'w'; // Use 'r' for reading, 'w' for writing

        // Open the file for reading or writing
        $fileHandle = fopen($filePath, $mode);

        if (!$fileHandle) {
            throw new \RuntimeException("Failed to open the file: $filePath");
        }

        try {
            if (flock($fileHandle, LOCK_EX)) {
                // If new content is provided, write it and return null
                ftruncate($fileHandle, 0); // Clear the file
                fwrite($fileHandle, $newContent);
                fflush($fileHandle);
            } else {
                throw new \RuntimeException('Failed to acquire an exclusive lock.');
            }
        } finally {
            fclose($fileHandle); // Always close the file handle, even on exceptions
        }
    }
}
