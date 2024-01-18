<?php

declare(strict_types=1);

namespace Orbeji\UnusedRoutes\Provider;

use Orbeji\UnusedRoutes\Entity\UsedRoute;
use Orbeji\UnusedRoutes\Helper\FileHelper;

final class FileUsageRouterProvider implements UsageRouteProviderInterface
{
    private string $unusedRoutesFilePath;
    private string $unusedRoutesFileName;

    public function __construct(string $unusedRoutesFilePath, string $unusedRoutesFileName)
    {
        $this->unusedRoutesFilePath = $unusedRoutesFilePath;
        $this->unusedRoutesFileName = $unusedRoutesFileName;
    }

    public function addRoute(UsedRoute $route): void
    {
        FileHelper::writeLine($this->transformToLine($route), $this->getFilePath());
    }

    /**
     * @param UsedRoute $route
     * @return string
     */
    private function transformToLine(UsedRoute $route): string
    {
        return $route->getRoute() . ';' . $route->getTimestamp();
    }

    /**
     * @return string
     */
    private function getFilePath(): string
    {
        $unusedRoutesFileName = str_replace('.', date('Ymd') . '.', $this->unusedRoutesFileName);
        return $this->unusedRoutesFilePath . DIRECTORY_SEPARATOR . $unusedRoutesFileName;
    }

    /**
     * @return UsedRoute[]
     */
    public function getRoutesUsage(): array
    {
        $files = $this->getFiles();

        $usedRoutes = [];
        foreach ($files as $file) {
            $fileContent = FileHelper::readContents($file);
            foreach ($fileContent as $line) {
                // Assuming the line contains route and timestamp separated by a delimiter
                $parts = explode(';', $line);
                if (count($parts) === 2) {
                    $route = trim($parts[0]);
                    $timestamp = (int)trim($parts[1]);

                    // Store route as key and update timestamp if it exists or add a new entry
                    if (array_key_exists($route, $usedRoutes)) {
                        $usedRoutes[$route]['timestamp'] = $timestamp;
                    } else {
                        $usedRoutes[$route] = [
                            'value' => $route,
                            'timestamp' => $timestamp,
                            'count' => 0, // Initialize count to 0
                        ];
                    }

                    // Increment count for the route
                    $usedRoutes[$route]['count']++;
                }
            }
        }
        // Convert $usedRoutes to the final grouped array format
        $groupedArray = [];
        foreach ($usedRoutes as $data) {
            $groupedArray[] = UsedRoute::fromGroupedData($data['value'], $data['timestamp'], $data['count']);
        }

        return $groupedArray;
    }

    /**
     * @return array<int, string>
     */
    private function getFiles(): array
    {
        $unusedRoutesFileName = str_replace('.', '*.', $this->unusedRoutesFileName);
        $files = FileHelper::readGlob($this->unusedRoutesFilePath, $unusedRoutesFileName);

        $matchPattern = str_replace('.', '\d{4}\d{2}\d{2}\.', $this->unusedRoutesFileName);
        $matchPattern = '/^' . $matchPattern . '$/';
        $matchedFiles = [];
        foreach ($files as $file) {
            $filename = basename($file);
            if (preg_match($matchPattern, $filename)) {
                $matchedFiles[] = $this->unusedRoutesFilePath . DIRECTORY_SEPARATOR . $filename;
            }
        }
        return $matchedFiles;
    }
}
