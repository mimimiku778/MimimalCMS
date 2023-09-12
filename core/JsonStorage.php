<?php

namespace Shadow;

/**
 * Class JsonStorage
 *
 * Provides functionality to initialize, copy properties to an object, and update a JSON file.
 */
class JsonStorage implements JsonStorageInterface
{
    /** @var array Holds the JSON data. */
    private array $storage;
    /** @var string Stores the filename for the JSON file. */
    private string $filename;

    public function init(string $filename): void
    {
        $this->filename = 'json/' . $filename;

        $jsonData = getStringFromFile($this->filename);
        $this->storage = json_decode($jsonData, true);

        if ($this->storage === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Failed to load JSON file: ' . $this->filename);
        }
    }

    public function copyPropertiesToObject(object $object): object
    {
        foreach ($this->storage as $key => $value) {
            if (!property_exists($object, $key)) {
                throw new \RuntimeException('Property does not exist: ' . $key);
            }

            $object->$key = $value;
        }

        return $object;
    }

    public function updateJsonFileFromObject(object $object): void
    {
        foreach ($object as $key => $value) {
            $this->storage[$key] = $value;
        }

        $json = json_encode($this->storage);
        if ($json === false) {
            throw new \RuntimeException('Failed to encode JSON data.');
        }

        if (!saveStringToFile($this->filename, $json)) {
            throw new \RuntimeException('Failed to save JSON file: ' . $this->filename);
        }
    }
}
