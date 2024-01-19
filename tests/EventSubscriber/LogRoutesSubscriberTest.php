<?php

namespace Orbeji\UnusedRoutes\Tests\EventSubscriber;

use Orbeji\UnusedRoutes\EventSubscriber\LogRoutesSubscriber;
use Orbeji\UnusedRoutes\Provider\FileUsageRouterProvider;
use Orbeji\UnusedRoutes\Provider\UsageRouteProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class LogRoutesSubscriberTest extends TestCase
{
    private FileUsageRouterProvider $provider;
    private string $filePath = __DIR__ . '/../../tests/files';

    protected function setUp(): void
    {
        parent::setUp();
        $this->deleteAllFiles();
        $this->provider = new FileUsageRouterProvider($this->filePath, 'usedRoutes.txt');
    }

    public function testOnControllerNooute()
    {
        $subscriber = new LogRoutesSubscriber($this->provider);
        $request = $this->createStub(Request::class);
        $route = null;
        $request->method('get')->willReturn($route);
        $controllerEvent = new ControllerEvent(
            $this->createStub(HttpKernelInterface::class),
            function(){},
            $request,
            1
        );
        $subscriber->onController($controllerEvent);

        $routes = $this->provider->getRoutesUsage();

        $this->assertCount(0, $routes);
    }

    public function testOnControllerValidRoute()
    {
        $subscriber = new LogRoutesSubscriber($this->provider);
        $request = $this->createStub(Request::class);
        $route = 'route';
        $request->method('get')->willReturn($route);
        $controllerEvent = new ControllerEvent(
            $this->createStub(HttpKernelInterface::class),
            function(){},
            $request,
            1
        );
        $subscriber->onController($controllerEvent);

        $routes = $this->provider->getRoutesUsage();

        $this->assertCount(1, $routes);
        $this->assertEquals(1, $routes[0]->getVisits());
        $this->assertEquals($route, $routes[0]->getRoute());
    }

    public function testOnControllerPrivateRoute()
    {
        $subscriber = new LogRoutesSubscriber($this->provider);
        $request = $this->createStub(Request::class);
        $route = '_route';
        $request->method('get')->willReturn($route);
        $controllerEvent = new ControllerEvent(
            $this->createStub(HttpKernelInterface::class),
            function(){},
            $request,
            1
        );
        $subscriber->onController($controllerEvent);

        $routes = $this->provider->getRoutesUsage();

        $this->assertCount(0, $routes);
    }
    public function testOnControllerNoRoute()
    {
        $subscriber = new LogRoutesSubscriber($this->provider);
        $request = $this->createStub(Request::class);
        $route = '';
        $request->method('get')->willReturn($route);
        $controllerEvent = new ControllerEvent(
            $this->createStub(HttpKernelInterface::class),
            function(){},
            $request,
            1
        );
        $subscriber->onController($controllerEvent);

        $routes = $this->provider->getRoutesUsage();

        $this->assertCount(0, $routes);
    }

    public function testGetSubscribedEvents()
    {
        $events = LogRoutesSubscriber::getSubscribedEvents();
        $this->assertEquals(['kernel.controller' => 'onController'], $events);
    }

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
