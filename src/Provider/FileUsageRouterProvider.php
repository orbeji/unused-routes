<?php

namespace Orbeji\UnusedRoutes\Provider;

use Orbeji\UnusedRoutes\Helper\FileHelper;

final class FileUsageRouterProvider implements UsageRouteProviderInterface
{
    private string $unusedRoutesFilePath;

    public function __construct(string $unusedRoutesFilePath)
    {
        $this->unusedRoutesFilePath = $unusedRoutesFilePath;
    }
    public function addRoute(string $route): void
    {
        FileHelper::writeLine($route, $this->unusedRoutesFilePath);
    }

    public function getRoutesUsage(): array
    {
        $usedRoutes = file($this->unusedRoutesFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $valueCounts = array_count_values($usedRoutes);

        $groupedArray = [];
        foreach ($valueCounts as $value => $count) {
            $groupedArray[$value] = [
                'value' => $value,
                'count' => $count,
            ];
        }
        return $groupedArray;
    }
}