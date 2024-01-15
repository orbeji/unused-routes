<?php

namespace Orbeji\UnusedRoutes\Tests;

use Orbeji\UnusedRoutes\Command\RouteUsageCommand;
use Orbeji\UnusedRoutes\EventSubscriber\LogRoutesSubscriber;
use Orbeji\UnusedRoutes\Helper\RouteUsageHelper;
use Orbeji\UnusedRoutes\Provider\FileUsageRouterProvider;
use Orbeji\UnusedRoutes\Provider\UsageRouteProviderInterface;
use Orbeji\UnusedRoutes\UnusedRoutesBundle;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Nyholm\BundleTest\TestKernel;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class BundleInitializationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(UnusedRoutesBundle::class);
        $kernel->handleOptions($options);

        return $kernel;
    }

    public function testInitBundle(): void
    {
        $kernel = self::bootKernel();
        $bundle = $kernel->getBundle('UnusedRoutesBundle');
        self::assertInstanceOf(BundleInterface::class, $bundle);

         $container = self::getContainer();

        $this->assertTrue($container->has('orbeji_unusedroutes.route_usage_command'));
        $service = $container->get('orbeji_unusedroutes.route_usage_command');
        $this->assertInstanceOf(RouteUsageCommand::class, $service);

        $this->assertTrue($container->has('orbeji_unusedroutes.usage_route_provider'));
        $service = $container->get('orbeji_unusedroutes.usage_route_provider');
        $this->assertInstanceOf(FileUsageRouterProvider::class, $service);

        $this->assertTrue($container->has('orbeji_unusedroutes.log_routes_subscriber'));
        $service = $container->get('orbeji_unusedroutes.log_routes_subscriber');
        $this->assertInstanceOf(LogRoutesSubscriber::class, $service);

        $this->assertTrue($container->has('orbeji_unusedroutes.route_usage_helper'));
        $service = $container->get('orbeji_unusedroutes.route_usage_helper');
        $this->assertInstanceOf(RouteUsageHelper::class, $service);
    }
}
