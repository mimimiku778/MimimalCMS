<?php

namespace Shadow;

interface ImageStoreInterface
{
    /**
     * Set parameters for storing the image.
     *
     * @param int|null $quality   The quality of the stored image (0-100).
     * @param int|null $maxWidth  The maximum width of the stored image. Set to null or 0 for no maximum width.
     * @param int|null $maxHeight The maximum height of the stored image. Set to null or 0 for no maximum height.
     * 
     * @throws \InvalidArgumentException If any of the given parameters are invalid.
     */
    public function setParams(?int $maxWidth = null, ?int $maxHeight = null, int $quality = 80);

    /**
     * Returns a GD image resource or false if image processing failed.
     *
     * @param array $file An array containing information about the uploaded file.
     *                    Must have the "tmp_name" key which contains the path to the uploaded file.
     *
     * @return \GdImage|false A GD image resource or false if image processing failed.
     *
     * @throws \InvalidArgumentException If the "tmp_name" key is not found in the input array.
     */
    public function getGdImage(array $file): object|false;

    /**
     * Store the given image file to the specified path with the given file name and image type.
     *
     * @param array $file An array containing information about the uploaded file.  
     *                    Must have the "tmp_name" key which contains the path to the uploaded file.
     * 
     * @param string      $path      The path to save the image file.
     * @param \ImageType   $imageType The image type to save as, defaults to ImageType::WEBP.
     * @param string|null $fileName  The file name to save as, defaults to a unique md5 hash.
     * 
     * @return bool Whether the image was successfully stored.
     * 
     * @throws \InvalidArgumentException If the provided arguments are invalid.
     */
    public function store(array $file, string $path, \ImageType $imageType = \ImageType::WEBP, ?string $fileName = null): bool;

    /**
     * Get the error code, or null if it is not set.
     *
     * @return int|null The error code, or null if it is not set.
     */
    public function getErrorCode(): ?int;

    /**
     * Get the error message, or null if it is not set.
     *
     * @return string|null The error message, or null if it is not set.
     */
    public function getErrorMessage(): ?string;

    /**
     * Get the file name, or null if it is not set.
     *
     * @return string|null The file name, or null if it is not set.
     */
    public function getFileName(): ?string;
}
