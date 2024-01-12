<?php

namespace Orbeji\UnusedRoutes\EventSubscriber;

use Orbeji\UnusedRoutes\Entity\UsedRoute;
use Orbeji\UnusedRoutes\Provider\UsageRouteProviderInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Webmozart\Assert\Assert;

use function PHPUnit\Framework\assertIsString;

final class LogRoutesSubscriber implements EventSubscriberInterface
{
    private UsageRouteProviderInterface $usageRouteProvider;

    public function __construct(UsageRouteProviderInterface $usageRouteProvider)
    {
        $this->usageRouteProvider = $usageRouteProvider;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onController',
        ];
    }

    public function onController(ControllerEvent $controllerEvent): void
    {
        $request = $controllerEvent->getRequest();
        $route = $request->get('_route', '');
        Assert::string($route);
        if ($route === '' || str_starts_with($route, '_')) {
            return;
        }
        $this->usageRouteProvider->addRoute(UsedRoute::newVisit($route));
    }
}