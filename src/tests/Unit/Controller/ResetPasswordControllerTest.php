<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\ResetPassword\ResetPasswordController;
use App\Service\Mail\ResetPasswordMailDispatcher;
use App\Service\Utils;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ResetPasswordControllerTest extends AbstractControllerTest
{
    private ResetPasswordController $controller;

    private SerializerInterface|MockObject $serializer;
    private ResetPasswordMailDispatcher|MockObject $resetPasswordMailDispatcher;
    private Utils|MockObject $utils;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->modules['serializer'] = $this->serializer;

        parent::setUp();

        $this->resetPasswordMailDispatcher = $this->createMock(ResetPasswordMailDispatcher::class);
        $this->utils = $this->createMock(Utils::class);

        $this->controller = new ResetPasswordController($this->utils);
        $this->controller->setContainer($this->container);
    }

    public function testResetPasswordRequestOk(): void
    {
        $email = 'admin@skema.fr';
        $request = new Request(content: json_encode(['email' => $email]));

        $this->resetPasswordMailDispatcher->expects($this->once())->method('dispatch')->with($this->equalTo($email));

        $content = $this->controller->request($request, $this->resetPasswordMailDispatcher);
        $this->assertSame(Response::HTTP_OK, $content->getStatusCode());
    }

    public function testResetPasswordRequestWithNoEmailGetAnError(): void
    {
        $request = new Request(content: json_encode(['test' => 'test']));

        $this->resetPasswordMailDispatcher->expects($this->never())->method('dispatch');

        $this->expectException(BadRequestException::class);

        $this->controller->request($request, $this->resetPasswordMailDispatcher);
        
    }
}