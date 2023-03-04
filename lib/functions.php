<?php

declare(strict_types=1);

/**
 * 配列に指定されたキーが存在し、文字列が指定された条件を満たすかどうかを検証する
 *
 * @param array $array 検証する配列
 * @param string $name 検証するキー名
 * @param int|null $max_length 文字列の最大長（オプション）
 * @param string|null $exact_match 精密マッチのための文字列（オプション）
 * @return bool 検証結果
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
 * 配列に指定されたキーが存在し、数字が指定された条件を満たすかどうかを検証する
 *
 * @param array $array 検証する配列
 * @param string $name 検証するキー名
 * @param int|null $max_num 数値の最大値（オプション）
 * @param int|null $min_num 数値の最小値（オプション）
 * @param int|null $exact_match 精密マッチのための数値（オプション）
 * @return bool 検証結果
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
