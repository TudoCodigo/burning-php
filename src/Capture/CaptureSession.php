<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Capture;

use TudoCodigo\BurningPHP\Configuration;
use TudoCodigo\BurningPHP\Support\Directory;
use TudoCodigo\BurningPHP\Support\Traits\SingletonPatternTrait;

class CaptureSession
{
    use SingletonPatternTrait;

    /** @var CaptureSessionFile[] */
    private array $files = [];

    public string $path;

    private function writeFilesStructure(): void
    {
        $configuration = Configuration::getInstance();

        file_put_contents($this->getSessionDirectory('/HEADER'), json_encode([
            'version'        => $configuration->getBurningVersionInt(),
            'sourceHash'     => $configuration->getBurningSourceHash(),
            'attributesHash' => $configuration->getAttributesHash(),
            'references'     => array_combine(array_map(static function (CaptureSessionFile $sessionFile) {
                return $sessionFile->getShortPath();
            }, $this->files), array_column($this->files, 'hash'))
        ], JSON_THROW_ON_ERROR));
    }

    public function __destruct()
    {
        $this->writeFilesStructure();
    }

    public function getFile(string $path): CaptureSessionFile
    {
        if (array_key_exists($path, $this->files)) {
            return $this->files[$path];
        }

        return $this->files[$path] = new CaptureSessionFile($this, $path);
    }

    public function getSessionDirectory(?string $additionalPath = null): string
    {
        return Configuration::getInstance()->getControlDirectory() . '/' . $this->path . $additionalPath;
    }

    public function initialize(): void
    {
        $configuration = Configuration::getInstance();

        $this->path = sprintf($configuration->sessionDirectoryFormat, str_pad(var_export($_SERVER['REQUEST_TIME_FLOAT'], true), 17, '0'));

        $sessionDirectory = $this->getSessionDirectory();

        Directory::rebuildDirectory($sessionDirectory);

        if ($configuration->sessionLastDirectoryEnabled) {
            Directory::createDirectoryLink($sessionDirectory, $configuration->getControlDirectory('/session-last'), true);
        }
    }
}
