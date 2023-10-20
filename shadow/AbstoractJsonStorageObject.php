<?php

namespace Shadow;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
abstract class AbstoractJsonStorageObject extends \stdClass
{
    protected JsonStorage $jsonStorageInstance;

    public function __construct()
    {
        $this->jsonStorageInstance = new JsonStorage;

        $this->jsonStorageInstance
            ->init($this)
            ->copyPropertiesToObject();
    }

    /**
     * Updates the JSON file with the values of this object, array, or `null`.
     *
     * @param array|null $values The values to update the JSON file with. If `null`, the values of this instance are used.
     *
     * @throws \RuntimeException If encoding the array to JSON fails or there is an error opening the file or acquiring an exclusive lock.
     */
    public function updateJsonFile(?array $values = null)
    {
        $this->jsonStorageInstance
            ->updateJsonFileFromObject($values);
    }

    /**
     * Overwrites the JSON file with the provided array.
     *
     * @param array $array The array to write to the JSON file.
     * @throws \RuntimeException If encoding the array to JSON fails.
     */
    public function overwriteJsonFile(array $array): void
    {
        $this->jsonStorageInstance
            ->overwriteJsonFile($array);
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
