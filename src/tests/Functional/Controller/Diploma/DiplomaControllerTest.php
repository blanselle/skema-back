<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Diploma;

use App\Entity\Diploma\Diploma;
use App\Tests\Functional\Controller\AbstractControllerTest;

class DiplomaControllerTest extends AbstractControllerTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->diplomaRepository = $this->em->getRepository(Diploma::class);
    }

    public function testDiplomaIndexOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/diplomas');

        $this->assertResponseIsSuccessful();
    }

    public function testDiplomaNewOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/diplomas/new');

        $this->assertResponseIsSuccessful();
    }

    public function testDiplomaEditOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('GET', '/admin/diplomas/22/edit');

        $this->assertResponseIsSuccessful();
    }

    public function testDiplomaDeleteOk(): void
    {
        $this->loginAsAdmin();
        $this->client->request('POST', '/admin/diplomas/22');

        $this->assertResponseStatusCodeSame(303);
    }
}