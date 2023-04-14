<?php

declare(strict_types=1);

namespace Shadow\Kernel;

/**
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class Validator implements ValidatorInterface
{
    public static function str(
        mixed $input,
        ?int $maxLen = null,
        ?string $regex = null,
        ?bool $emptyAble = true,
        ?string $e = null
    ): string|false {
        if (!is_string($input)) {
            $errorCode = 1001;
            $errorMessage = 'The input must be a string.';
            if ($e === null) {
                return false;
            }
            throw new $e($errorMessage, $errorCode);
        }

        if ($regex !== null) {
            $result = @preg_match($regex, $input);
            if ($result === false) {
                $errorCode = 1000;
                $errorMessage = 'An error occurred while executing preg_match function. Please check the regex pattern.';
                throw new \LogicException($errorMessage, $errorCode);
            }

            if ($result === 0) {
                $errorCode = 1002;
                $errorMessage = 'The input string does not match the specified regex pattern.';
                if ($e === null) {
                    return false;
                }
                throw new $e($errorMessage, $errorCode);
            }
        }

        if (!$emptyAble) {
            $normalizedStr = $input;
            if (class_exists('Normalizer')) {
                $normalizedStr = \Normalizer::normalize($input, \Normalizer::FORM_KC);
            }

            $replaceStr = preg_replace(ValidatorInterface::ZERO_WHITE_SPACE, '', $normalizedStr);

            if ($replaceStr === null || empty(trim($replaceStr))) {
                $errorCode = 1003;
                $errorMessage = 'The input string contains only whitespace characters or an empty string.';
                if ($e === null) {
                    return false;
                }
                throw new $e($errorMessage, $errorCode);
            }
        }

        if ($maxLen !== null && mb_strlen($input) > $maxLen) {
            $errorCode = 1004;
            $errorMessage = 'The input string exceeds the maximum length limit of ' . $maxLen . ' characters.';
            if ($e === null) {
                return false;
            }
            throw new $e($errorMessage, $errorCode);
        }

        return $input;
    }

    public static function arrayStr(
        array $array,
        string $key,
        ?int $maxLen = null,
        ?string $regex = null,
        ?bool $emptyAble = true,
        ?string $e = null
    ): string|false {
        if (!isset($array[$key])) return false;
        return self::str($array[$key], $maxLen, $regex, $emptyAble, $e);
    }

    public static function num(
        mixed $input,
        ?int $max = null,
        ?int $min = null,
        ?int $exactMatch = null,
        ?string $e = null
    ): int|false {
        if (!is_int($input) && (!is_string($input) || !ctype_digit($input))) {
            if ($e === null) return false;
            $errorCode = 2001;
            $errorMessage = 'The input must be an integer or a string containing only digits.';
            throw new $e($errorMessage, $errorCode);
        }

        $number = (int) $input;

        if ($exactMatch !== null && $number !== $exactMatch) {
            if ($e === null) return false;
            $errorCode = 2002;
            $errorMessage = 'The input does not match the expected value.';
            throw new $e($errorMessage, $errorCode);
        }

        if ($min !== null && $number < $min) {
            if ($e === null) return false;
            $errorCode = 2003;
            $errorMessage = 'The input must be greater than or equal to ' . $min . '.';
            throw new $e($errorMessage, $errorCode);
        }

        if ($max !== null && $number > $max) {
            if ($e === null) return false;
            $errorCode = 2004;
            $errorMessage = 'The input must be less than or equal to ' . $max . '.';
            throw new $e($errorMessage, $errorCode);
        }

        return (int) $input;
    }

    public static function arrayNum(
        array $array,
        string $key,
        ?int $max = null,
        ?int $min = null,
        ?int $exactMatch = null,
        ?string $e = null
    ): int|false {
        if (!isset($array[$key])) return false;
        return self::num($array[$key], $max, $min, $exactMatch, $e);
    }

    public static function file(array $file, array $allowedMimeTypes, int $maxFileSize = DEFAULT_MAX_FILE_SIZE): array
    {
        if (!is_uploaded_file($file['tmp_name'])) {
            $errorCode = 3000;
            $errorMessage = 'Invalid file.';
            throw new \LogicException($errorMessage, $errorCode);
        }

        if ($file['size'] > $maxFileSize * 1024) {
            $errorCode = 3001;
            $errorMessage = 'File too large.';
            throw new \ValidationException($errorMessage, $errorCode);
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            $errorCode = 3002;
            $errorMessage = 'File extension not allowed.';
            throw new \ValidationException($errorMessage, $errorCode);
        }

        if ($mimeType !== $file['type']) {
            $errorCode = 3003;
            $errorMessage = 'File type does not match.';
            throw new \ValidationException($errorMessage, $errorCode);
        }

        return $file;
    }
}
