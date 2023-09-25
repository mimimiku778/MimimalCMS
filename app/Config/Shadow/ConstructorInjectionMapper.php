<?php

namespace App\Config\Shadow;

class ConstructorInjectionMapper
{
    static $map = [
        \Shadow\StringCryptorInterface::class => \Shadow\StringCryptor::class,
        \Shadow\JsonStorageInterface::class => \Shadow\JsonStorage::class,
        \Shadow\Kernel\ViewInterface::class => \Shadow\Kernel\View::class,
        \Shadow\File\FileValidatorInterface::class => \Shadow\File\FileValidator::class,
        \Shadow\File\Image\ImageStoreInterface::class => \Shadow\File\Image\ImageStore::class,
        \Shadow\File\Image\GdImageFactoryInterface::class => \Shadow\File\Image\GdImageFactory::class,
    ];
}
