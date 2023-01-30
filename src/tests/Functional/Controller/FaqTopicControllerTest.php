<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;


use App\Entity\Faq\FaqTopic;
use App\Entity\ProgramChannel;
use App\Repository\ProgramChannelRepository;
use App\Repository\Faq\FaqTopicRepository;
use Symfony\Component\HttpFoundation\Response;

class FaqTopicControllerTest extends AbstractControllerTest
{
    private FaqTopicRepository $faqTopicRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->faqTopicRepository = $this->em->getRepository(FaqTopic::class);
        $this->programChannelRepository = $this->em->getRepository(ProgramChannel::class);
    }

    public function testFaqIndexOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/faqs/topic');

        $this->assertResponseIsSuccessful();
    }

    public function testFaqNewOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/faqs/topic/new');

        $this->assertResponseIsSuccessful();
    }

    public function testFaqEditOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/faqs/topic/7/edit');

        $this->assertResponseIsSuccessful();
    }

    public function testFaqDeleteOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/faqs/topic/7');

        $this->assertResponseStatusCodeSame(Response::HTTP_SEE_OTHER);
    }

    public function testFaqNewSubmitedOk(): void
    {
        $faq = $this->provideFaqTopic();
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/faqs/topic/new');
        $this->checkSubmitForm($faq);
        $this->removeFaq($faq);
    }

    public function testEditSubmitedOk(): void
    {
        $faq = $this->provideFaqTopic();
        $this->saveFaq($faq);
        $this->loginAsAdmin();
        $this->client->request('GET', sprintf('/admin/faqs/topic/%d/edit', $faq->getId()));
        $faq = $this->checkSubmitForm($faq);
        $this->removeFaq($faq);
    }

    public function testDeleteOk(): void
    {
        $faq = $this->provideFaqTopic();
        $this->saveFaq($faq);
        $this->loginAsAdmin();
        $this->client->request('GET', sprintf('/admin/faqs/topic/%d/edit', $faq->getId()));

        $this->client->submitForm('');

        $faq = $this->faqTopicRepository->findOneById($faq->getId());

        $this->assertNull($faq);
    }

    private function checkSubmitForm(FaqTopic $expectedFaq): FaqTopic
    {
        $this->client->submitForm('Sauvegarder', [
            'faq_topic[label]' => $expectedFaq->getLabel(),
            'faq_topic[programChannels]' => $expectedFaq->getProgramChannels()[0]->getId(),
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_SEE_OTHER);

        $faq = $this->faqTopicRepository->find($expectedFaq->getId());

        $this->assertNotNull($faq);
        $this->assertSame($expectedFaq->getLabel(), $faq->getLabel());
        $this->assertSame($expectedFaq->getProgramChannels()[0]->getId(), $faq->getProgramChannels()[0]->getId());

        return $faq;
    }

    private function provideFaqTopic(): FaqTopic
    {
        $faqTopic = new FaqTopic();
        $faqTopic->setId(999)
            ->setLabel('test')
            ->addProgramChannel($this->programChannelRepository->findAll()[0])
        ;
        $this->saveFaq($faqTopic);

        return $faqTopic;
    }

    private function saveFaq(FaqTopic $faqTopic): void
    {
        $this->em->persist($faqTopic);
        $this->em->flush();
    }

    private function removeFaq(FaqTopic $faqTopic): void
    {
        $faqTopic = $this->faqTopicRepository->find($faqTopic->getId());
        $this->em->remove($faqTopic);
        $this->em->flush();
    }
}