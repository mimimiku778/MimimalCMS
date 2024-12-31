<?php

namespace App\Config;

class AppConfig
{
    public static array $convertDatetimeFormat = [
        'dateFormat' => 'Y/m/d',
        'separator' => ' ',
        'timeFormat' => 'H:i'
    ];
    public static string $siteIconFilePath = 'assets/icon-192x192.png';
    public static string $defaultOgpImageFilePath = 'assets/ogp.png';
}
