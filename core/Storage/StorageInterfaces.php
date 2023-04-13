<?php

namespace Storage;

interface FileValidatorInterface
{
    /**
     * @param array $allowedMimeTypes Array of allowed mime types for uploaded files.
     */
    public function setAllowedMimeTypes(array $allowedMimeTypes);


    /**
     * @param array $allowedMimeTypes Array of allowed mime types for uploaded files.
     */
    public function setMaxFileSize(int $maxFileSize);

    /**
     * Validates an uploaded file based on the allowed mime types and maximum file size.
     *
     * @param string $postName     Name of the file input element.
     *
     * @throws \LogicException     The file was not uploaded using HTTP POST.
     *                             * Error codes:  
     *                             3000 - The file was not uploaded via HTTP POST.  
     * 
     * @throws ValidationException If the file is not uploaded, too large, has an extension not allowed,
     *                             or has a mime type that does not match the file type.  
     *                             * Error codes:  
     *                             3000 - File not found.  
     *                             3001 - File too large.  
     *                             3002 - File extension not allowed.  
     *                             3003 - File type does not match.  
     */
    public function validate(string $postName): void;

    /**
     * Moves the uploaded file to the specified path.
     *
     * @param string $postName     Name of the file input element.
     * @param string $destination  Destination path where the file should be moved.
     * 
     * @throws \LogicException     If the file is not uploaded via HTTP POST.
     *                             * Error codes:  
     *                             3004 - Failed to move file.
     */
    public function move(string $postName, string $destination): void;
}

interface SecureImageInterface
{
    /**
     * Returns a resized GD image resource based on the provided file path and maximum width/height constraints.
     *
     * @param string $filePath The path of the image file.
     * @param int|null $maxWidth The maximum width constraint. Defaults to the original width if not provided.
     * @param int|null $maxHeight The maximum height constraint. Defaults to the original height if not provided.
     *
     * @return GdImage A GD image resource of the resized image.
     *
     * @throws RuntimeException If an error occurs during the operation
     *                            with an error code as the second argument.
     *                            * Error codes:  
     *                            5000 - Invalid File.  
     *                            5001 - Unable to create image from the given data.  
     *                            5002 - Unable to get size of the given image data.  
     *                            5003 - Failed to create a new image.  
     *                            5004 - Failed to resize the image.
     */
    public function getImage(string $filePath, ?int $maxWidth = null, ?int $maxHeight = null): \GdImage;
}
