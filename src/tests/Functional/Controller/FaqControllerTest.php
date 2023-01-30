<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Entity\Faq\Faq;
use App\Entity\Faq\FaqTopic;
use App\Repository\Faq\FaqRepository;
use App\Repository\Faq\FaqTopicRepository;
use Symfony\Component\HttpFoundation\Response;

class FaqControllerTest extends AbstractControllerTest
{
    private FaqRepository $faqRepository;
    private FaqTopicRepository $faqTopicRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->faqRepository = $this->em->getRepository(Faq::class);
        $this->faqTopicRepository = $this->em->getRepository(FaqTopic::class);

    }

    public function testFaqIndexOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/faqs');

        $this->assertResponseIsSuccessful();
    }

    public function testFaqNewOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/faqs/new');

        $this->assertResponseIsSuccessful();
    }

    public function testFaqEditOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/faqs/28/edit');

        $this->assertResponseIsSuccessful();
    }

    public function testFaqDeleteOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/faqs/28');

        $this->assertResponseStatusCodeSame(Response::HTTP_SEE_OTHER);
    }

    public function testFaqNewSubmitedOk(): void
    {
        $faq = $this->provideFaq();
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/faqs/new');
        $this->checkSubmitForm($faq);
        $this->removeFaq($faq);
    }

    public function testEditSubmitedOk(): void
    {
        $faq = $this->provideFaq();
        $this->saveFaq($faq);
        $this->loginAsAdmin();
        $this->client->request('GET', sprintf('/admin/faqs/%d/edit', $faq->getId()));
        $faq = $this->checkSubmitForm($faq);
        $this->removeFaq($faq);
    }

    public function testDeleteOk(): void
    {
        $faq = $this->provideFaq();
        $this->saveFaq($faq);
        $this->loginAsAdmin();
        $this->client->request('GET', sprintf('/admin/faqs/%d/edit', $faq->getId()));

        $this->client->submitForm('');

        $faq = $this->faqRepository->findOneById($faq->getId());

        $this->assertNull($faq);
    }

    private function checkSubmitForm(Faq $expectedFaq): Faq
    {
        $this->client->submitForm('Sauvegarder', [
            'faq[question]' => $expectedFaq->getQuestion(),
            'faq[answer]' => $expectedFaq->getAnswer(),
            'faq[topics]' => $expectedFaq->getTopics()[0]->getId(),
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_SEE_OTHER);

        $faq = $this->faqRepository->find($expectedFaq->getId());

        $this->assertNotNull($faq);
        $this->assertSame($expectedFaq->getQuestion(), $faq->getQuestion());
        $this->assertSame($expectedFaq->getAnswer(), $faq->getAnswer());
        $this->assertSame($expectedFaq->getTopics()[0]->getId(), $faq->getTopics()[0]->getId());

        return $faq;
    }

    private function provideFaq(): Faq
    {
        $faq = new Faq();
        $faq->setQuestion('Is it a test ?')
            ->setAnswer('Yes')
            ->addTopic($this->faqTopicRepository->findAll()[0])
            ->setId(9999)
        ;
        $this->saveFaq($faq);

        return $faq;
    }

    private function saveFaq(Faq $faq): void
    {
        $this->em->persist($faq);
        $this->em->flush();
    }

    private function removeFaq(Faq $faq): void
    {
        $faq = $this->faqRepository->find($faq->getId());
        $this->em->remove($faq);
        $this->em->flush();
    }
}