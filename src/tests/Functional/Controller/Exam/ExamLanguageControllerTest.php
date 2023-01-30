<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Exam;


use App\Entity\Exam\ExamLanguage;
use App\Tests\Functional\Controller\AbstractControllerTest;

class ExamLanguageControllerTest extends AbstractControllerTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testExamLanguageIndexOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/exam/languages');

        $this->assertResponseIsSuccessful();
    }

    public function testExamLanguageNewOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/exam/languages/new');

        $this->assertResponseIsSuccessful();
    }

    public function testExamLanguageEditOk(): void
    {
        $this->loginAsAdmin();

        $examLanguage = $this->em->getRepository(ExamLanguage::class)->findOneBy([]);
        $this->client->request('GET', sprintf('/admin/exam/languages/%d/edit', $examLanguage->getId()));

        $this->assertResponseIsSuccessful();
    }

    public function testExamLanguageDeleteOk(): void
    {
        $this->loginAsAdmin();

        $examLanguage = $this->em->getRepository(ExamLanguage::class)->findOneBy([]);
        $this->client->request('POST', sprintf('/admin/exam/languages/%d', $examLanguage->getId()));

        $this->assertResponseStatusCodeSame(303);
    }
}