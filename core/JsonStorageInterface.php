<?php

namespace Shadow;

/**
 * Interface JsonStorageInterface
 *
 * Provides methods to initialize, copy properties to an object, and update a JSON file.
 */
interface JsonStorageInterface
{
    /**
     * Initializes the JsonStorage object with data from a JSON file.
     *
     * @param string $filename The name of the JSON file.
     *
     * @throws RuntimeException if the JSON file cannot be loaded.
     */
    public function init(string $filename): void;

    /**
     * Copies properties from the storage array to the provided object.
     *
     * @param object $object The object to which properties will be copied.
     *
     * @return object The object with copied properties.
     *
     * @throws RuntimeException if a property in the storage array does not exist in the object.
     */
    public function copyPropertiesToObject(object $object): object;

    /**
     * Updates the JSON file with properties from the provided object.
     *
     * @param object $object The object containing properties to update the JSON file.
     *
     * @throws RuntimeException if the JSON file cannot be saved.
     */
    public function updateJsonFileFromObject(object $object): void;
}
