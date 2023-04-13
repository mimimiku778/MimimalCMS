<?php

declare(strict_types=1);

namespace Storage;

require_once __DIR__ . '/StorageInterfaces.php';

use RuntimeException;

class SecureImage implements SecureImageInterface
{
    public function getImage(string $filePath, ?int $maxWidth = null, ?int $maxHeight = null): \GdImage
    {
        $imageData = file_get_contents($filePath);

        if (!$imageData) {
            throw new RuntimeException('Invalid File.', 5000);
        }

        $srcImage = imagecreatefromstring($imageData);

        if (!$srcImage) {
            throw new RuntimeException('Unable to create image from the given data.', 5001);
        }

        [$srcWidth, $srcHeight] = getimagesizefromstring($imageData);

        if (!$srcWidth || !$srcHeight) {
            throw new RuntimeException('Unable to get size of the given image data.', 5002);
        }

        [$dstWidth, $dstHeight] = $this->getNewSize($srcWidth, $srcHeight, $maxWidth, $maxHeight);

        $dstImage = $this->createImage($dstWidth, $dstHeight);

        $this->processAlphaChannel($srcImage, $dstImage);

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $dstWidth, $dstHeight, $srcWidth, $srcHeight);

        imagedestroy($srcImage);

        return $dstImage;
    }

    private function getNewSize(int $srcWidth, int $srcHeight, ?int $maxWidth, ?int $maxHeight): array
    {
        $dstWidth = $srcWidth;
        $dstHeight = $srcHeight;

        if ($maxWidth && $srcWidth > $maxWidth) {
            $dstWidth = $maxWidth;
            $dstHeight = intval(($dstWidth / $srcWidth) * $srcHeight);
        }

        if ($maxHeight && $dstHeight > $maxHeight) {
            $dstHeight = $maxHeight;
            $dstWidth = intval(($dstHeight / $srcHeight) * $srcWidth);
        }

        return [$dstWidth, $dstHeight];
    }

    private function createImage(int $width, int $height): \GdImage
    {
        $image = imagecreatetruecolor($width, $height);

        if (!$image) {
            throw new RuntimeException('Failed to create a new image.', 5003);
        }

        return $image;
    }

    private function processAlphaChannel(\GdImage $srcImage, \GdImage $dstImage)
    {
        $isAlpha = false;
        if (function_exists('imageistruecolor') && !imageistruecolor($srcImage)) {
            $isAlpha = imagecolortransparent($srcImage) != -1 || imagecolorstotal($srcImage) > 255;
        } else {
            $isAlpha = imagealphablending($srcImage, false);
            $color = imagecolorallocatealpha($srcImage, 0, 0, 0, 127);
            $isAlpha = $isAlpha || (imagecolorat($srcImage, 0, 0) & 0x7F000000) != 0x7F000000 || imagecolorclosestalpha($srcImage, 0, 0, 0, 127) != $color;
        }
        if ($isAlpha) {
            imagepalettetotruecolor($srcImage);
            imagealphablending($srcImage, true);
            imagesavealpha($srcImage, true);
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
        }
    }
}
