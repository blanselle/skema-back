<?php

declare(strict_types=1);

namespace App\Tests\Functional\api\Entity\Bloc;

use App\Entity\Bloc\Bloc;
use App\Tests\Functional\api\Entity\AbstractEntityTest;

class BlocTest extends AbstractEntityTest
{
    public function testBlocsListeOk(): void
    {
        static::createClient()->request('GET', '/api/blocs');
        $this->assertResponseIsSuccessful();
    }

    public function testBlocsItemOk(): void
    {
        $bloc = $this->em->getRepository(Bloc::class)->findOneBy([]);
        static::createClient()->request('GET', sprintf('/api/blocs/%d', $bloc->getId()));
        $this->assertResponseIsSuccessful();
    }

    public function testBlocsListWithTagGEOrderedByPositionAsc(): void
    {
        $result = static::createClient()->request('GET', '/api/blocs?tag.label=HOME_GE&order[position]=asc', ['headers' => ['accept' => 'application/json']]);
        $this->assertResponseIsSuccessful();
        $items = json_decode($result->getContent(), true);
        $this->assertTrue($this->checkOrder(items: $items, fieldPosition: 'position', sens: AbstractEntityTest::ASC));
    }

    public function testBlocsListWithTagGEOrderedByPositionDesc(): void
    {
        $result = static::createClient()->request('GET', '/api/blocs?tag.label=HOME_GE&order[position]=desc', ['headers' => ['accept' => 'application/json']]);
        $this->assertResponseIsSuccessful();
        $items = json_decode($result->getContent(), true);
        $this->assertTrue($this->checkOrder(items: $items, fieldPosition: 'position', sens: AbstractEntityTest::DESC));
    }

    public function testBlocListFilteredByProgramChannels(): void
    {
        $result = static::createClient()->request('GET', '/api/program_channels', ['headers' => ['accept' => 'application/json']]);
        $this->assertResponseIsSuccessful();
        $programChannels = json_decode($result->getContent(), true);
        $first = $programChannels[0]['id'];
        $second = $programChannels[1]['id'];

        $result = static::createClient()->request('GET', "/api/blocs?programChannels[]={$first}&programChannels[]={$second}", ['headers' => ['accept' => 'application/json']]);
        $this->assertResponseIsSuccessful();
        $items = json_decode($result->getContent(), true);

        $a = [$first, $second];
        foreach ($items as $item) {
            $filtered = array_filter($item['programChannels'], function($p) use ($a) {
                if (in_array($p['id'], $a)) {
                    return $p;
                }
            });

            $this->assertGreaterThan(0, count($filtered));
        }
    }
}