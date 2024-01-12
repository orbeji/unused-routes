<?php

namespace Orbeji\UnusedRoutes\Tests\Entity;

use Orbeji\UnusedRoutes\Entity\UsedRoute;
use PHPUnit\Framework\TestCase;

class UsedRouteTest extends TestCase
{
    public function testNewVisit(): void
    {
        $time = time();
        $route = 'route';

        $usedRoute = UsedRoute::newVisit($route);

        $this->assertEquals($route, $usedRoute->getRoute());
        $this->assertGreaterThanOrEqual($time, $usedRoute->getTimestamp());
        $this->assertEquals(1, $usedRoute->getVisits());
    }
    public function testGroupedData(): void
    {
        $time = time();
        $route = 'route';
        $visits = 5;

        $usedRoute = UsedRoute::fromGroupedData($route, $time, $visits);

        $this->assertEquals($route, $usedRoute->getRoute());
        $this->assertEquals($time, $usedRoute->getTimestamp());
        $this->assertEquals($visits, $usedRoute->getVisits());
    }
}
