<?php

declare(strict_types=1);

namespace App\Tests\Unit\Twig;

use App\Entity\User;
use App\Twig\Navigation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Twig\TwigFunction;

class NavigationTest extends TestCase
{
    private Security|MockObject $security;
    private RequestStack|MockObject $requestStack;
    private Request|MockObject $request;

    private Navigation $navigation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->security = $this->createMock(Security::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->request = $this->createMock(Request::class);

        $this->requestStack
            ->method('getCurrentRequest')
            ->willReturn($this->request)
        ;

        $this->navigation = new Navigation(
            $this->security,
            $this->requestStack,
        );

        $this->request
            ->method('get')
            ->with($this->equalTo('_route'))
            ->willReturn('home')
        ;
    }

    public function testCurrentNavigationOk(): void
    {
        $this->security
            ->expects($this->any())
            ->method('getUser')
            ->willReturn((new User()))
        ;

        $this->security
            ->expects($this->any())
            ->method('isGranted')
            ->willReturn(true)
        ;
        $navigation = $this->navigation->navigation();

        $this->checkNavigation($navigation);
    }

    public function testMockedNavigation(): void
    {
        $this->security
            ->expects($this->any())
            ->method('getUser')
            ->willReturn((new User()))
        ;

        $this->security
            ->expects($this->any())
            ->method('isGranted')
            ->willReturnCallback(function ($role) {
                return $role === 'ROLE_COORDINATOR';
            })
        ;
        $navigation = $this->navigation->rewriteNavigation($this->provideNavigationConfig());
        
        $this->checkNavigation($navigation);
        $this->assertFalse(isset($navigation['categories']['forbidden']));
        $home = $navigation['categories']['home'];
        $this->assertFalse(isset($home['forbidden']));
        $this->assertTrue($home['active']);
        $this->assertTrue($home['pages']['home']['active']);
    }

    public function testNotConnectedUser(): void
    {        
        $navigation = $this->navigation->rewriteNavigation($this->provideNavigationConfig());
        
        $this->checkNavigation($navigation);
    }

    public function testGetFunctionOk(): void
    {
        $result = $this->navigation->getFunctions();
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(TwigFunction::class, $result[0]);
    }

    private function checkNavigation(array $navigation): void
    {
        $this->assertTrue(isset($navigation['categories']));
        foreach ($navigation['categories'] as $category) {
            $this->checkCategorie($category);
        }
    }

    private function checkCategorie(array $category): void
    {
        $this->fieldExist($category);
        $this->fieldExist($category, 'label');
        foreach ($category['pages'] as $page) {
            $this->checkPage($page);
        }
    }

    private function checkPage(array $page): void
    {
        $this->fieldExist($page);
        $this->fieldExist($page, 'label');
    }

    private function fieldExist(array $subject, string $field = 'active'): void
    {
        $this->assertTrue(isset($subject[$field]));
    }

    private function provideNavigationConfig(): array
    {
        return  [
            "categories" => [
                "home" => [
                    "label" => "Dashboard",
                    "role" => "ROLE_COORDINATOR",
                    "pages" => [
                        "home" => [
                            "label" => "Accueil",
                        ],
                        "forbidden" => [
                            "label" => "Accueil",
                            "role" => "ROLE_ADMIN",
                        ],
                    ],
                ],
                "forbidden" => [
                    "label" => "Dashboard",
                    "role" => "ROLE_ADMIN",
                    "pages" => [
                        "home" => [
                            "label" => "Accueil",
                        ],
                    ],
                ],
                "parameter" => [
                    "label" => "Paramétrage",
                    "role" => "ROLE_COORDINATOR",
                    "pages" => [
                        "parameter_index" => [
                            "label" => "Paramètre",
                        ],
                        "event_index" => [
                            "label" => "Evenement",
                        ],
                        "bloc_index" => [
                            "label" => "Bloc",
                        ],
                        "exam_index" => [
                            "label" => "Examen",
                        ],
                        "program_channel_index" => [
                            "label" => "Program",
                        ],
                        "program_index" => [
                            "label" => "Voie de concours",
                        ],
                    ]
                ]
            ]
        ];
    }
}