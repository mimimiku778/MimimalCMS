<?php

namespace App\Config;

class ConstructorInjectionClassMap
{
    const MAP = [
        \Shadow\Storage\SecureImageInterface::class => \Shadow\Storage\SecureImage::class,
        \Shadow\StringCryptorInterface::class => \Shadow\StringCryptor::class,
    ];
}
