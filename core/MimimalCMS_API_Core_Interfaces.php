<?php

namespace Shadow;

interface DBInterface
{
    /**
     * Executes an SQL query and returns a PDOStatement object with bound values.
     *
     * @param string     $query  The SQL query to execute.
     *                   * *Example:* `'SELECT * FROM table WHERE category = :category LIMIT :offset, :limit'`
     * 
     * @param array|null $params [optional] An associative array of query parameters.
     *                           InvalidArgumentException will be thrown if any of the array values are not strings or numbers.
     *                   * *Example:* `['category' => 'foods', 'limit' => 20, 'offset' => 60]`
     * 
     * @return PDOStatement Returns a PDOStatement object containing the results of the query, or false.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws InvalidArgumentException If any of the array values are not strings or numbers.
     */
    public static function execute(string $query, ?array $params = null): \PDOStatement;

    /**
     * Executes an SQL query and returns a single row as an associative array.
     * 
     * @param string     $query  The SQL query to execute.
     *                   * *Example:* `'SELECT * FROM table WHERE id = :id'`
     * 
     * @param array|null $params [optional] An associative array of query parameters.
     *                           InvalidArgumentException will be thrown if any of the array values are not strings or numbers.
     *                   * *Example:* `['id' => 10]`
     * 
     * @return array|false Returns a single row as an associative array or false if no rows.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws InvalidArgumentException If any of the array values are not strings or numbers.
     */
    public static function fetch(string $query, ?array $params = null): array|false;

    /**
     * Executes an SQL query and returns rows as associative arrays.
     * 
     * @param string     $query  The SQL query to execute.
     *                   * *Example:* `'SELECT * FROM table WHERE category = :category LIMIT :offset, :limit'`
     * 
     * @param array|null $params [optional] An associative array of query parameters.
     *                           InvalidArgumentException will be thrown if any of the array values are not strings or numbers.
     *                   * *Example:* `['category' => 'foods', 'limit' => 20, 'offset' => 60]`
     * 
     * @return array An empty array is returned if there are zero results to fetch.
     * 
     * @throws PDOException If an error occurs during the query execution.
     * @throws InvalidArgumentException If any of the array values are not strings or numbers.
     */
    public static function fetchAll(string $query, ?array $params = null): array;
}

interface ImageStoreIntercase
{
    /**
     * Set parameters for storing the image.
     *
     * @param int|null $quality   The quality of the stored image (0-100).
     * @param int|null $maxWidth  The maximum width of the stored image. Set to null or 0 for no maximum width.
     * @param int|null $maxHeight The maximum height of the stored image. Set to null or 0 for no maximum height.
     * 
     * @throws InvalidArgumentException If any of the given parameters are invalid.
     */
    public function setParams(?int $maxWidth = null, ?int $maxHeight = null, int $quality = 80);

    /**
     * Returns a GD image resource or false if image processing failed.
     *
     * @param array $file An array containing information about the uploaded file.
     *                    Must have the "tmp_name" key which contains the path to the uploaded file.
     *
     * @return GdImage|false A GD image resource or false if image processing failed.
     *
     * @throws InvalidArgumentException If the "tmp_name" key is not found in the input array.
     */
    public function getGdImage(array $file): object|false;

    /**
     * Store the given image file to the specified path with the given file name and image type.
     *
     * @param array $file An array containing information about the uploaded file.  
     *                    Must have the "tmp_name" key which contains the path to the uploaded file.
     * 
     * @param string      $path      The path to save the image file.
     * @param ImageType   $imageType The image type to save as, defaults to ImageType::WEBP.
     * @param string|null $fileName  The file name to save as, defaults to a unique md5 hash.
     * 
     * @return bool Whether the image was successfully stored.
     * 
     * @throws InvalidArgumentException If the provided arguments are invalid.
     */
    public function store(array $file, string $path, \ImageType $imageType = \ImageType::WEBP, ?string $fileName = null): bool;

    /**
     * Get the error code, or null if it is not set.
     *
     * @return int|null The error code, or null if it is not set.
     */
    public function getErrorCode(): ?int;

    /**
     * Get the error message, or null if it is not set.
     *
     * @return string|null The error message, or null if it is not set.
     */
    public function getErrorMessage(): ?string;

