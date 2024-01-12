<?php

namespace Orbeji\UnusedRoutes\Tests\Provider;

use Orbeji\UnusedRoutes\Entity\UsedRoute;
use Orbeji\UnusedRoutes\Provider\FileUsageRouterProvider;
use PHPUnit\Framework\TestCase;

class FileUsageRouterProviderTest extends TestCase
{
    private FileUsageRouterProvider $provider;
    private string $filePath = __DIR__ . '/../../tests/files';

    protected function setUp(): void
    {
        parent::setUp();
        $this->deleteAllFiles();
        $this->provider = new FileUsageRouterProvider($this->filePath, 'usedRoutes.txt');
    }

    public function testAddRoute()
    {
        $usedRoute = UsedRoute::newVisit('route');
        $this->provider->addRoute($usedRoute);
        $routes = $this->provider->getRoutesUsage();

        $this->assertCount(1, $routes);
        $this->assertEquals(1, $routes[0]->getVisits());
        $this->assertEquals($usedRoute->getRoute(), $routes[0]->getRoute());
        $this->assertEquals($usedRoute->getTimestamp(), $routes[0]->getTimestamp());
    }

    public function testAddSameRoute()
    {
        $usedRoute1 = UsedRoute::newVisit('route');
        $this->provider->addRoute($usedRoute1);

        $usedRoute2 = UsedRoute::newVisit('route');
        $this->provider->addRoute($usedRoute2);

        $routes = $this->provider->getRoutesUsage();

        $this->assertCount(1, $routes);
        $this->assertEquals(2, $routes[0]->getVisits());
        $this->assertEquals($usedRoute1->getRoute(), $routes[0]->getRoute());
        $this->assertEquals($usedRoute2->getTimestamp(), $routes[0]->getTimestamp());
    }

    public function testAddDifferentRoute()
    {
        $usedRoute1 = UsedRoute::newVisit('route');
        $this->provider->addRoute($usedRoute1);

        $usedRoute2 = UsedRoute::newVisit('route2');
        $this->provider->addRoute($usedRoute2);

        $routes = $this->provider->getRoutesUsage();

        $this->assertCount(2, $routes);

        $this->assertEquals(1, $routes[0]->getVisits());
        $this->assertEquals($usedRoute1->getRoute(), $routes[0]->getRoute());
        $this->assertEquals($usedRoute1->getTimestamp(), $routes[0]->getTimestamp());

        $this->assertEquals(1, $routes[1]->getVisits());
        $this->assertEquals($usedRoute2->getRoute(), $routes[1]->getRoute());
        $this->assertEquals($usedRoute2->getTimestamp(), $routes[1]->getTimestamp());
    }

    public function testAddDifferentAndSameRoute()
    {
        $usedRoute1 = UsedRoute::newVisit('route');
        $this->provider->addRoute($usedRoute1);

        $usedRoute2 = UsedRoute::newVisit('route2');
        $this->provider->addRoute($usedRoute2);

        $usedRoute3 = UsedRoute::newVisit('route2');
        $this->provider->addRoute($usedRoute3);

        $routes = $this->provider->getRoutesUsage();

        $this->assertCount(2, $routes);

        $this->assertEquals(1, $routes[0]->getVisits());
        $this->assertEquals($usedRoute1->getRoute(), $routes[0]->getRoute());
        $this->assertEquals($usedRoute1->getTimestamp(), $routes[0]->getTimestamp());

        $this->assertEquals(2, $routes[1]->getVisits());
        $this->assertEquals($usedRoute2->getRoute(), $routes[1]->getRoute());
        $this->assertEquals($usedRoute3->getTimestamp(), $routes[1]->getTimestamp());
    }

    /**
     * @return void
     */
    public function deleteAllFiles(): void
    {
        $files = glob($this->filePath . '/*'); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file); // delete file
            }
        }
    }
}
