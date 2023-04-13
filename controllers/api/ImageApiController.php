<?php

class ImageApiController
{
    public function store(array $file, string $imageType, int $imageSize, ImageStore $image)
    {
        $image->setParams($imageSize, $imageSize);

        if ($image->store($file, 'images', constant("ImageType::{$imageType}"))) {
            return redirect('image')
                ->with('image', $image->getFileName());
        } else {
            return redirect('image')
                ->withErrors('image', $image->getErrorCode(), $image->getErrorMessage());
        }
    }
}
