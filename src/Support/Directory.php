<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Support;

use Symfony\Component\Filesystem\Filesystem;

class Directory
{
    private static function getFilesystem(): Filesystem
    {
        return Deterministic::withClosure(static function () {
            return new Filesystem;
        });
    }

    public static function createDirectory(string $directory): void
    {
        $filesystem = self::getFilesystem();

        if (!$filesystem->exists($directory)) {
            $filesystem->mkdir($directory);
        }
    }

    public static function createDirectoryLink(string $directoryReal, string $directoryLink, ?bool $overwrite = null): bool
    {
        if ($overwrite === true && is_dir($directoryLink)) {
            rmdir($directoryLink);
        }

        if (PHP_OS_FAMILY === 'Windows') {
            exec(sprintf('mklink /J %s %s', escapeshellarg($directoryLink), escapeshellarg($directoryReal)));

            return is_dir($directoryLink);
        }

        return symlink($directoryReal, $directoryLink);
    }

    public static function rebuildDirectory(string $directory): void
    {
        $filesystem = self::getFilesystem();
        $filesystem->remove($directory);
        $filesystem->mkdir($directory);
    }

    public static function temporaryFilename(): string
    {
        return self::getFilesystem()->tempnam(sys_get_temp_dir(), 'tmp');
    }
}
