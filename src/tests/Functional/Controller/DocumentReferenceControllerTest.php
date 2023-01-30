<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Entity\Document\DocumentReference;
use App\Repository\Document\DocumentReferenceRepository;
use GuzzleHttp\Psr7\UploadedFile;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\HttpFoundation\Response;

class DocumentReferenceControllerTest extends AbstractControllerTest
{
    private DocumentReferenceRepository $documentReferenceRepository;

    private const FILE='uploads/sample.pdf';

    protected function setUp(): void
    {
        parent::setUp();

        $this->documentReferenceRepository = $this->em->getRepository(DocumentReference::class);
    }

    public function testDocumentReferenceIndexOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/document/reference');

        $this->assertResponseIsSuccessful();
    }

    public function testDocumentReferenceNewOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/document/reference/new');

        $this->assertResponseIsSuccessful();
    }

    public function testDocumentReferenceEditOk(): void
    {
        $documentReference = $this->provideDocumentReference();
        $this->saveDocumentReference($documentReference);
        $this->loginAsAdmin();
        $this->client->request('GET', sprintf('/admin/document/reference/%d/edit', $documentReference->getId()));

        $this->assertResponseIsSuccessful();
        
        $this->removeDocumentReference($documentReference);
    }
   
    public function testNewSubmitedFailed(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/document/reference/new');
        $this->client->submitForm('Sauvegarder', []);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testNewSubmitedOk(): void
    {
        $documentReference = $this->provideDocumentReference();
        $this->loginAsAdmin();
        $documentReference = $this->checkSubmitForm($documentReference, true);
        $this->removeDocumentReference($documentReference);
    }

    public function testEditSubmitedOk(): void
    {
        $documentReference = $this->provideDocumentReference();
        $this->saveDocumentReference($documentReference);
        $this->loginAsAdmin();
        $documentReference = $this->checkSubmitForm($documentReference);
        $this->removeDocumentReference($documentReference);
    }

    private function checkSubmitForm(DocumentReference $expectedDocumentReference, bool $createForm = false): DocumentReference
    {
        if ($createForm) {
            $formName = 'document_reference_create';
            /** @var Crawler */
            $crawler = $this->client->request('GET', '/admin/document/reference/new');
        } else {
            $formName = 'document_reference';
            /** @var Crawler */
            $crawler = $this->client->request('GET', sprintf('/admin/document/reference/%d/edit', $expectedDocumentReference->getId()));
        }

        $buttonCrawlerNode = $crawler->selectButton('Sauvegarder');
        /** @var Form */
        $form = $buttonCrawlerNode->form();

        $form[$formName . '[file]']->upload(dirname(__FILE__).'/../../'.self::FILE);
        $form[$formName . '[name]']->setValue($expectedDocumentReference->getName());

        $crawler = $this->client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_SEE_OTHER);
        $documentReference = $this->documentReferenceRepository->findOneByName($expectedDocumentReference->getName());
        $this->assertNotNull($documentReference);
        $this->assertSame($expectedDocumentReference->getName(), $documentReference->getName());
        $this->assertNotNull($documentReference->getFile());
        $this->assertStringContainsString('documents/document_reference', $documentReference->getFile());

        return $documentReference;
    }

    public function testDeleteOk(): void
    {
        $documentReference = $this->provideDocumentReference();
        $this->saveDocumentReference($documentReference);
        $this->loginAsAdmin();
        $this->client->request('GET', sprintf('/admin/document/reference/%d/edit', $documentReference->getId()));

        $this->client->submitForm(''); // delete button on edit page

        $documentReference = $this->documentReferenceRepository->findOneByName($documentReference->getName());
        $this->assertNull($documentReference);
    }

    private function saveDocumentReference(DocumentReference $documentReference): void
    {
        $this->em->persist($documentReference);
        $this->em->flush();
    }

    private function removeDocumentReference(DocumentReference $documentReference): void
    {
        $documentReference = $this->documentReferenceRepository->findOneById($documentReference->getId());
        $this->em->remove($documentReference);
        $this->em->flush();
    }

    private function provideDocumentReference(): DocumentReference
    {
        return (new DocumentReference())
            ->setName('DOCUMENT_TEST')
            ->setFile('documents/document_reference/phpTOdHp4-6253e7e4e1930.pdf')
        ;
    }
}