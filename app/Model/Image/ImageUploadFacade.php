<?php

namespace App\Model\Image;

use Nette\Http\FileUpload;
use Nette\Utils\Random;

final class ImageUploadFacade
{
    private string $uploadDir;

    public function __construct(
        private readonly string $wwwDir,
    ) {
        $this->uploadDir = $this->wwwDir . '/uploads';
    }

    /**
     * Nahraje obrázek do zadaného podadresáře a vrátí relativní cestu od www.
     * Pokud upload není validní, vrátí null.
     */
    public function upload(FileUpload $file, string $subdirectory = 'posts'): ?string
    {
        if (!$file->isOk() || !$file->isImage()) {
            return null;
        }

        $dir = $this->uploadDir . '/' . $subdirectory;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $ext = pathinfo($file->getSanitizedName(), PATHINFO_EXTENSION);
        $filename = Random::generate(10) . '.' . $ext;

        $file->move($dir . '/' . $filename);

        return 'uploads/' . $subdirectory . '/' . $filename;
    }

    /**
     * Smaže obrázek na zadané relativní cestě.
     */
    public function delete(string $relativePath): void
    {
        $fullPath = $this->wwwDir . '/' . $relativePath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}


