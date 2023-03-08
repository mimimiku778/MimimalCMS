<?php

/**
 * Evil autoloader
 * 
 * It's maybe safe to use this autoloader in conjunction with Composer's autoloader.
 * 
 * NOTE: If the class file is located in the root directory path, namespace is not required.
 *       If the class file is located in a subdirectory, namespace must be used to match the class and file names correctly.
 */
spl_autoload_register(function ($className) {
    // Additional directories can be added to the $rootDirectoryNames variable.
    $rootDirectoryNames = ['core', 'models'];

    // Match the subdirectory name with the namespace name.
    $classFile = str_replace('\\', '/', ltrim($className, '\\')) . '.php';

    // Search for the class file in each root directory.
    foreach ($rootDirectoryNames as $rootDirectoryName) {
        $classFilePath = __DIR__ . '/' . $rootDirectoryName . '/' . $classFile;
        if (file_exists($classFilePath)) {
            require $classFilePath;
            return true;
        }
    }

    return false;
});

require __DIR__ . '/shared/config.php';
require __DIR__ . '/shared/functions.php';
require __DIR__ . '/shared/exceptions.php';

new Route();
