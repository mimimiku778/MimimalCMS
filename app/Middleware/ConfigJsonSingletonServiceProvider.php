<?php

namespace App\Middleware;

use App\Config\ConfigJson;

class ConfigJsonSingletonServiceProvider
{
    public function handle()
    {
        $concrete = new ConfigJson;

        \Shadow\Kernel\Dispatcher\ConstructorInjection::$container[ConfigJson::class] = [
            'concrete' => $concrete,
            'singleton' => [
                'flag' => true
            ]
        ];
    }
}
