<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Parameter;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;

/**
 * @codeCoverageIgnore
 */
final class CheckOralTestStudentState implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['state'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'state' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            ref: 'Check oralTestStudent state',
            get: new Model\Operation(
                operationId: 'checkOralTestStudentId',
                tags: ['OralTestStudent'],
                responses: [
                    '200' => [
                        'description' => 'Return oralTestStudent state',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'state' => ['type' => 'string'],
                                    ]
                                ],
                                'example' => [
                                    'status' => 'waiting_for_treatment',
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Check oralTestStudent state.',
                parameters: [new Parameter(
                    name: 'id',
                    in: 'path',
                    description: 'Resource identifier',
                )],
            ),
        );
        $openApi->getPaths()->addPath('/api/oral_test_students/{id}/check', $pathItem);

        return $openApi;
    }
}
