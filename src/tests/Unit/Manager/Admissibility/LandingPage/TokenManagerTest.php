<?php

declare(strict_types=1);

namespace App\Tests\Unit\Manager\Admissibility\LandingPage;

use App\Entity\Admissibility\LandingPage\AdmissibilityStudentToken;
use App\Entity\Student;
use App\Manager\Admissibility\LandingPage\TokenManager;
use App\Repository\Admissibility\LandingPage\AdmissibilityStudentTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TokenManagerTest extends TestCase
{
    private EntityManagerInterface|MockObject $em;
    private AdmissibilityStudentTokenRepository|MockObject $admissibilityStudentTokenRepository;

    private TokenManager $tokenManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->admissibilityStudentTokenRepository = $this->createMock(AdmissibilityStudentTokenRepository::class);

        $this->tokenManager = new TokenManager(
            $this->em, 
            $this->admissibilityStudentTokenRepository,
        );
    }

    public function testSaveToken(): void
    {
        $this->admissibilityStudentTokenRepository->expects($this->once())->method('findOneBy')->willReturn(null);
        $this->em->expects($this->once())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $admissibilityStudentToken = $this->tokenManager->saveToken(new Student());
        $this->assertIsString($admissibilityStudentToken->getToken());
    }

    public function testRefreshToken(): void
    {
        $this->admissibilityStudentTokenRepository->expects($this->once())->method('findOneBy')->willReturn((new AdmissibilityStudentToken())->setId(1));
        $this->em->expects($this->never())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $admissibilityStudentToken = $this->tokenManager->saveToken(new Student());
        $this->assertIsString($admissibilityStudentToken->getToken());
        $this->assertEquals(1, $admissibilityStudentToken->getId());
    }
}