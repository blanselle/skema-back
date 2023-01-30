<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Notification;

use App\Entity\Bloc\Bloc;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Entity\User;
use App\Manager\Admissibility\LandingPage\TokenManager;
use App\Repository\BlocRepository;
use App\Service\Bloc\BlocRewriter;
use App\Service\Notification\NotificationAdmissibilityResultDispatcher;
use App\Service\Notification\NotificationCenter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NotificationAdmissibilityResultDispatcherTest extends TestCase
{
    private NotificationCenter|MockObject $dispatcher;
    private BlocRewriter|MockObject $blocRewriter;
    private BlocRepository|MockObject $blocRepository;
    private TokenManager|MockObject $tokenManager;

    private NotificationAdmissibilityResultDispatcher $notificationAdmissibilityDispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher = $this->createMock(NotificationCenter::class);
        $this->blocRewriter = $this->createMock(BlocRewriter::class);
        $this->blocRepository = $this->createMock(BlocRepository::class);
        $this->tokenManager = $this->createMock(TokenManager::class);

        $this->notificationAdmissibilityDispatcher = new NotificationAdmissibilityResultDispatcher(
            'https://domain.com',
            $this->dispatcher,
            $this->blocRewriter,
            $this->blocRepository,
            $this->tokenManager,
        );
    }

    public function testDispatchIsOk(): void
    {
        $programChannel1 = (new ProgramChannel())
            ->setId(1)
        ;

        $programChannel2 = (new ProgramChannel())
            ->setId(2)
        ;

        $students = [
            (new Student())
                ->setProgramChannel($programChannel1)
                ->setUser((new User())
                    ->setFirstName('Henri 1')
                )
            ,
            (new Student())
                ->setProgramChannel($programChannel2)
                ->setUser((new User())
                    ->setFirstName('Henri 2')
                )
            ,

            (new Student())
                ->setProgramChannel($programChannel1)
                ->setUser((new User())
                    ->setFirstName('Henri 1')
                )
            ,
            (new Student())
                ->setProgramChannel($programChannel2)
                ->setUser((new User())
                    ->setFirstName('Henri 2')
                )
            ,
        ];

        $bloc1 = (new Bloc())
            ->setContent('content1')
            ->setLabel('label1')
        ;

        $bloc2 = (new Bloc())
            ->setContent('content2')
            ->setLabel('label2')
        ;

        $this->blocRepository->expects($this->exactly(2))->method('findActiveByKeyAndProgramChannel')->willReturn($bloc1, $bloc2);
        $this->blocRewriter->expects($this->exactly(4))->method('rewriteBloc')->willReturn($bloc1, $bloc2, $bloc1, $bloc2);
        $this->dispatcher->expects($this->exactly(4))->method('dispatch');

        foreach($students as $student) {
            $this->notificationAdmissibilityDispatcher->dispatch($student);
        }
    }
}