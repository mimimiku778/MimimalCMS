<?php

namespace Shadow;

/**
 * Class JsonStorage
 *
 * Provides functionality to initialize, copy properties to an object, and update a JSON file.
 */
interface JsonStorageInterface
{
    /**
     * Initializes the JsonStorage instance.
     *
     * @param string|object $class The class name or instance to be used.
     * @param string|null $jsonFilePath [optional]
     * @return JsonStorageInterface The initialized JsonStorage instance.
     * @throws \RuntimeException If no file name is defined in JsonStorageClassMap or if the JSON file does not exist or fails to load.
     */
    public function init(string|object $class, ?string $jsonFilePath = null): JsonStorageInterface;

    /**
     * Copies properties from the stored array to the provided object.
     *
     * @param object|null $object The object to copy properties to. If not provided, a new instance of the stored class will be created.
     * @return object The object with copied properties.
     */
    public function copyPropertiesToObject(?object $object = null): object;

    /**
     * Updates the JSON file from the provided values or from the stored instance.
     *
     * @param object|array|null $values The values to update. If not provided, the stored instance will be used.
     * @throws \RuntimeException If there is an error opening the file or acquiring an exclusive lock.ect is specified to update or if a property does not exist in the stored array.
     */
    public function updateJsonFileFromObject(object|array|null $values = null): void;

    /**
     * Rolls back changes in the JSON file to the initial state when this instance was created.
     * 
     * @throws \RuntimeException If failed to encode JSON data or error opening the file or acquiring an exclusive lock
     */
    public function rollbackJsonFile(): void;
}
