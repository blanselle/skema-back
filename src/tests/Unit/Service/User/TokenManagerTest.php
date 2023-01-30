<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\TokenManager;
use DateTimeImmutable;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Uid\Uuid;

class TokenManagerTest extends TestCase
{
    private TokenManager $tokenManager;
    private JWTTokenManagerInterface|MockObject $jWTTokenManagerInterface;
    private UserRepository|MockObject $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jWTTokenManagerInterface = $this->createMock(JWTManager::class);
        $this->userRepository = $this->createMock(UserRepository::class);

        $this->tokenManager = new TokenManager(
            $this->jWTTokenManagerInterface,
            $this->userRepository
        );
    }

    public function testParseOk(): void
    {
        $uuid = 'd9e7a184-5d5b-11ea-a62a-3499710062d0';

        $this->jWTTokenManagerInterface
            ->expects($this->once())
            ->method('parse')
            ->willReturn(['userIdentifier' => $uuid])
        ;

        $this->userRepository
            ->expects($this->once())
            ->method('__call')
            ->willReturn((new User())
                ->setFirstName('John')
                ->setId(Uuid::fromString($uuid))
            )
        ;

        $result = $this->tokenManager->parse('token');

        $this->assertSame($result->getFirstName(), 'John');
    }

    public function testParseUserNotFound(): void
    {
        $uuid = Uuid::fromString('d9e7a184-5d5b-11ea-a62a-3499710062d0');

        $this->jWTTokenManagerInterface
            ->expects($this->once())
            ->method('parse')
            ->willReturn(['userIdentifier' => $uuid])
        ;

        $this->userRepository
            ->expects($this->once())
            ->method('__call')
            ->willReturn(null)
        ;

        $this->expectException(UserNotFoundException::class);
        $this->tokenManager->parse('token');
    }
    
    public function testSetInvalidExpirationGetAnError(): void
    {
        $this->jWTTokenManagerInterface->expects($this->never())->method('parse');

        $this->userRepository->expects($this->never())->method('__call');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Expiration not valid');
        $this->tokenManager->setExpiration(-1);
    }

    public function testSetInvalidExpirationFromDateGetAnError(): void
    {
        $this->jWTTokenManagerInterface->expects($this->never())->method('parse');

        $this->userRepository->expects($this->never())->method('__call');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Expiration parameter not valid');
        $this->tokenManager->setEndDateExpiration('testu');
    }

    public function testSetValidExpirationFromDateIsOk(): void
    {
        $this->jWTTokenManagerInterface->expects($this->never())->method('parse');

        $this->userRepository->expects($this->never())->method('__call');

        $this->tokenManager->setEndDateExpiration(('1 month'));
    }
}