<?php

declare(strict_types=1);

namespace App\Service\Media;

use App\Entity\Media;
use App\Entity\Student;
use App\Service\FileManager;
use App\Service\Workflow\Media\MediaWorkflowManager;
use Gedmo\Exception\UploadableFileNotReadableException;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaUploader
{
    public function __construct(
        private FileManager $fileManager,
        private MediaPathGenerator $mediaPathGenerator,
        private MediaWorkflowManager $mediaWorkflowManager
    ) {
    }

    public function upload(Media $media): void
    {
        if (null === $media->getFormFile()) {
            return;
        }
        $file = $media->getFormFile();
        try {
            $filename = $this->fileManager->moveFile(
                $file,
                $this->mediaPathGenerator->getAbsolutePrivateFolder(),
            );
        } catch (UploadableFileNotReadableException) {
            return;
        }

        if (!($file instanceof UploadedFile)) {
            throw new UnexpectedTypeException($file, UploadedFile::class);
        }

        $media->setOriginalName($file->getClientOriginalName());
        $media->setFile($this->mediaPathGenerator->getRelativePathFromFileName($filename));
    }

    public function forceStateMedia(Media $media, string $code, Student $student): void
    {
        $media->setStudent($student);
        $media->setCode($code);
        $this->mediaWorkflowManager->uploadedToCheck($media);
        $this->mediaWorkflowManager->checkToAccepted($media);

    }
}
