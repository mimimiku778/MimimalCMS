<?php

namespace App\Config;

class ConstructorInjectionClassMap
{
    static $map = [
        \Shadow\StringCryptorInterface::class => \Shadow\StringCryptor::class,
        \Shadow\FileNameServiceInterface::class => \Shadow\FileNameService::class,
        \Shadow\JsonStorageInterface::class => \Shadow\JsonStorage::class,
        \Shadow\File\FileValidatorInterface::class => \Shadow\File\FileValidator::class,
        \Shadow\File\Image\ImageStoreInterface::class => \Shadow\File\Image\ImageStore::class,
        \Shadow\File\Image\GdImageFactoryInterface::class => \Shadow\File\Image\GdImageFactory::class,
    ];
}
