<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security;

use App\Entity\User;
use App\Security\UserAuthenticator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

class UserAuthenticatorTest extends TestCase
{
    private UrlGeneratorInterface|MockObject $urlGenerator;

    private Request|MockObject $request;
    
    private UserAuthenticator|MockObject $userAuthenticator;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->request = $this->createMock(Request::class);

        $this->userAuthenticator = new UserAuthenticator($this->urlGenerator);
    }

    public function testAuthenticate(): void
    {
        $params = [
            'email' => 'my-email@skema.fr',
            '_csrf_token' => 'this token',
            'password' => 'my password',
        ];
        $this->request->request = new InputBag($params);
        $this->request->setSession(new Session());

        $result = $this->userAuthenticator->authenticate($this->request);
        $this->assertTrue($result instanceof Passport);
        $this->assertSame($params['email'], $result->getBadges()['Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge']->getUserIdentifier());
        $this->assertSame($params['_csrf_token'], $result->getBadges()['Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge']->getCsrfToken());
        $this->assertSame($params['password'], $result->getBadges()['Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials']->getPassword());
    }

    public function testAuthentificationSuccessWithoutRedirection(): void
    {
        $this->request->setSession(new Session());

        $token = new PostAuthenticationToken(new User(), 'back-office', ['ROLE_ADMIN']);

        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('home')
            ->willReturn('https://site.com/')
        ;

        $result = $this->userAuthenticator->onAuthenticationSuccess($this->request, $token, 'back-office');
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
        $this->assertStringContainsString('https://site.com/', $result->getContent());
    }

    public function testAuthentificationSuccessWithRedirection(): void
    {
       
        /** @var Session|MockObject */
        $session = new Session(new MockArraySessionStorage());
        $session->set('_security.back-office.target_path', 'https://site.com/');

        $this->request
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($session)
        ;

        $token = new PostAuthenticationToken(new User(), 'back-office', ['ROLE_ADMIN']);

        $result = $this->userAuthenticator->onAuthenticationSuccess($this->request, $token, 'back-office');
        $this->assertSame(Response::HTTP_FOUND, $result->getStatusCode());
        $this->assertStringContainsString('https://site.com/', $result->getContent());
    }

    public function testGetLoginUrl(): void 
    {
        $url = 'https://site.com/';

        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->willReturn($url)
        ;

        $userAuthenticatorExtended = new class($this->urlGenerator) extends UserAuthenticator {
            public function __construct(UrlGeneratorInterface $urlGenerator)
            {
                parent::__construct($urlGenerator);
            }
            public function getLoginUrl(Request $request): string
            {
                return parent::getLoginUrl($request);
            }
        };

        $result = $userAuthenticatorExtended->getLoginUrl($this->request);

        $this->assertSame($url, $result);
    }
}