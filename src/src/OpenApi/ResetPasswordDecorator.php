<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\OpenApi;
use ArrayObject;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @codeCoverageIgnore
 */
final class ResetPasswordDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated,
        private UrlGeneratorInterface $router,
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Credentials'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'example' => 'candidate.ast1@skema.fr',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'mdp',
                ],
            ],
        ]);

        $schemas['Token'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'example' => 'eyJ0eX....',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'new password',
                ],
            ],
        ]);

        $schemas['ChangePassword'] = new ArrayObject([
            'type' => 'object',
            'properties' => [
                'old_password' => [
                    'type' => 'string',
                    'example' => 'mdp',
                ],
                'new_password' => [
                    'type' => 'string',
                    'example' => 'mdp_new',
                ],
                'confirmation_password' => [
                    'type' => 'string',
                    'example' => 'mdp_new',
                ],
            ],
        ]);

        $items = [];
        $items['reset_password_request'] = new Model\PathItem(
            ref: 'RESET-PASSWORD',
            post: new Model\Operation(
                operationId: 'resetPassword-request',
                tags: ['Reset password'],
                responses: [
                            '200' => [
                                'description' => 'The reset password email was sent. The contains a link to the password reset form with the token as GET parameter. ex: https://frontend-skema.pictime-groupe-integ.com/Motdepasse?token=eyJ0eX',
                                'content' => [
                                    'application/json' => [
                                        'code' => 200,
                                        'message' => 'reset password request sent',
                                    ],
                                ],
                            ],
                        ],
                summary: 'First step of the reset password',
                requestBody: new Model\RequestBody(
                    description: 'Send a email to reset password',
                    content: new ArrayObject([
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Credentials',
                                    ],
                                ],
                            ]),
                ),
            ),
        );

        $items['reset_password'] = new Model\PathItem(
            ref: 'RESET-PASSWORD',
            post: new Model\Operation(
                operationId: 'resetPassword',
                tags: ['Reset password'],
                responses: [
                            '200' => [
                                'description' => 'The password is reset',
                                'content' => [
                                    'application/json' => [
                                        'code' => 200,
                                        'message' => 'password edited with success',
                                    ],
                                ],
                            ],
                        ],
                summary: 'Second step of the reset password',
                requestBody: new Model\RequestBody(
                    description: 'Reset the password',
                    content: new ArrayObject([
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Token',
                                    ],
                                ],
                            ]),
                ),
            ),
        );

        $items['change_password'] = new Model\PathItem(
            ref: 'RESET-PASSWORD',
            post: new Model\Operation(
                operationId: 'change-password',
                tags: ['Reset password'],
                responses: [
                    '200' => [
                        'description' => 'The password is reset',
                        'content' => [
                            'application/json' => [
                                'code' => 200,
                                'message' => 'password edited with success',
                            ],
                        ],
                    ],
                ],
                summary: 'Change password',
                requestBody: new Model\RequestBody(
                    description: 'Reset the password',
                    content: new ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/ChangePassword',
                            ],
                        ],
                    ]),
                ),
            ),
        );

        foreach ($items as $route => $item) {
            $openApi->getPaths()->addPath($this->router->generate($route), $item);
        }

        return $openApi;
    }
}
