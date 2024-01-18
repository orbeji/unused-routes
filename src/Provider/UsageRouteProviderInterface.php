<?php

declare(strict_types=1);

namespace Orbeji\UnusedRoutes\Provider;

use Orbeji\UnusedRoutes\Entity\UsedRoute;

interface UsageRouteProviderInterface
{
    public function addRoute(UsedRoute $route): void;

    /**
     * @return UsedRoute[]
     */
    public function getRoutesUsage(): array;
}
