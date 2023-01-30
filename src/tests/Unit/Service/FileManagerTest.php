<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\FileManager;
use Gedmo\Exception\UploadableFileNotReadableException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class FileManagerTest extends TestCase
{
    private SluggerInterface|MockObject $slugger;
    private File|MockObject $file;

    private FileManager $fileManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->slugger = $this->createMock(SluggerInterface::class);
        $this->file = $this->createMock(File::class);

        $this->fileManager = new FileManager($this->slugger);
    }

    public function testMoveInvalidFileGetAnError(): void
    {
        $this->file->expects($this->once())->method('isReadable')->willReturn(false);

        $this->file->expects($this->never())->method('getFilename');
        $this->file->expects($this->never())->method('guessExtension');
        $this->file->expects($this->never())->method('move');
        $this->slugger->expects($this->never())->method('slug');

        $this->expectException(UploadableFileNotReadableException::class);

        $this->fileManager->moveFile($this->file, 'test/');        
    }

    public function testMoveFileOk(): void
    {
        $this->file->expects($this->once())->method('isReadable')->willReturn(true);
        $this->file->expects($this->once())->method('getFilename')->willReturn('toto.jpg');
        $this->file->expects($this->once())->method('guessExtension')->willReturn('jpg');
        $this->file->expects($this->once())->method('move')->willReturn($this->file);
        $this->slugger->expects($this->once())->method('slug')->willReturn(new UnicodeString('toto'));
        $this->fileManager = new FileManager($this->slugger);
        
        $return = $this->fileManager->moveFile($this->file, 'test/');
    
        $this->assertNotFalse(preg_match('/^(test\/toto-[^.]+\.jpg)$/', $return));
    }
}