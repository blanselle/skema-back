<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Diploma;

use App\Entity\Diploma\DiplomaChannel;
use App\Repository\Diploma\DiplomaChannelRepository;
use App\Tests\Functional\Controller\AbstractControllerTest;

class DiplomaChannelControllerTest extends AbstractControllerTest
{
    private DiplomaChannelRepository $diplomaRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->diplomaRepository = $this->em->getRepository(DiplomaChannel::class);
    }

    public function testDiplomaChannelIndexOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/diploma/channels');

        $this->assertResponseIsSuccessful();
    }

    public function testDiplomaChannelNewOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/diploma/channels/new');

        $this->assertResponseIsSuccessful();
    }

    public function testDiplomaChannelEditOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/diploma/channels/113/edit');

        $this->assertResponseIsSuccessful();
    }

    public function testDiplomaChannelDeleteOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/diploma/channels/113');

        $this->assertResponseStatusCodeSame(303);
    }
}