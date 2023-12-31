<?php

namespace Orbeji\UnusedRoutes\Helper;

final class FileHelper
{
    public static function writeLine(string $line, string $filePath): void
    {
        if (file_exists($filePath)) {
            file_put_contents($filePath, $line . PHP_EOL, FILE_APPEND);
        }
    }
}
