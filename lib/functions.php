<?php

declare(strict_types=1);

/**
 * Verify if specified key exists in array and meets specified string conditions.
 *
 * @param array $array Array to be validated
 * @param string $name Key name to be validated
 * @param int|null $max_length Maximum length of string (optional)
 * @param string|null $exact_match String for precise matching (optional)
 * @return bool Result of validation
 */
function validateArrayString(array $array, string $name, ?int $max_length = null, ?string $exact_match = null): bool
{
    $input = $array[$name] ?? null;
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
 * Validates whether the key specified in the array exists and meets the specified numeric conditions
 * 
 * @param array $array The array to validate
 * @param string $name The key name to validate
 * @param int|null $max_num The maximum numeric value (optional)
 * @param int|null $min_num The minimum numeric value (optional)
 * @param int|null $exact_match The numeric value for exact match (optional)
 * @return bool The validation result
 */
function validateArrayNumber(array $array, string $name, ?int $max_num = null, ?int $min_num = null, ?int $exact_match = null): bool
{
    $input = $array[$name] ?? null;
    if (!ctype_digit($input)) {
        return false;
    }

    $number = (int) $input;

    if (!is_null($exact_match) && $number !== $exact_match) {
        return false;
    }

    if (!is_null($min_num) && $number < $min_num) {
        return false;
    }

    if (!is_null($max_num) && $number > $max_num) {
        return false;
    }

    return true;
}
