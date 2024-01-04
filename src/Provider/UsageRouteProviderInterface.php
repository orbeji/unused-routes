<?php

namespace Orbeji\UnusedRoutes\Provider;

interface UsageRouteProviderInterface
{
    public function addRoute(string $route): void;

    public function getRoutesUsage(): array;
}