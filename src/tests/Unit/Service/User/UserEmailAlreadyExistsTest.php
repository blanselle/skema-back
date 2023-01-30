<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Entity\Bloc\Bloc;
use App\Entity\User;
use App\Exception\Bloc\BlocNotFoundException;
use App\Repository\BlocRepository;
use App\Repository\UserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UserEmailAlreadyExistsTest extends TestCase
{
    private UserRepository|MockObject $userRepository;
    private BlocRepository|MockObject $blocRepository;

    private UserEmailAlreadyExists $userEmailAlreadyExists;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->blocRepository = $this->createMock(BlocRepository::class);

        $this->userEmailAlreadyExists = new UserEmailAlreadyExists(
            $this->userRepository,
            $this->blocRepository,
        );
    }

    public function testEmailExistsIsOk(): void
    {
        $user = (new User())
            ->setEmail('email@mail.fr')
        ;

        $this->userRepository->expects($this->once())->method('emailExist')->willReturn(false);
        $this->blocRepository->expects($this->never())->method('findActiveByKey');
        $this->userEmailAlreadyExists->check($user);
    }

    public function testEmailExistBlocMessageDoesNotExistsGetAnError(): void
    {
        $user = (new User())
            ->setEmail('email@mail.fr')
        ;

        $this->userRepository->expects($this->once())->method('emailExist')->willReturn(true);
        $this->blocRepository->expects($this->once())->method('findActiveByKey')->willReturn(null);

        $this->expectException(BlocNotFoundException::class);
        $this->expectExceptionMessage('bloc MESSAGE_EMAIL_ALREADY_EXISTS is missing');

        $this->userEmailAlreadyExists->check($user);
    }

    public function testEmailExistsGetAnError(): void
    {
        $user = (new User())
            ->setEmail('email@mail.fr')
        ;

        $bloc = (new Bloc())
            ->setContent('Le mail %email% existe déjà')
        ;

        $this->userRepository->expects($this->once())->method('emailExist')->willReturn(true);
        $this->blocRepository->expects($this->once())->method('findActiveByKey')->willReturn($bloc);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Le mail email@mail.fr existe déjà');

        $this->userEmailAlreadyExists->check($user);
    }

    public function testEmailExistsWithBlocWithoutEmailTagGetAnError(): void
    {
        $user = (new User())
            ->setEmail('email@mail.fr')
        ;

        $bloc = (new Bloc())
            ->setContent('L’adresse email est déjà lié a un compte, merci de vous connectez')
        ;

        $this->userRepository->expects($this->once())->method('emailExist')->willReturn(true);
        $this->blocRepository->expects($this->once())->method('findActiveByKey')->willReturn($bloc);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('L’adresse email est déjà lié a un compte, merci de vous connectez');

        $this->userEmailAlreadyExists->check($user);
    }
}
