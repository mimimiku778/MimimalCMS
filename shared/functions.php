<?php

declare(strict_types=1);

/**
 * Simple autoloader
 * 
 * It's maybe safe to use this autoloader in conjunction with Composer's autoloader.
 * 
 * NOTE: If the class file is located in the root directory path, namespace is not required.
 *       If the class file is located in a subdirectory, namespace must be used to match the class and file names correctly.
 */
function simpleAutoloader(string $className)
{
    // Match the subdirectory name with the namespace name.
    $classFile = str_replace('\\', '/', ltrim($className, '\\')) . '.php';

    // Search for the class file in each root directory.
    foreach (AUTOLOADER_ROOT_DIRECTORY_NAMES as $rootDirectoryName) {
        $classFilePath = __DIR__ . '/' . $rootDirectoryName . '/' . $classFile;
        if (file_exists($classFilePath)) {
            require_once $classFilePath;
        }
    }
}

/**
 * Validate whether the specified key exists in the array and meets the specified string conditions.
 * 
 * @param array $array The array to be validated
 * @param string $key The key to be validated
 * @param int|null $max_length The maximum length of the string (optional)
 * @param string|null $exact_match The string for exact matching (optional)
 * @return bool The result of validation
 */
function validateKeyStr(
    array $array,
    string $key,
    ?int $max_length = null,
    ?string $exact_match = null
): bool {
    $input = $array[$key] ?? null;
    if (!is_string($input) || empty(trim($input))) {
        return false;
    }

    if (!is_null($exact_match)) {
        return $input === $exact_match;
    }

    if (!is_null($max_length) && mb_strlen($input) > $max_length) {
        return false;
    }

    return true;
}

/**
 * Validate whether the specified key exists in the array and meets the specified numeric conditions.
 * 
 * @param array $array The array to be validated
 * @param string $key The key to be validated
 * @param int|null $max_value The maximum numeric value (optional)
 * @param int|null $min_value The minimum numeric value (optional)
 * @param int|null $exact_match The numeric value for exact match (optional)
 * @return bool The result of validation
 */
function validateKeyNum(
    array $array,
    string $key,
    ?int $max_value = null,
    ?int $min_value = null,
    ?int $exact_match = null
): bool {
    $input = $array[$key] ?? null;
    if (!ctype_digit($input)) {
        return false;
    }

    $number = (int) $input;

    if (!is_null($exact_match) && $number !== $exact_match) {
        return false;
    }

    if (!is_null($min_value) && $number < $min_value) {
        return false;
    }

    if (!is_null($max_value) && $number > $max_value) {
        return false;
    }

    return true;
}
