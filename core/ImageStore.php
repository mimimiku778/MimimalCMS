<?php

declare(strict_types=1);

require_once __DIR__ . '/storage/SecureImage.php';

use Storage\SecureImageInterface;
use Kernel\ImageStoreIntercase;

enum ImageType: string
{
    case JPG = 'jpeg';
    case WEBP = 'webp';
    case PNG = 'png';
}

/**
 * Store validated images.
 */
class ImageStore implements ImageStoreIntercase
{
    const MAX_WIDTH = 10000;
    const MAX_HEIGHT = 10000;

    private SecureImageInterface $image;
    private int $quality = 80;
    private ?int $maxWidth = null;
    private ?int $maxHeight = null;
    private int $errorCode;
    private string $errorMessage;
    private string $fileName;

    public function __construct(?SecureImageInterface $instance = null)
    {
        $this->image = $instance ?? new Storage\SecureImage;
    }

    public function setParams(?int $maxWidth = null, ?int $maxHeight = null, int $quality = 80)
    {
        if (Validator::num($quality, min: 0, max: 100) === false) {
            throw new InvalidArgumentException('Invalid image quality. Quality must be between 0 and 100.');
        }

        if ($maxWidth !== null && Validator::num($maxWidth, max: self::MAX_WIDTH) === false) {
            throw new InvalidArgumentException(
                'Invalid maximum width. Maximum width must be less than equal to ' . self::MAX_WIDTH
            );
        }

        if ($maxHeight !== null && Validator::num($maxHeight, max: self::MAX_HEIGHT) === false) {
            throw new InvalidArgumentException(
                'Invalid maximum height. Maximum height must be less than equal to ' . self::MAX_HEIGHT
            );
        }

        $this->quality = $quality;
        $this->maxWidth = $maxWidth > 0 ? $maxWidth : null;
        $this->maxHeight = $maxHeight > 0 ? $maxWidth : null;
    }

    public function getGdImage(array $file): object|false
    {
        try {
            return $this->image->getImage(
                Validator::arrayStr($file, 'tmp_name', emptyAble: false, e: InvalidArgumentException::class),
                $this->maxWidth,
                $this->maxHeight
            );
        } catch (RuntimeException $e) {
            $this->errorCode = $e->getCode();
            $this->errorMessage = $e->getMessage();
        }
    }

    public function store(array $file, string $path, \ImageType $imageType = \ImageType::WEBP, ?string $fileName = null): bool
    {
        $image = $this->getGdImage($file);
        if ($image === false) {
            return false;
        }

        if ($fileName === null) {
            $fileName = md5(uniqid((string) rand(), true));
        }

        if ($path !== '') {
            $path = ltrim(rtrim($path, "/"), "/") . "/";
        }

        $type = $imageType->value;

        if ($type === 'png') {
            $this->quality = $this->convertToSingleDigitWithBias($this->quality);
        }

        $function = 'image' . $type;
        $extention = '.' . $type;

        $filePath = __DIR__ . '/../' . $path . $fileName . $extention;

        if (!$function($image, $filePath, $this->quality)) {
            $this->errorCode =  6000;
            $this->errorMessage = 'Image convert failed';
            return false;
        }

        $this->fileName = $fileName . $extention;
        return true;
    }

    /**
     * Convert image quality value to a single digit integer between 0 and 9 with a bias.
     *
     * @param int $num The image quality value to convert. Must be between 0 and 100.
     * 
     * @return int A single digit integer between 0 and 9.
     * 
     * @throws InvalidArgumentException If the input is not between 0 and 100.
     */
    private function convertToSingleDigitWithBias(int $num): int
    {
        if ($num < 0 || $num > 100) {
            throw new InvalidArgumentException('Invalid image quality. Quality must be between 0 and 100.');
        }

        if ($num === 80) {
            return 6;
        }

        if ($num < 80) {
            // Convert the range 0-80 to 0-6 with a linear interpolation
            $result = $num / 80 * 6;
        } else {
            // Convert the range 80-100 to 6-9 with a linear interpolation
            $result = 6 + ($num - 80) / 20 * 3;
        }

        return round($result);
    }

    public function getErrorCode(): ?int
    {
        return $this->errorCode ?? null;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage ?? null;
    }

    public function getFileName(): ?string
    {
        return $this->fileName ?? null;
    }
}
