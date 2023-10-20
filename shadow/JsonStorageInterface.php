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
interface JsonStorageInterface
{
    /**
     * Initializes the JSON storage.
     *
     * @param string|object $class The class name or object to be stored.
     * @param ?string $jsonFilePath The path to the JSON file to store the data in. If `null`, the JSON file will be stored in the default JSON storage directory.
     *
     * @return JsonStorageInterface The JSON storage object.
     *
     * @throws \RuntimeException If the JSON file does not exist or cannot be loaded.
     */
    public function init(string|object $class, ?string $jsonFilePath = null): JsonStorageInterface;

    /**
     * Copies the properties of the JSON storage to an object.
     *
     * If the `$object` parameter is `null`, a new instance of the class specified in the JSON storage is created.
     *
     * @param ?object $object The object to copy the properties to. If `null`, a new instance of the class specified in the JSON storage is created.
     *
     * @return object The object with the copied properties.
     */
    public function copyPropertiesToObject(?object $object = null): object;

    /**
     * Updates the JSON file with the values of the provided object, array, or `null`.
     *
     * If the `$values` parameter is `null`, the JSON file is updated with the values of the instance of the class specified in the JSON storage.
     *
     * @param object|array|null $values The values to update the JSON file with. If `null`, the values of the instance of the class specified in the JSON storage are used.
     *
     * @throws \RuntimeException If the `$values` parameter is not an object, array, or `null`.
     */
    public function updateJsonFileFromObject(object|array|null $values = null): void;

    /**
     * Rolls back changes in the JSON file to the initial state when this instance was created.
     * 
     * @throws \RuntimeException If failed to encode JSON data or error opening the file or acquiring an exclusive lock
     */
    public function rollbackJsonFile(): void;

    /**
     * Overwrites the JSON file with the provided array.
     *
     * @param array $array The array to write to the JSON file.
     * @throws \RuntimeException If encoding the array to JSON fails.
     */
    public function overwriteJsonFile(array $array): void;
}
