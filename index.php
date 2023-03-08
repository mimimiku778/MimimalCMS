<?php

/**
 * Evil way autoloader
 * 
 * NOTE: Namespace is not required, if the class file is located at the root directory path.
 *       If it is located in a subdirectory, namespace must be used to match the class and file names correctly.
 */
spl_autoload_register(function ($className) {
    // Additional directories can be added to the $rootDirectoryNames variable.
    $rootDirectoryNames = ['core', 'models'];

    //Match subdirectory name with namespace name
    $classFile = str_replace('\\', '/', ltrim($className, '\\')) . '.php';

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

new Route();
