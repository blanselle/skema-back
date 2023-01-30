<?php

declare(strict_types=1);

namespace App\Tests\Functional\Media;

use App\Entity\Media;
use App\Entity\User;
use App\Message\NotificationMessage;
use App\Tests\Functional\api\AbstractApiTest;

class MediaActionTest extends AbstractApiTest
{
    private const TEST_EMAIL = 'test.media.candidate.ast1@skema.fr';
    private const TEST_PASSWORD = 'Azertyuiop123!';
    
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testRejectToCheckMediaWithCoordinatorIsOk(): void
    {
        $this->createAst1Created(self::TEST_EMAIL, self::TEST_PASSWORD);

        $this->activeAccount();

        $token = $this->getToken(self::TEST_EMAIL, self::TEST_PASSWORD);
        $userInfos = $this->getMe($token);
        
        $response = $this->createMedia($token);
        $this->assertResponseIsSuccessful();
        $media = json_decode($response->getContent(), true);

        $response = $this->request(
            method: 'GET',
            url: $userInfos['student']['administrativeRecord'],
            token: $token,
        );
        $this->assertResponseIsSuccessful();
        $administrativeRecord = json_decode($response->getContent(), true);

        $this->request(
            method: 'PUT', 
            url: '/api/administrative_records/' . $administrativeRecord['studentLastDiploma']['id'],
            token: $token,
            body: [
                'diplomaMedias' => [$media['@id']],
            ]
        );

        $admin = $this->em->getRepository(User::class)->findOneByEmail('coordinator@skema.fr');

        $this->client->loginUser($admin, 'back_office');

        $response = $this->client->request('POST', sprintf('/admin/ajax/media/notification/%s/send', $media['id']), [
            'extra' => [
                'parameters' => [
                    'subject' => 'Document+incomplet',
                    'content' => 'blabla',
                    'media' => $media['id'],
                    'tag'	=> 'media_rejection',
                    'user'	=> $userInfos['id'],
                ],
            ],
        ]);

        $this->assertResponseIsSuccessful();


        $this->assertSame('rejected', $this->em->getRepository(Media::class)->findOneById($media['id'])->getState());

        $this->em->remove($this->em->getRepository(User::class)->findOneByEmail(self::TEST_EMAIL));
        $this->em->flush();
    }

