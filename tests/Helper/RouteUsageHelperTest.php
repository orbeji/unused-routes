<?php

namespace Orbeji\UnusedRoutes\Tests\Helper;

use Orbeji\UnusedRoutes\Entity\UsedRoute;
use Orbeji\UnusedRoutes\Helper\RouteUsageHelper;
use Orbeji\UnusedRoutes\Provider\UsageRouteProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class RouteUsageHelperTest extends TestCase
{
    public function testEmpty(): void
    {
        $routeUsageHelper = new RouteUsageHelper(
            $this->createStub(UsageRouteProviderInterface::class),
            $this->createStub(RouterInterface::class)
        );

        $result = $routeUsageHelper->getRoutesUsage(false);
        $this->assertEquals([], $result);
    }

    public function testUnusedRoute(): void
    {
        $usageRouteProvider = $this->createStub(UsageRouteProviderInterface::class);

        $usageRouteProvider->method('getRoutesUsage')
            ->willReturn([]);

        $router = $this->createStub(RouterInterface::class);
        $routeCollection = new RouteCollection();
        $routeCollection->add('route', new Route('route'));
        $router->method('getRouteCollection')->willReturn($routeCollection);
        $routeUsageHelper = new RouteUsageHelper(
            $usageRouteProvider,
            $router
        );

        $result = $routeUsageHelper->getRoutesUsage(false);
        $this->assertEquals([
            [
                "value" => "route",
                "count" => 0,
                "date" => "-"
            ]
        ], $result);
    }
    public function testUsedRoute(): void
    {
        $usageRouteProvider = $this->createStub(UsageRouteProviderInterface::class);

        $usedRoute = UsedRoute::newVisit('route');
        $usageRouteProvider->method('getRoutesUsage')
            ->willReturn([$usedRoute]);

        $router = $this->createStub(RouterInterface::class);
        $routeCollection = new RouteCollection();
        $routeCollection->add('route', new Route('route'));
        $router->method('getRouteCollection')->willReturn($routeCollection);
        $routeUsageHelper = new RouteUsageHelper(
            $usageRouteProvider,
            $router
        );

        $result = $routeUsageHelper->getRoutesUsage(true);
        $this->assertEquals([
            [
                "value" => "route",
                "count" => 1,
                "date" => date('d/m/Y', $usedRoute->getTimestamp()),
            ]
        ], $result);
    }
}
