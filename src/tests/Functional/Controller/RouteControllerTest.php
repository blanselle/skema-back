<?php

namespace App\Tests\Functional\Controller;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class RouteControllerTest extends AbstractControllerTest
{
    public function testRoutesDoesNotError(): void
    {
        $routes = self::getContainer()->get(RouterInterface::class)->getRouteCollection();
        $retry = [];

        /** @var Route $route */
        foreach ($routes as $route) {
            if (str_contains($route->getPath(), '/efconnect') ||
                str_contains($route->getPath(), '{') ||
                str_contains($route->getPath(), '/dashboard') || // because is render in an template
                str_contains($route->getPath(), '/api') || // routes only for student
                str_contains($route->getPath(), '/export-list-file') // export file
            ) {
                continue;
            }

            $methods = $route->getMethods();
            if ([] === $methods) {
                $methods[] = 'GET';
            }

            foreach ($methods as $method) {
                if ($method !== 'GET') {
                    continue;
                }
                $this->loginAsAdmin();
                $this->client->request($method, $route->getPath());

                self::assertLessThan(
                    500,
                    $this->client->getResponse()->getStatusCode(),
                    sprintf('Route "%s" return 500', $route->getPath())
                );
            }
        }
    }
}