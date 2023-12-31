<?php

namespace Orbeji\UnusedRoutes\EventSubscriber;

use Orbeji\UnusedRoutes\Helper\FileHelper;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class LogRoutesSubscriber implements EventSubscriberInterface
{
   private string $dir;

   public function __construct(ParameterBagInterface $parameterBag)
   {
        $this->dir = $parameterBag->get('unused_routes.file_path');
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
        $route = $request->get('_route');
        if ($route === null) {
            return;
        }
        $this->storeUsedAction($route);
    }

    private function storeUsedAction(string $route): void
    {
        FileHelper::writeLine($this->dir, $route);
    }
}