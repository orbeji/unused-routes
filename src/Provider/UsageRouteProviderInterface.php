<?php

namespace Orbeji\UnusedRoutes\Provider;

use Orbeji\UnusedRoutes\Entity\UsedRoute;

interface UsageRouteProviderInterface
{
    public function addRoute(UsedRoute $route): void;

    public function getRoutesUsage(): array;
}