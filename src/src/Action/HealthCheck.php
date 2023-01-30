<?php

declare(strict_types=1);

namespace App\Action;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthCheck
{
    #[Route('/api/ping', name: 'app_health_check', methods: ['GET'])]
    public function __invoke(): Response
    {
        return new JsonResponse('pong');
    }
}
