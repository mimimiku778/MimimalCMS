<?php

/**
 * Converts a date or datetime to a string.
 *
 * @param string|int $datetime The date or datetime to convert.
 * @param bool $time Whether to include the time.
 * @return string The converted date or datetime.
 * @throws \InvalidArgumentException If the provided datetime is invalid.
 */
function convertDatetime(string|int $datetime, bool $time = true): string
{
    $config = \App\Config\AppConfig::CONVERT_DATETIME_FORMAT;
    $datetimeFormat = $time ? $config['dateTimeFormat'] : $config['dateFormat'];

    if (is_int($datetime)) {
        return date($datetimeFormat, $datetime);
    }

    if (is_string($datetime)) {
        $formatsToTry = [
            'Y-m-d H:i:s', // Example: '2023-09-25 14:30:00'
            'Y-m-d H:i',   // Example: '2023-09-25 14:30'
            'Y-m-d',       // Example: '2023-09-25'
        ];

        foreach ($formatsToTry as $format) {
            $dateTime = DateTimeImmutable::createFromFormat($format, $datetime);
            if ($dateTime !== false) {
                return $dateTime->format($datetimeFormat);
            }
        }
    }

    throw new \InvalidArgumentException('Invalid datetime type');
}

/**
 * Get the elapsed time as a formatted string (MM:SS) from a given start time.
 *
 * @param string|int $startTime The start time in the format "Y-m-d H:i:s" or a Unix timestamp.
 * @return string The formatted elapsed time in "MM:SS" format.
 * @throws \InvalidArgumentException If the provided start time is not in a valid format.
 */
function getElapsedTime(string|int $startTime): string
{
    if (is_numeric($startTime)) {
        $startTime = (int)$startTime;
        if ($startTime < 0) {
            throw new \InvalidArgumentException("Invalid start time");
        }
        $startTime = date_create('@' . $startTime)->format("Y-m-d H:i:s");
    }

    $startTimeObj = date_create_from_format("Y-m-d H:i:s", $startTime);
    if (!$startTimeObj) {
        throw new \InvalidArgumentException("Invalid start time format");
    }

    $currentTime = new DateTime();
    $interval = $startTimeObj->diff($currentTime);

    // If the start time is in the future, set elapsed time to 00:00
    if ($interval->invert) {
        return "00:00";
    }

    $totalSeconds = $interval->s + ($interval->i * 60) + ($interval->h * 3600) + ($interval->days * 86400);

    $minutes = floor($totalSeconds / 60);
    $seconds = $totalSeconds % 60;

    return sprintf("%02d:%02d", $minutes, $seconds);
}

/**
 * Converts each line of a string into an HTML paragraph.
 *
 * @param string $string The input string to be converted.
 * @return string The converted string with each line wrapped in a HTML paragraph.
 */
function nl2p(string $string): string
{
    $paragraphs = array_map(fn ($line) => '<p>' . $line . '</p>', preg_split('/\r\n|\r|\n/', $string));
    return implode("\n", $paragraphs);
}

/**
 * Inserts HTML line breaks before all newlines in a string.
 *
 * @param string $string The input string to be processed.
 * @return string The string with HTML line breaks.
 */
function nl2brReplace(string $string): string
{
    $lines = preg_split('/\r\n|\r|\n/', $string);
    $result = implode("<br>", $lines);
    return $result;
}

function gTag(string $id): string
{
    return
        <<<HTML
        <script async src="https://www.googletagmanager.com/gtag/js?id={$id}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
        
            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', '{$id}');
        </script>
        HTML;
}
