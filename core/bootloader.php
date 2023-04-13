<?php

require_once __DIR__ . '/ExceptionHandler.php';

require_once __DIR__ . '/SimpleAutoloader.php';
spl_autoload_register('SimpleAutoloader::load');

require_once __DIR__ . '/Kernel/Kernel.php';
