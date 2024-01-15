<?php

namespace Orbeji\UnusedRoutes\Tests\Command;

use Nyholm\BundleTest\TestKernel;
use Orbeji\UnusedRoutes\Helper\RouteUsageHelper;
use Orbeji\UnusedRoutes\UnusedRoutesBundle;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

class RouteUsageCommandTest extends KernelTestCase
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

    public function testExecuteNoRoutes()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('unused-routes:list');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $expected = ' ------- ------- ------------- 
  Route   #Uses   Last access  
 ------- ------- ------------- 

';
        $this->assertEquals($expected, $output);
    }

    public function testExecuteRoutes()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $container = self::getContainer();
        $routeUsageHelper = $this->createStub(RouteUsageHelper::class);
        $routeUsageHelper->method('getRoutesUsage')->willReturn(
            [
                ['value' => 'route', 'count' => 1, 'date' => 'date'],
                ['value' => 'route2', 'count' => 0, 'date' => 'date']
            ]
        );
        $container->set('orbeji_unusedroutes.route_usage_helper', $routeUsageHelper);

        $command = $application->find('unused-routes:list');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $expected = ' -------- ------- ------------- 
  Route    #Uses   Last access  
 -------- ------- ------------- 
  route    1       date         
  route2   0       date         
 -------- ------- ------------- 

';
        $this->assertEquals($expected, $output);
    }
}