    /**
     * Get the file name, or null if it is not set.
     *
     * @return string|null The file name, or null if it is not set.
     */
    public function getFileName(): ?string;
}

interface StringCryptorInterface
{
    /**
     * Set a private key. If this method is not used, the key set in `StringCryptorConfig` will be used.
     * 
     * **Example of key generation**
     * 
     * * Generate a key from an arbitrary password
     * `$key = hash('sha256', 'YOUR_PASSWORD');`
     * 
     * * Generate a random key
     * `$key = bin2hex(random_bytes(32));`
     * 
     * @param string $hkdfKey    The HKDF key to use. If null, the default key will be used.
     * @param string $opensslKey The OpenSSL key to use. If null, the default key will be used.
     */
    public function setSeacretKey(string $hkdfKey, string $opensslKey);

    /**
     * Encrypts a string to a Base64 URL encoded string using AES-CBC and obtains the hash of the encrypted string using HKDF.
     * 
     * @param string $string The string to be encrypted and hashed.
     *               * *Example:* `'Hello World'`
     * 
     * @return string Returns a Base64 URL encoded string containing encrypted text and its hash, 
     *                decryptable using `verifyHashAndDecrypt()` method only.
     *  * *Example:* 
     *  `'cnJHV0JVR2dRSFRyeE9JU0VzQVRndz09QE1LMGM4VjBzV25yeXhBd1MzVmR0RFE9PSNMd1YwMHVWc3FQVHNXWnZUSG0raXU4VnJhWld1NGFCZ3R2ZmtWUT09QFIxMk9yK0FVSEJZajJ5NDdzRE5CZlE9PQ'`
     * 
     * @throws LogicException If the encryption fails.
     */
    public function encryptAndHashString(string $string): string;

    /**
     * Verifies the hash of a Base64 URL encoded encrypted string and returns the decrypted string.
     * 
     * @param string $encryptedString A Base64 URL encoded string that is a concatenation of the encrypted string and its hash, 
     *                                generated by `encryptAndHashString()` method only.
     * * *Example:*
     *  `'cnJHV0JVR2dRSFRyeE9JU0VzQVRndz09QE1LMGM4VjBzV25yeXhBd1MzVmR0RFE9PSNMd1YwMHVWc3FQVHNXWnZUSG0raXU4VnJhWld1NGFCZ3R2ZmtWUT09QFIxMk9yK0FVSEJZajJ5NDdzRE5CZlE9PQ'`
     * 
     * @return string Returns the decrypted string if the hash is valid.
     *                * *Example:* `'Hello World'`
     * 
     * @throws RuntimeException If the Base64 URL encoded encrypted string is invalid.
     * @throws LogicException If the hash is valid but decryption fails.
     */
    public function verifyHashAndDecrypt(string $encryptedString): string;

    /**
     * Encrypts a string to a Base64 URL encoded string with hashed validity period using AES-CBC and HKDF.
     * 
     * @param string $string  The string to be encrypted and hashed.
     *               * *Example:* `'Hello World'`
     * 
     * @param int    $expires The validity period in Unix time should be 10 digits. The encrypted and hashed string can only be decrypted within this period.
     *               * *Example:* `time() + (7 * 24 * 60 * 60)` `1678970283`
     * 
     * @return string Returns a Base64 URL encoded string containing encrypted text, its hash and the validity period, 
     *                decryptable using `verifyHashAndDecryptWithValidity()` method only.
     * * *Example:* 
     *  `'1678970283dbGRqVXp4L0dDbXNjNUNPazEwakI5UT09QHRkUTVxYXNNNk5nZmhqTWZiWjNYWnc9PSM1Y2hPNFpRVFZqbmJJQno3LzgxMEFzVzY1UkdNVlVRdU9xanR0Zz09QHVLM29vRXR2NG9kY295bnVWYStuSnc9PQ'`
     * 
     * @throws InvalidArgumentException If the validity period in Unix time is before now, or not 10 digits.
     * @throws LogicException If the encryption fails.
     */
    public function encryptAndHashWithValidity(string $string, int $expires): string;

