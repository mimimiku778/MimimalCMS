<?php

namespace Shadow;

abstract class AbstoractJsonStorageObject extends \stdClass
{
    private JsonStorage $jsonStorageInstance;

    public function __construct()
    {
        $this->jsonStorageInstance = new JsonStorage;

        $this->jsonStorageInstance
            ->init($this)
            ->copyPropertiesToObject();
    }

    /**
     * Overwrites the JSON file with the values of this object, array, or `null`.
     *
     * @param array|null $values The values to overwrite the JSON file with. If `null`, the values of this instance are used.
     *
     * @throws \RuntimeException If encoding the array to JSON fails or there is an error opening the file or acquiring an exclusive lock.
     */
    public function overwriteJsonFile(?array $values = null)
    {
        $this->jsonStorageInstance
            ->init($this)
            ->updateJsonFileFromObject($values);
    }

    /**
     * Rolls back changes in the JSON file to the initial state when this instance was created.
     * 
     * @throws \RuntimeException If failed to encode JSON data or error opening the file or acquiring an exclusive lock
     */
    public function rollbackJsonFile(): void
    {
        $this->jsonStorageInstance
            ->rollbackJsonFile();
    }
}
