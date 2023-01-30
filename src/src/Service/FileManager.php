<?php

declare(strict_types=1);

namespace App\Service;

use Gedmo\Exception\UploadableFileNotReadableException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileManager
{
    public function __construct(
        private SluggerInterface $slugger
    ) {
    }

    public function moveFile(File $file, string $directory): string
    {
        if (!($file->isReadable())) {
            throw new UploadableFileNotReadableException();
        }

        $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);

        $safeFilename = $this->slugger->slug($filename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move(
            $directory,
            $newFilename
        );
        return $newFilename;
    }
}
