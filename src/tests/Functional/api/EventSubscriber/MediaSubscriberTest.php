<?php

declare(strict_types=1);

namespace App\Tests\Functional\api\EventSubscriber;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Constants\Media\MediaCodeConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Constants\User\StudentWorkflowStateConstants;
use App\Constants\User\StudentWorkflowTransitionConstants;
use App\Entity\Media;
use App\Entity\User;
use App\Service\Workflow\Media\MediaWorkflowManager;
use Doctrine\ORM\EntityManagerInterface;

class MediaSubscriberTest extends ApiTestCase
{
    private const USER_TEST_EMAIL = 'candidate.ast1@skema.fr';

    private EntityManagerInterface $em;
    private MediaWorkflowManager $mediaWorkflowManager;

    protected function setUp(): void
    {
        parent::setUp();

        parent::bootKernel();

        $this->em = $this->getContainer()->get(EntityManagerInterface::class);
        $this->mediaWorkflowManager = $this->getContainer()->get(MediaWorkflowManager::class);
    }

    public function testStudentWorkflowToEligibleWithMediaCrous()
    {
        /**
         * @var User $user
         */
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => self::USER_TEST_EMAIL]);
        $student = $user->getStudent();
        $student->setState(StudentWorkflowStateConstants::STATE_CHECK_BOURSIER);

        /** @var Media $media */
        $media = $this->em->getRepository(Media::class)->findOneBy(['type' => 'document_to_validate']);
        $media->setStudent($student);
        $media->setCode(MediaCodeConstants::CODE_CROUS);

        $this->mediaWorkflowManager->checkToAccepted($media);
        $this->em->flush();

        $this->assertSame(StudentWorkflowStateConstants::STATE_VALID, $student->getState());
    }

    public function testStudentWorkflowToEligibleWithMediaCertificatEligibility()
    {
        /**
         * @var User $user
         */
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => self::USER_TEST_EMAIL]);
        $student = $user->getStudent();
        $student->setState(StudentWorkflowStateConstants::STATE_VALID);

        /** @var Media $media */
        $media = $this->em->getRepository(Media::class)->findOneBy(['type' => 'document_to_validate']);
        $media->setStudent($student);
        $media->setCode(MediaCodeConstants::CODE_CERTIFICAT_ELIGIBILITE);

        $this->mediaWorkflowManager->checkToAccepted($media);
        $this->em->flush();

        $this->assertSame(StudentWorkflowStateConstants::STATE_ELIGIBLE, $student->getState());
    }

    public function testStudentWorkflowToEligibleWithMediaCertificatEligibilityNotEligible()
    {
        /**
         * @var User $user
         */
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => self::USER_TEST_EMAIL]);
        $student = $user->getStudent();
        $student->setState(StudentWorkflowStateConstants::STATE_CHECK_BOURSIER);
        $student->setTransition(StudentWorkflowTransitionConstants::AR_TO_CHECK);

        $media = new Media();
        $media->setFile('toto.jpg')
            ->setStudent($student)
            ->setCode(MediaCodeConstants::CODE_CERTIFICAT_ELIGIBILITE)
            ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
            ->setOriginalName('toto.jpg')
        ;
        $this->em->persist($media);
        $this->em->flush();

        /** @var Media $media */
        $media = $this->em->getRepository(Media::class)->findOneBy(['type' => 'document_to_validate']);
        $media->setStudent($student);
        $media->setCode(MediaCodeConstants::CODE_CROUS);
        $media->setState(MediaWorflowStateConstants::STATE_TO_CHECK);
        $this->em->flush();

        $this->mediaWorkflowManager->checkToAccepted($media);
        $this->em->flush();

        $this->assertSame(StudentWorkflowStateConstants::STATE_ELIGIBLE, $student->getState());
    }
}