    public function testRejectAcceptedMediaWithCoordinatorGetAnError(): void
    {
        $this->createAst1Created(self::TEST_EMAIL, self::TEST_PASSWORD);

        $this->activeAccount();

        $token = $this->getToken(self::TEST_EMAIL, self::TEST_PASSWORD);
        $userInfos = $this->getMe($token);
        
        $response = $this->createMedia($token);
        $this->assertResponseIsSuccessful();
        $media = json_decode($response->getContent(), true);

        $response = $this->request(
            method: 'GET',
            url: $userInfos['student']['administrativeRecord'],
            token: $token,
        );
        $this->assertResponseIsSuccessful();
        $administrativeRecord = json_decode($response->getContent(), true);

        $this->request(
            method: 'PUT', 
            url: '/api/administrative_records/' . $administrativeRecord['studentLastDiploma']['id'],
            token: $token,
            body: [
                'diplomaMedias' => [$media['@id']],
            ]
        );

        $admin = $this->em->getRepository(User::class)->findOneByEmail('coordinator@skema.fr');

        $this->client->loginUser($admin, 'back_office');

        $response = $this->client->request('POST', sprintf('/admin/ajax/media/validate', $media['id']), [
            'extra' => [
                'parameters' => [
                    'media' => $media['id'],
                    'choice' => 'accepted'
                ],
            ],
        ]);

        $response = $this->client->request('POST', sprintf('/admin/ajax/media/notification/%s/send', $media['id']), [
            'extra' => [
                'parameters' => [
                    'subject' => 'Document+incomplet',
                    'content' => 'blabla',
                    'media' => $media['id'],
                    'tag'	=> 'media_rejection',
                    'user'	=> $userInfos['id'],
                ],
            ],
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertSame('accepted', $this->em->getRepository(Media::class)->findOneById($media['id'])->getState());

        $this->em->remove($this->em->getRepository(User::class)->findOneByEmail(self::TEST_EMAIL));
        $this->em->flush();
    }

    public function testRejectToCheckMediaWithResponsableIsOk(): void
    {
        $this->createAst1Created(self::TEST_EMAIL, self::TEST_PASSWORD);

        $this->activeAccount();

        $token = $this->getToken(self::TEST_EMAIL, self::TEST_PASSWORD);
        $userInfos = $this->getMe($token);
        
        $response = $this->createMedia($token);
        $this->assertResponseIsSuccessful();
        $media = json_decode($response->getContent(), true);

        $response = $this->request(
            method: 'GET',
            url: $userInfos['student']['administrativeRecord'],
            token: $token,
        );
        $this->assertResponseIsSuccessful();
        $administrativeRecord = json_decode($response->getContent(), true);

        $this->request(
            method: 'PUT', 
            url: '/api/administrative_records/' . $administrativeRecord['studentLastDiploma']['id'],
            token: $token,
            body: [
                'diplomaMedias' => [$media['@id']],
            ]
        );

        $admin = $this->em->getRepository(User::class)->findOneByEmail('responsable@skema.fr');

        $this->client->loginUser($admin, 'back_office');

        $response = $this->client->request('POST', sprintf('/admin/ajax/media/validate', $media['id']), [
            'extra' => [
                'parameters' => [
                    'media' => $media['id'],
                    'choice' => 'accepted'
                ],
            ],
        ]);

        $response = $this->client->request('POST', sprintf('/admin/ajax/media/notification/%s/send', $media['id']), [
            'extra' => [
                'parameters' => [
                    'subject' => 'Document+incomplet',
                    'content' => 'blabla',
                    'media' => $media['id'],
                    'tag'	=> 'media_rejection',
                    'user'	=> $userInfos['id'],
                ],
            ],
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertSame('rejected', $this->em->getRepository(Media::class)->findOneById($media['id'])->getState());

        $this->em->remove($this->em->getRepository(User::class)->findOneByEmail(self::TEST_EMAIL));
        $this->em->flush();
    }

    public function testTransfertToCheckMediaWithCoordinatorIsOk(): void
    {
        $this->createAst1Created(self::TEST_EMAIL, self::TEST_PASSWORD);

        $this->activeAccount();

        $token = $this->getToken(self::TEST_EMAIL, self::TEST_PASSWORD);
        $userInfos = $this->getMe($token);
        
        $response = $this->createMedia($token);
        $this->assertResponseIsSuccessful();
        $media = json_decode($response->getContent(), true);

        $response = $this->request(
            method: 'GET',
            url: $userInfos['student']['administrativeRecord'],
            token: $token,
        );
        $this->assertResponseIsSuccessful();
        $administrativeRecord = json_decode($response->getContent(), true);

        $this->request(
            method: 'PUT', 
            url: '/api/administrative_records/' . $administrativeRecord['studentLastDiploma']['id'],
            token: $token,
            body: [
                'diplomaMedias' => [$media['@id']],
            ]
        );

        $admin = $this->em->getRepository(User::class)->findOneByEmail('coordinator@skema.fr');

        $this->client->loginUser($admin, 'back_office');

        $response = $this->client->request('POST', sprintf('/admin/ajax/media/notification/%s/send', $media['id']), [
            'extra' => [
                'parameters' => [
                    'subject' => 'Document+incomplet',
                    'content' => 'blabla',
                    'tag'	=> 'media_transfer',
                    'receiver'	=> $this->em->getRepository(User::class)->findOneByEmail('responsable@skema.fr')->getId(),
                ],
            ],
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertSame('transfered', $this->em->getRepository(Media::class)->findOneById($media['id'])->getState());
        /** @var InMemoryTransport $transport */
        $transport = $this->getContainer()->get('messenger.transport.async_notifier');
        $this->assertNotEmpty($transport->getSent());
        /** @var NotificationMessage $notificationMessage */
        $notificationMessage = $transport->getSent()[0]->getMessage();
        
        $this->assertSame('Document+incomplet', $notificationMessage->getSubject());
        $this->assertSame(
            sprintf(
                'Transfert du document Certificat de scolarité pour la candidature %s. blabla',
                $userInfos['student']['identifier'],
            ), 
            $notificationMessage->getContent()
        );

        $this->em->remove($this->em->getRepository(User::class)->findOneByEmail(self::TEST_EMAIL));
        $this->em->flush();
    }
}