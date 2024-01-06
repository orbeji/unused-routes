<?php

namespace Orbeji\UnusedRoutes\Provider;

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

    public function addRoute(string $route): void
    {
        FileHelper::writeLine($route, $this->getFilePath());
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        $unusedRoutesFileName = str_replace('.', date('Ymd') . '.', $this->unusedRoutesFileName);
        return $this->unusedRoutesFilePath . DIRECTORY_SEPARATOR . $unusedRoutesFileName;
    }

    public function getRoutesUsage(): array
    {
        $files = $this->getFiles();

        $usedRoutes = [];
        foreach ($files as $file) {
            $usedRoutes[] = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        }

        $usedRoutes = array_merge(...$usedRoutes);
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

    private function getFiles(): array
    {
        $unusedRoutesFileName = str_replace('.', '*.', $this->unusedRoutesFileName);
        $files = glob($this->unusedRoutesFilePath . DIRECTORY_SEPARATOR . $unusedRoutesFileName);

        $matchPattern = str_replace('.', '\d{4}\d{2}\d{2}\.', $this->unusedRoutesFileName);
        $matchPattern = '/^'.$matchPattern.'$/';
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
