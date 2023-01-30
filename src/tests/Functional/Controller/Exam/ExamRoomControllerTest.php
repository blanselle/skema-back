<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Exam;


use App\Tests\Functional\Controller\AbstractControllerTest;

class ExamRoomControllerTest extends AbstractControllerTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testExamRoomEditOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/exams/sessions/1/edit/rooms');

        $this->assertResponseIsSuccessful();
    }

    public function testExamRoomDeleteOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/exams/sessions/1/rooms/1');

        $this->assertResponseStatusCodeSame(303);
    }
}