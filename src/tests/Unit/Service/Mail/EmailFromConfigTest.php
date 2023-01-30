<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Mail;

use App\Constants\Mail\MailConstants;
use App\Service\Mail\EmailFromConfig;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EmailFromConfigTest extends TestCase
{
    private ParameterBagInterface|MockObject $params;

    private EmailFromConfig $emailFromConfig; 

    protected function setUp(): void
    {
        parent::setUp();

        $this->params = $this->createMock(ParameterBagInterface::class);

        $this->params
            ->expects($this->any())
            ->method('get')
            ->willReturnCallBack(function($param) {
                return match ($param) {
                    MailConstants::MAIL_ADMIN => 'admin',
                };
            });

        $this->emailFromConfig = new EmailFromConfig(
            $this->params,
        );    
    }

    public function testGetEmail(): void 
    {
        $this->assertSame('admin', $this->emailFromConfig->get(MailConstants::MAIL_ADMIN));
    }
}