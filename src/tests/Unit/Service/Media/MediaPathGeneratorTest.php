<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Media;

use App\Constants\Media\MediaPathConstants;
use App\Entity\Media;
use App\Service\Media\MediaPathGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class MediaPathGeneratorTest extends TestCase
{
    private ParameterBagInterface|MockObject $params;

    private MediaPathGenerator $mediaPathGenerator; 
    private SluggerInterface $slugger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->params = $this->createMock(ParameterBagInterface::class);
        $this->slugger = $this->createMock(SluggerInterface::class);

        $this->params
            ->expects($this->any())
            ->method('get')
            ->willReturnCallBack(function($param) {
                return match ($param) {
                    'kernel.project_dir' => MediaPathConstants::ROOT_PATH,
                    'medias_private_path' => MediaPathConstants::PRIVATE_PATH,
                    'medias_fixture_path' => MediaPathConstants::FIXTURE_PATH,
                };
            });

        $this->mediaPathGenerator = new MediaPathGenerator(
            $this->params,
            $this->slugger
        );    
    }

    public function testRootFolderIsOk(): void 
    {
        $result = $this->mediaPathGenerator->getRootFolder();

        $this->assertSame(MediaPathConstants::ROOT_PATH, $result);
    }

    public function testRelativePrivateFolderIsOk(): void 
    {
        $result = $this->mediaPathGenerator->getRelativePrivateFolder();

        $this->assertSame(
            MediaPathConstants::PRIVATE_PATH, 
            $result
        );
    }

    public function testRelativeFixturesFolderIsOk(): void 
    {
        $result = $this->mediaPathGenerator->getRelativeFixturesFolder();

        $this->assertSame(
            MediaPathConstants::FIXTURE_PATH,
            $result
        );
    }

    public function testAbsolutePrivateFolderIsOk(): void 
    {
        $result = $this->mediaPathGenerator->getAbsolutePrivateFolder();

        $this->assertSame(
            sprintf(
                '%s/%s', 
                MediaPathConstants::ROOT_PATH, 
                MediaPathConstants::PRIVATE_PATH
            ), 
            $result
        );
    }

    public function testAbsoluteFixturesFolderIsOk(): void 
    {
        $result = $this->mediaPathGenerator->getAbsoluteFixturesFolder();

        $this->assertSame(
            sprintf(
                '%s/%s', 
                MediaPathConstants::ROOT_PATH, 
                MediaPathConstants::FIXTURE_PATH
            ), 
            $result
        );
    }

    public function testRelativePathFromFileNameIsOk(): void 
    {
        $result = $this->mediaPathGenerator->getRelativePathFromFileName('file.txt');

        $this->assertSame(
            sprintf(
                '%s/%s', 
                MediaPathConstants::PRIVATE_PATH,
                'file.txt',
            ), 
            $result
        );
    }

    public function testAbsolutePathOfMediaIsOk(): void 
    {
        $media = (new Media())
            ->setFile(MediaPathConstants::PRIVATE_PATH . '/file.txt');
        ;

        $result = $this->mediaPathGenerator->getAbsolutePathOfMedia($media);

        $this->assertSame(
            sprintf(
                '%s/%s/%s', 
                MediaPathConstants::ROOT_PATH,
                MediaPathConstants::PRIVATE_PATH,
                'file.txt',
            ), 
            $result
        );
    }


}