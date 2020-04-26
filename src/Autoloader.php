<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP;

use Composer\Autoload\ClassLoader;
use Symfony\Polyfill\Php80\Php80;
use TudoCodigo\BurningPHP\Capture\Capture;
use TudoCodigo\BurningPHP\Processor\Processor;
use TudoCodigo\BurningPHP\Support\Directory;
use TudoCodigo\BurningPHP\Support\Traits\SingletonPatternTrait;

use function Composer\Autoload\includeFile;

class Autoloader
{
    use SingletonPatternTrait;

    /** @var string[] */
    private array $ignorablePrefixes;

    public ClassLoader $composerClassLoader;

    public function __construct()
    {
        $this->ignorablePrefixes = array_merge(
            [ realpath(getcwd() . DIRECTORY_SEPARATOR . 'vendor') . DIRECTORY_SEPARATOR ],
            Configuration::getInstance()->getTargetDevelopmentPaths()
        );
    }

    public static function prepareControlDirectory(): void
    {
        Directory::createDirectory(Configuration::getInstance()->getControlDirectory('/cache'));
    }

    public function autoload(string $classname): void
    {
        if (str_starts_with($classname, 'TudoCodigo\\BurningPHP\\')) {
            return;
        }

        $file = $this->composerClassLoader->findFile($classname);

        if (!$file || !is_readable($file)) {
            return;
        }

        $file = realpath($file);

        foreach ($this->ignorablePrefixes as $ignorablePrefix) {
            if (str_starts_with($file, $ignorablePrefix)) {
                return;
            }
        }

        includeFile(Processor::getInstance()->process($file)->getExecutableSourcePath());
    }

    public function register(\Closure $composerAutoloadRegister): void
    {
        self::prepareControlDirectory();

        Capture::register();

        $this->composerClassLoader = $composerAutoloadRegister();
        $this->composerClassLoader->loadClass(Php80::class);

        spl_autoload_register([ $this, 'autoload' ], true, true);
    }
}
