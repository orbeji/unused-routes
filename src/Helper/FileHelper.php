<?php

namespace Orbeji\UnusedRoutes\Helper;

use Webmozart\Assert\Assert;

use function PHPUnit\Framework\assertIsArray;

final class FileHelper
{
    public static function writeLine(string $line, string $filePath): void
    {
        file_put_contents($filePath, $line . PHP_EOL, FILE_APPEND);
    }

    /**
     * @param string $file
     * @return array<int, string>
     */
    public static function readContents(string $file): array
    {
        $contents = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        Assert::isArray($contents);
        return $contents;
    }

    /**
     * @param string $path
     * @param string $unusedRoutesFileName
     * @return array<int, string>
     */
    public static function readGlob(string $path, string $unusedRoutesFileName): array
    {
        $glob = glob($path . DIRECTORY_SEPARATOR . $unusedRoutesFileName);
        Assert::isArray($glob);
        return $glob;
    }
}
