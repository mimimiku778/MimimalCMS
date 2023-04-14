<?php

declare(strict_types=1);

namespace Shadow\Storage;

/**
 * Class FileValidator
 * 
 * Validates uploaded files based on the allowed mime types and maximum file size.
 * 
 * @author mimimiku778 <0203.sub@gmail.com>
 * @license https://github.com/mimimiku778/MimimalCMS/blob/master/LICENSE.md
 */
class FileValidator implements FileValidatorInterface
{
    private array $allowedMimeTypes;
    private int $maxFileSize = DEFAULT_MAX_FILE_SIZE;
    private array $files = $_FILES;

    public function setAllowedMimeTypes(array $allowedMimeTypes)
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    public function setMaxFileSize(int $maxFileSize)
    {
        $this->maxFileSize = $maxFileSize;
    }

    public function validate(string $postName): void
    {
        $file = $this->files[$postName] ?? null;

        if ($file === null) {
            throw new \ValidationException('The file was not uploaded using HTTP POST.', 3000);
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            throw new \LogicException('Invalid file.', 3000);
        }

        if ($file['size'] > $this->maxFileSize  * 1024) {
            throw new \ValidationException('File too large.', 3001);
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $this->allowedMimeTypes, true)) {
            throw new \ValidationException('File extension not allowed.', 3002);
        }

        if ($mimeType !== $file['type']) {
            throw new \ValidationException('File type does not match.', 3003);
        }
    }

    public function move(string $postName, string $destination): void
    {
        if (!move_uploaded_file($this->files[$postName]['tmp_name'], $destination)) {
            throw new \LogicException('Failed to move file.', 3004);
        }
    }
}
