<?php

declare(strict_types=1);

class StringCryptor
{
    // NOTE: Replace the following placeholder keys with your own keys before deploying to production.
    private const HKDF_KEY = 'REPLACE_WITH_YOUR_HKDF_KEY';
    private const OPENSSL_KEY = 'REPLACE_WITH_YOUR_OPENSSL_KEY';

    /**
     * Encrypts a string using AES-CBC and obtains the hash of the encrypted string using HKDF.
     * The output is a concatenation of the encrypted string and its hash, separated by the "#" symbol and Base64 encoded.
     * 
     * @param string $string The string to be encrypted and hashed.
     * @return string The concatenation of the encrypted string and its hash, separated by the "#" symbol and Base64 encoded.
     * 
     * @throws LogicException if the encryption fails.
     */
    public function encryptAndHashString(string $string): string
    {
        $encryptedString = $this->encryptAesCbcString($string);
        $hash = $this->hashHkdf($encryptedString);

        return $this->encodeBase64URL($encryptedString . '#' . $hash);
    }

    /**
     * Verifies the hash of an encrypted string and returns the decrypted string.
     * 
     * @param string $encryptedString The encrypted string and its hash, separated by the "#" symbol and Base64 encoded.
     * @return string|false Returns the decrypted string if the hash is valid, or false otherwise.
     * 
     * @throws LogicException If the hash is valid but decryption fails.
     */
    public function decryptAndVerifyHash(string $encryptedString): string|false
    {
        $components = explode("#", $this->decodeBase64URL($encryptedString));

        if (count($components) !== 2) {
            return false;
        }

        $encryptedString = $components[0];
        $hash = $components[1];

        if (!$this->hkdfEquals($encryptedString, $hash)) {
            return false;
        }

        try {
            $decryptedData = $this->decryptAesCbcString($encryptedString);
        } catch (RuntimeException $e) {
            throw new LogicException('Hash is valid but decryption fails: ' . $e->getMessage());
        }

        return $decryptedData;
    }

    /**
     * Hashes a string using HKDF with SHA3-224 and returns a string in the format of "<hash>@<salt>".
     *
     * @param string $string The string to hash.
     * @return string The hashed string with salt.
     */
    public function hashHkdf(string $string): string
    {
        $salt = random_bytes(16);
        $hash = hash_hkdf('SHA3-224', self::HKDF_KEY, 0, $string, $salt);

        // Return the Base64 encoded hash with the salt in the format of "<hash>@salt>".
        return base64_encode($hash) . '@' . base64_encode($salt);
    }

    /**
     * Compares a string with a HKDF hashed string with salt in the format of "<hash>@<salt>".
     *
     * @param string $string The string to compare.
     * @param string $hashedString The HKDF hashed string with salt in the format of "<hash>@<salt>".
     * @return bool True if the strings are equal, false otherwise.
     */
    public function hkdfEquals(string $string, string $hashedString): bool
    {
        $components = explode('@', $hashedString);

        if (count($components) !== 2) {
            return false;
        }

        $hash = base64_decode($components[0]);
        $salt = base64_decode($components[1]);

        $reHash = hash_hkdf('SHA3-224', self::HKDF_KEY, 0, $string, $salt);

        return hash_equals($hash, $reHash);
    }

    /**
     * 
     * Decrypts a string that was encoded using AES-256-CBC algorithm with the format of "<string>@<iv>".
     * 
     * @param string $encryptedString The encrypted string to decrypt in the format of "<string>@<iv>".
     * @return string The decrypted string.
     * 
     * @throws RuntimeException if the decryption fails.
     */
    public function decryptAesCbcString(string $encryptedString): string|false
    {
        $components = explode('@', $encryptedString);
        if (count($components) !== 2) {
            throw new RuntimeException('Invalid format for the encrypted string.');
            return false;
        }

        $encryptedData = base64_decode($components[0]);
        $iv = base64_decode($components[1]);

        $decryptedData = openssl_decrypt(
            $encryptedData,
            'AES-256-CBC',
            self::OPENSSL_KEY,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($decryptedData === false) {
            throw new RuntimeException('Decryption failed.');
            return false;
        }

        return $decryptedData;
    }

    /**
     * 
     * Encrypts a string using the AES-256-CBC algorithm and returns the encrypted string in the format of "<string>@<iv>".
     * 
     * @param string $targetString The string to encrypt.
     * @return string The encrypted string in the format of "<string>@<iv>".
     * 
     * @throws LogicException if the encryption fails.
     */
    public function encryptAesCbcString(string $targetString): string
    {
        $iv = random_bytes(16);

        $encryptedData = openssl_encrypt(
            $targetString,
            'AES-256-CBC',
            self::OPENSSL_KEY,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($encryptedData === false) {
            throw new LogicException('Encryption failed.');
        }

        // Return the Base64-encoded encrypted string in the format "<string>@<iv>".
        return base64_encode($encryptedData) . '@' . base64_encode($iv);
    }

    /**
     * Decodes a Base64URL encoded string.
     *
     * @param string $encodedString The Base64URL encoded string to decode.
     * @return string The decoded string.
     */
    public function decodeBase64URL(string $encodedString): string
    {
        $str = strtr($encodedString, '-_', '+/');
        $padding = strlen($str) % 4;
        if ($padding !== 0) {
            $str = str_pad($str, strlen($str) + (4 - $padding), '=', STR_PAD_RIGHT);
        }
        return base64_decode($str);
    }

    /**
     * Encodes a string to Base64URL.
     *
     * @param string $string The string to encode.
     * @return string The Base64URL encoded string.
     */
    public function encodeBase64URL(string $string): string
    {
        $base64 = base64_encode($string);
        $urlSafe = strtr($base64, '+/', '-_');
        return rtrim($urlSafe, '=');
    }
}

$test = new StringCryptor();
$test->decryptAesCbcString('aaaa');
var_dump('aa');
