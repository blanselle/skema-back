<?php

declare(strict_types=1);

namespace App\Tests\Functional\EventSubscriber\Parameter;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Constants\Parameters\ParametersKeyTypeConstants;
use App\Entity\Parameter\Parameter;
use App\Entity\Parameter\ParameterKey;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class ParameterSubscriberTest extends ApiTestCase
{ 
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();

        $this->em = $this->getContainer()->get(EntityManagerInterface::class);
    }

    public function testRewriteDateValue(): void
    {
        $parameter = $this->provideParameter();
        $parameter->setValueDateTime(new DateTime());
        $parameter->getKey()->setType(ParametersKeyTypeConstants::DATE);

        $this->saveParameter($parameter);
        

        $result = static::createClient()->request('GET', sprintf('/api/parameters/%d', $parameter->getId()), ['headers' => ['accept' => 'application/json']]);
        $result = json_decode($result->getContent());

        $this->assertSame($parameter->getValueDateTime()->getTimestamp(), (new DateTime($result->value))->getTimestamp());
    }

    public function testRewriteNumberValue(): void
    {
        $parameter = $this->provideParameter();
        $parameter->setValueNumber(1743622);
        $parameter->getKey()->setType(ParametersKeyTypeConstants::NUMBER);

        $this->saveParameter($parameter);

        $result = static::createClient()->request('GET', sprintf('/api/parameters/%d', $parameter->getId()), ['headers' => ['accept' => 'application/json']]);
        $result = json_decode($result->getContent());

        $this->assertSame($parameter->getValueNumber(), $result->value);
    }

    public function testRewriteStringValue(): void
    {
        $parameter = $this->provideParameter();
        $parameter->setValueString('test');
        $parameter->getKey()->setType(ParametersKeyTypeConstants::TEXT);

        $this->saveParameter($parameter);

        $result = static::createClient()->request('GET', sprintf('/api/parameters/%d', $parameter->getId()), ['headers' => ['accept' => 'application/json']]);
        $result = json_decode($result->getContent());

        $this->assertSame($parameter->getValueString(), $result->value);
    }

    private function provideParameter(): Parameter
    {
        $parameter = (new Parameter())
            ->setId(1)
            ->setProgramChannels(new ArrayCollection())
            ->setCampuses(new ArrayCollection())
            ->setKey((new ParameterKey())
                ->setId(0)
                ->setDescription('la description')
                ->setName('name')
            )
        ;

        return $parameter;
    }

    private function saveParameter(Parameter $parameter): void
    {
        if(null !== $parameter->getKey()) {
            $this->em->persist($parameter->getKey());
        }
        $this->em->persist($parameter);
        $this->em->flush();
    }
}