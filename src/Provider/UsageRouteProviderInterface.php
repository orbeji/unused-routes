<?php

declare(strict_types=1);

namespace Orbeji\UnusedRoutes\Provider;

use Orbeji\UnusedRoutes\Entity\UsedRoute;

interface UsageRouteProviderInterface
{
    /**
     * Everytime a user accesses a route this method is called to store this usage
     */
    public function addRoute(UsedRoute $route): void;

    /**
     * This method aggregates all UsedRoutes by the used route and sums all visits, leaving the timestamp of the last
     * visit
     * @return UsedRoute[]
     */
    public function getRoutesUsage(): array;
}
