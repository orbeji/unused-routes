<?php

namespace Orbeji\UnusedRoutes\Helper;

use Orbeji\UnusedRoutes\Entity\UsedRoute;
use Orbeji\UnusedRoutes\Provider\UsageRouteProviderInterface;
use Symfony\Component\Routing\RouterInterface;

class RouteUsageHelper
{
    private RouterInterface $router;
    private UsageRouteProviderInterface $usageRouteProvider;

    public function __construct(UsageRouteProviderInterface $usageRouteProvider, RouterInterface $router)
    {
        $this->usageRouteProvider = $usageRouteProvider;
        $this->router = $router;
    }

    /**
     * @param bool $showAllRoutes
     * @return array<int, array{value: string, count: int, date: string}>
     */
    public function getRoutesUsage(bool $showAllRoutes): array
    {
        $usedRoutes = $this->usageRouteProvider->getRoutesUsage();
        $allRoutes = $this->getAllRouteNames();
        return $this->getRoutesUsageResult($usedRoutes, $allRoutes, $showAllRoutes);
    }

    /**
     * @return array<int, string>
     */
    private function getAllRouteNames(): array
    {
        $routeCollection = $this->router->getRouteCollection();
        $routeNames = [];
        foreach ($routeCollection->all() as $routeName => $route) {
            if (!str_starts_with($routeName, '_')) {
                $routeNames[] = $routeName;
            }
        }
        return $routeNames;
    }

    /**
     * @param UsedRoute[] $usedRoutes
     * @param array<int, string> $allRoutes
     * @param bool $showAll
     * @return array<int, array{value: string, count: int, date: string}>
     */
    private function getRoutesUsageResult(
        array $usedRoutes,
        array $allRoutes,
        bool $showAll
    ): array {
        $unusedRoutes = array();
        foreach ($allRoutes as $route) {
            $existRouteInArray = $this->existRouteInArray($route, $usedRoutes);
            if ($showAll && $existRouteInArray instanceof UsedRoute) {
                $unusedRoutes[] = [
                    'value' => $existRouteInArray->getRoute(),
                    'count' => $existRouteInArray->getVisits(),
                    'date' => date('d/m/Y', $existRouteInArray->getTimestamp()),
                ];
            } elseif (!$existRouteInArray) {
                $unusedRoutes[] = ['value' => $route, 'count' => 0, 'date' => '-'];
            }
        }

        return $unusedRoutes;
    }

    /**
     * @param string $route
     * @param UsedRoute[] $usedRoutes
     * @return UsedRoute|bool
     */
    private function existRouteInArray(string $route, array $usedRoutes): UsedRoute|bool
    {
        foreach ($usedRoutes as $usedRoute) {
            if ($usedRoute->getRoute() === $route) {
                return $usedRoute;
            }
        }
        return false;
    }
}
