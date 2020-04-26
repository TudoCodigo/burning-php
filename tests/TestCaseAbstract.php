<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Tests;

use PHPUnit\Framework\TestCase;
use TudoCodigo\BurningPHP\Autoloader;
use TudoCodigo\BurningPHP\Configuration;
use TudoCodigo\BurningPHP\Support\Directory;

abstract class TestCaseAbstract
    extends TestCase
{
    public static function prepareBurningConfigurationForTesting(): void
    {
        $configuration                              = Configuration::getInstance(true);
        $configuration->sessionLastDirectoryEnabled = false;
        $configuration->sessionDirectoryFormat      = 'session-testing';

        Autoloader::prepareControlDirectory();

        Directory::rebuildDirectory(getcwd() . '/.burning/cache');
    }
}
