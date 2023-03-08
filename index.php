<?php

/**
 * Autoloader
 * 
 * NOTE: Class and file names must match exactly, no use namespaces.
 */
spl_autoload_register(function ($className) {
    // Additional directories can be added to the $directoryNames variable.
    $directoryNames = ['core', 'models'];

    $className = ltrim($className, '\\');
    $classFile = str_replace('\\', '/', $className) . '.php';

    foreach ($directoryNames as $directoryName) {
        $classFilePath = __DIR__ . '/' . $directoryName . '/' . $classFile;
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