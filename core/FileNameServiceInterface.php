<?php

namespace Shadow;

/**
 * Interface FileNameServiceInterface
 *
 * The FileNameServiceInterface defines methods for working with file names
 * mapped to JSON properties.
 */
interface FileNameServiceInterface
{
    /**
     * Get the file name mapped to the given property name from JSON data.
     *
     * @param string|null $propertyName [optional] The property name to retrieve the file name for.
     * @param bool        $includeVersionQuery [optional]
     * @return string|null The file name associated with the specified property.
     * 
     * @throws \RuntimeException If invalid JSON element
     */
    public function getFileName(?string $propertyName = null, bool $includeVersionQuery = true): string|array|null;

    /**
     * Increment the version integer associated with a file name
     * corresponding to the given property name. If $propertyName is null,
     * increment the version for all elements.
     *
     * @param string|array|null $propertyName [optional] The property name to update the version for.
     *                                                   If null, increment the version for all elements.
     * 
     * @throws \RuntimeException If invalid JSON element
     */
    public function updateFileVersionIncrement(string|array|null $propertyName = null): void;
}
