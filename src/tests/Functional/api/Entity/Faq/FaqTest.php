<?php

declare(strict_types=1);

namespace App\Tests\Functional\api\Entity\Faq;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class FaqTest extends ApiTestCase
{
    public function testGetFaqOk(): void
    {
        static::createClient()->request('GET', '/api/faq_topics');
        $this->assertResponseIsSuccessful();
    }

    public function testGetOneFaqOk(): void
    {        
        static::createClient()->request('GET', '/api/faq_topics/7');
        $this->assertResponseIsSuccessful();
    }
}