<?php

declare(strict_types=1);

namespace App\Action\Experience;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\CV\Experience;
use App\Manager\ExperienceManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class GetDuration extends AbstractController
{
    public function __invoke(
        Request $request,
        SerializerInterface $serializer,
        ExperienceManager $experienceManager,
        ValidatorInterface $validator
    ): Response {
        /** @var Experience $experience */
        $experience = $serializer->deserialize(
            $request->getContent(),
            Experience::class,
            'json'
        );

        try {
            $validator->validate($experience, groups: ['experience:duration']);
        } catch (Exception $e) {
            return new JsonResponse(
                ["error" => $e->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        return new JsonResponse(
            ["duration" => $experienceManager->getDurationForExperience($experience)]
        );
    }
}
