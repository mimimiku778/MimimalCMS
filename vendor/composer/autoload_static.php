<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb99d262e1b8550ce1d6fd4bec6885e85
{
    public static $files = array (
        'a0806125a7b5fd4bade8b1d3de4ae2f2' => __DIR__ . '/../..' . '/core/Exceptions/ExceptionHandler.php',
        '32ce0fe7e283c4ab2501f48c49b8b765' => __DIR__ . '/../..' . '/shared/MimimalCMS_Config.php',
        '072636c41bf6e1d1798641cf4f6d69ca' => __DIR__ . '/../..' . '/shared/MimimalCMS_Enums.php',
        '5f7bfa9cae57467db83be145516aa1f7' => __DIR__ . '/../..' . '/shared/MimimalCMS_HelperFunctions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Shadow\\' => 7,
        ),
        'A' => 
        array (
            'App\\Views\\' => 10,
            'App\\Controllers\\' => 16,
            'App\\Config\\' => 11,
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Shadow\\' => 
        array (
            0 => __DIR__ . '/../..' . '/core',
        ),
        'App\\Views\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/Views/Classes',
        ),
        'App\\Controllers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app/Controllers',
        ),
        'App\\Config\\' => 
        array (
            0 => __DIR__ . '/../..' . '/shared/Config',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb99d262e1b8550ce1d6fd4bec6885e85::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb99d262e1b8550ce1d6fd4bec6885e85::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb99d262e1b8550ce1d6fd4bec6885e85::$classMap;

        }, null, ClassLoader::class);
    }
}
