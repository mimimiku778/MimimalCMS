<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use Shadow\File\Image\GdImageFactoryInterface;
use Shadow\File\Image\ImageStoreInterface;

class ImageApiController
{
    public function store(
        GdImageFactoryInterface $image,
        ImageStoreInterface $store,
        array $file,
        string $imageType,
        int $imageSize
    ) {
        try {
            $gdImage = $image->createGdImage($file, $imageSize, $imageSize);
            $fileName = $store->storeImageFromGdImage(
                $gdImage,
                publicDir('images'),
                hash('sha256', getIP() . rand() . time()),
                $imageType
            );
        } catch (\RuntimeException $e) {
            return redirect('image')
                ->withErrors('image', $e->getCode(), $e->getMessage());
        }

        return redirect('image')
            ->with('image', $fileName);
    }
}
