<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Media;

use App\Constants\Media\MediaPathConstants;
use App\Entity\Media;
use App\Service\FileManager;
use App\Service\Media\MediaPathGenerator;
use App\Service\Media\MediaUploader;
use App\Service\Workflow\Media\MediaWorkflowManager;
use Gedmo\Exception\UploadableFileNotReadableException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaUploaderTest extends TestCase
{
    private FileManager|MockObject $fileManager;
    private MediaPathGenerator|MockObject $mediaPathGenerator;
    private MediaWorkflowManager|MockObject $mediaWorkflowManager;

    private MediaUploader $mediaUploader; 

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileManager = $this->createMock(FileManager::class);
        $this->mediaPathGenerator = $this->createMock(MediaPathGenerator::class);
        $this->mediaWorkflowManager = $this->createMock(MediaWorkflowManager::class);

        $this->mediaUploader = new MediaUploader(
            $this->fileManager,
            $this->mediaPathGenerator,
            $this->mediaWorkflowManager
        );    
    }

    public function testNoFileIsOk(): void
    {
        $media = (new Media())
            ->setFormFile(null)
        ;

        $this->mediaPathGenerator->expects($this->never())->method('getAbsolutePrivateFolder');
        $this->fileManager->expects($this->never())->method('moveFile');

        $this->mediaUploader->upload($media);
    }

    public function testUploadNotReadableIsOk(): void
    {
        $media = (new Media())
            ->setFormFile($this->createMock(File::class))
        ;

        $this->mediaPathGenerator->expects($this->once())->method('getAbsolutePrivateFolder')->willReturn('test');
        $this->fileManager->expects($this->once())->method('moveFile')->willThrowException(new UploadableFileNotReadableException());

        $this->mediaUploader->upload($media);
        $this->assertNull($media->getFile());
    }

    public function testUploadFileUnexpectedTypeGetAnError(): void
    {
        $media = (new Media())
            ->setFormFile($this->createMock(File::class))
        ;

        $this->mediaPathGenerator->expects($this->once())->method('getAbsolutePrivateFolder')->willReturn('test');
        $this->fileManager->expects($this->once())->method('moveFile')->willReturn('test');

        $this->expectException(UnexpectedTypeException::class);
        $this->mediaUploader->upload($media);
    }

    public function testUploadIsOk(): void
    {
        $media = (new Media())
            ->setFormFile($this->createMock(UploadedFile::class))
        ;

        $this->mediaPathGenerator->expects($this->once())->method('getAbsolutePrivateFolder')->willReturn('test');
        $this->fileManager->expects($this->once())->method('moveFile')->willReturn('test');
        $this->mediaPathGenerator->expects($this->once())->method('getRelativePathFromFileName')->willReturn('testu');

        $this->mediaUploader->upload($media);

        $this->assertSame('testu', $media->getFile());

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

}