    /**
     * Verifies the hash and validity period of a Base64 URL encoded encrypted string and returns the decrypted string if the validity period is still valid.
     * 
     * @param string $encryptedString A Base64 URL encoded string that is a concatenation of the encrypted string and its hash, 
     *                                generated by `encryptAndHashWithValidity()` method only.
     * * *Example:*
     *  `'1678970283dbGRqVXp4L0dDbXNjNUNPazEwakI5UT09QHRkUTVxYXNNNk5nZmhqTWZiWjNYWnc9PSM1Y2hPNFpRVFZqbmJJQno3LzgxMEFzVzY1UkdNVlVRdU9xanR0Zz09QHVLM29vRXR2NG9kY295bnVWYStuSnc9PQ'`
     * 
     * @return array|false Returns an array contains the decrypted string and the expiry time, if the hash is valid or false if expires.
     *                     * *Example:* `[1678970283, 'Hello World']`
     * 
     * @throws RuntimeException If the Base64 URL encoded encrypted string is invalid.
     * @throws LogicException If the hash is valid but decryption fails.
     */
    public function verifyHashAndDecryptWithValidity(string $encryptedString): array|false;

    /**
     * Hashes a string using HKDF with SHA3-224 and returns a string in the format of `hash`@`salt`.
     *
     * @param string $string The string to hash.
     *               * *Example:* `'Hello World'`
     * 
     * @return string The hashed string with salt.
     * * *Example:* `'2VrazmQuSS0alphnIsMXGBp2LEzmiCpcxMJ/Mg==@0VJpOVEUTZqDG8J4DGlRqA=='`
     */
    public function hashHkdf(string $string): string;

    /**
     * Compares a string with a HKDF hashed string with salt in the format of `hash`@`salt`.
     *
     * @param string $string The string to compare.
     *               * *Example:* `'Hello World'`
     * 
     * @param string $hashedString The HKDF hashed string with salt in the format of `hash`@`salt`.
     * * *Example:* `'2VrazmQuSS0alphnIsMXGBp2LEzmiCpcxMJ/Mg==@0VJpOVEUTZqDG8J4DGlRqA=='`
     * 
     * @return bool True if the strings are equal, false otherwise.
     * 
     * @throws RuntimeException If the HKDF hashed string is invalid format.
     */
    public function hkdfEquals(string $string, string $hashedString): bool;

    /**
     * Encrypts a string using the AES-256-CBC algorithm and returns the encrypted string in the format of `string`@`iv`.
     * 
     * @param string $targetString The string to encrypt.
     *               * *Example:* `'Hello World'`
     * 
     * @return string The encrypted string in the format of `string`@`iv`.
     * * *Example:* `'hexzX3nLJKqMWuXEhiOQHQ==@IsMEmTl11x6Siyyug2HBnw=='`
     * 
     * @throws LogicException If the encryption fails.
     */
    public function encryptAesCbcString(string $targetString): string;

    /**
     * 
     * Decrypts a string that was encoded using AES-256-CBC algorithm with the format of `string`@`iv`.
     * 
     * @param string $encryptedString The encrypted string to decrypt in the format of `string`@`iv`.
     *               * *Example:* `'hexzX3nLJKqMWuXEhiOQHQ==@IsMEmTl11x6Siyyug2HBnw=='`
     * 
     * @return string The decrypted string.
     * * *Example:* `'Hello World'`
     * 
     * @throws RuntimeException If the decryption fails.
     */
    public function decryptAesCbcString(string $encryptedString): string;

    /**
     * Encodes a string to Base64 URL.
     *
     * @param string $string The string to encode.
     *               * *Example:* `'Hello World'`
     * 
     * @return string The Base64 URL encoded string.
     *                * *Example:* `'SGVsbG8gV29ybGQ'`
     */
    public function encodeBase64URL(string $string): string;

    /**
     * Decodes a Base64 URL encoded string.
     *
     * @param string $encodedString The Base64 URL encoded string to decode.
     *               * *Example:* `'SGVsbG8gV29ybGQ'`
     * 
     * @return string The decoded string.
     *                * *Example:* `'Hello World'`
     */
    public function decodeBase64URL(string $encodedString): string;
}
