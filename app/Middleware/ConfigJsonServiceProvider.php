<?php

namespace App\Middleware;

class ConfigJsonServiceProvider
{
    public function handle()
    {
        $concrete = function () {
            $jsonStorage = new \Shadow\JsonStorage;

            return $jsonStorage
                ->init(\App\Config\ConfigJson::class)
                ->copyPropertiesToObject();
        };

        \Shadow\Kernel\Dispatcher\ConstructorInjection::$container[\App\Config\ConfigJson::class] = [
            'concrete' => $concrete,
            'singleton' => [
                'flag' => false
            ]
        ];
    }
}
