<?php

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractControllerTest extends WebTestCase
{
    protected ContainerInterface|MockObject $container;

    protected array $modules = [];
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->modules['twig'] = new class{ public function render(){ return 'RENDU'; }};

        /** @var Container|MockObject */
        $this->container = $this->createMock(ContainerInterface::class);
        
        $logic = call_user_func_array([$this, 'logicalOr'], array_map(function($module) { return $this->equalTo($module); }, array_keys($this->modules)));

        $this->container
            ->expects($this->any())
            ->method('has')
            ->with($logic)
            ->willReturn(true)
        ;

        $this->container
            ->expects($this->any())
            ->method('get')
            ->with($logic)
            ->willReturnCallBack(function($param){
                return $this->modules[$param];
            });
        ;
    }
}