<?php

declare(strict_types=1);

namespace App\Tests\Functional\api\EventSubscriber;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Constants\CV\DistinctionCodeConstants;
use App\Constants\Media\MediaCodeConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\CV\Bac\Bac;
use App\Entity\CV\Bac\BacChannel;
use App\Entity\CV\Bac\BacDistinction;
use App\Entity\CV\Cv;
use App\Entity\Media;
use App\Entity\Student;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class BacSubscriberTest extends ApiTestCase
{
    private const USER_TEST_EMAIL = 'candidate.bce@skema.fr';

    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();

        parent::bootKernel();

        $this->em = $this->getContainer()->get(EntityManagerInterface::class);
    }

    public function testBacNoDistinctionWithMediaFromUploadedToApproved()
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => self::USER_TEST_EMAIL]);
        /** @var Student $student */
        $student = $user->getStudent();

        $bacChannel = $this->em->getRepository(BacChannel::class)->findOneBy(['name' => 'Technologique']);

        $media = new Media();
        $media->setFile('toto.jpg')
            ->setStudent($student)
            ->setCode(MediaCodeConstants::CODE_BAC)
            ->setOriginalName('toto.jpg')
        ;
        $this->em->persist($media);
        $this->em->flush();

        $cv = new Cv();
        $cv->setStudent($student)
        ;
        $this->em->persist($cv);
        $this->em->flush();

        $bacDistinction = $this->em->getRepository(BacDistinction::class)->findOneBy(['code' => DistinctionCodeConstants::NO_DISTINCTION]);

        $bac = new Bac();
        $bac->setBacDistinction($bacDistinction)
            ->setIne('151150055')
            ->setRewardedYear(2015)
            ->setBacChannel($bacChannel)
            ->setDetail('Mon bac pro')
            ->setMedia($media)
            ->setCv($cv)
        ;

        $this->em->persist($bac);
        $this->em->flush();

        $this->assertSame(MediaWorflowStateConstants::STATE_ACCEPTED, $media->getState());
    }
}