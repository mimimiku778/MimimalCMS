<?php

namespace Shadow\Storage;

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