<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Exam;


use App\Tests\Functional\Controller\AbstractControllerTest;

class ExamInscriptionsControllerTest  extends AbstractControllerTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testExamInscriptionsIndexOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/exams/sessions/inscription');

        $this->assertResponseIsSuccessful();
    }

    public function testExamInscriptionsEditOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/exams/sessions/inscription/student/1/edit');

        $this->assertResponseIsSuccessful();
    }

    public function testExamInscriptionsDetailsOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/exams/sessions/inscription/session/1');

        $this->assertResponseIsSuccessful();
    }

    public function testExamInscriptionsDeleteOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/exams/sessions/1/rooms/1');

        $this->assertResponseStatusCodeSame(303);
    }
}