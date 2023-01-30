<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Exam;

use App\Tests\Functional\Controller\AbstractControllerTest;

class ExamSessionControllerTest extends AbstractControllerTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testExamSessionIndexOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/exams/sessions');

        $this->assertResponseIsSuccessful();
    }

    public function testExamSessionNewOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/exams/sessions/new');

        $this->assertResponseIsSuccessful();
    }

    public function testExamSessionEditOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/exams/sessions/1/edit');

        $this->assertResponseIsSuccessful();
    }

    public function testExamSessionDeleteOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/exams/sessions/1');

        $this->assertResponseStatusCodeSame(303);
    }
}