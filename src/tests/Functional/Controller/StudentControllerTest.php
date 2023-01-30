<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Constants\User\StudentWorkflowStateConstants;
use App\Constants\User\UserRoleConstants;
use App\Entity\Student;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\Response;

class StudentControllerTest extends AbstractControllerTest
{
    private StudentRepository $studentRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studentRepository = $this->em->getRepository(Student::class);
    }

    public function providerRoutes(): iterable
    {
        yield ['GET', '/admin/students', UserRoleConstants::ROLE_ADMIN, Response::HTTP_OK];
        yield ['GET', '/admin/students?identifier=&lastname=&state=&order=identifier&direction=ASC', UserRoleConstants::ROLE_ADMIN, Response::HTTP_OK];
        yield ['GET', '/admin/students/1/edit', UserRoleConstants::ROLE_ADMIN, Response::HTTP_OK];
    }

    public function testAcceptedExemptionOk(): void
    {
        $student = $this->studentRepository->find(1);
        $student->setState(StudentWorkflowStateConstants::STATE_EXEMPTION);

        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/students/1/edit');

        $this->client->submitForm('Valider la dérogation');

        $student = $this->studentRepository->find(1);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame(StudentWorkflowStateConstants::STATE_CREATED, $student->getState());
    }

    public function testRejectedExemptionOk(): void
    {
        $student = $this->studentRepository->find(1);
        $student->setState(StudentWorkflowStateConstants::STATE_EXEMPTION);

        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/students/1/edit');

        $this->client->submitForm('Refuser la dérogation');

        $student = $this->studentRepository->find(1);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame(StudentWorkflowStateConstants::STATE_REJECTED, $student->getState());
    }
}