<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated,
        private ParameterBagInterface $params,
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $openApi = $openApi->withInfo((new Model\Info(
            $this->params->get('app_name'),
            $this->params->get('app_version'),
            $this->params->get('app_descripion'),
        ))->withExtensionProperty('info-key', 'Info value'));

        return $openApi;
    }
}