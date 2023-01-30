<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Exam;


use App\Tests\Functional\Controller\AbstractControllerTest;
use Symfony\Component\HttpFoundation\Response;

class ExamClassificationControllerTest extends AbstractControllerTest
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testExamClassificationIndexOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/exams/sessions/classification');

        $this->assertResponseIsSuccessful();
    }

    public function testExamClassificationNewOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/exams/sessions/classification/new');

        $this->assertResponseIsSuccessful();
    }

    public function testExamClassificationEditOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/exams/sessions/classification/10/edit');

        $this->assertResponseIsSuccessful();
    }

    public function testExamClassificationDeleteOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/exams/sessions/classification/10');

        $this->assertResponseStatusCodeSame(Response::HTTP_SEE_OTHER);
    }
}