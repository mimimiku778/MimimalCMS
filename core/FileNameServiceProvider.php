<?php

declare(strict_types=1);

namespace Shadow;

use Shadow\JsonStorage;
use Shadow\Kernel\Dispatcher\ConstructorInjection;

class FileNameServiceProvider
{
    public function handle()
    {
        $concrete = function () {
            $jsonStorage = new JsonStorage;

            return $jsonStorage
                ->init(FileNameService::class)
                ->copyPropertiesToObject();
        };

        ConstructorInjection::$container[FileNameService::class] = ['concrete' => $concrete, 'singleton' => ['flag' => false]];
    }
}
