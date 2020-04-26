<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Composer;

use Composer\XdebugHandler\XdebugHandler;
use TudoCodigo\BurningPHP\Autoloader;
use TudoCodigo\BurningPHP\Configuration;

if (class_exists(Autoloader::class, false)) {
    return;
}

class Autoload
{
    public static function init(): void
    {
        require '$composerAutoloadReal';

        if (Configuration::getInstance()->disableXdebug) {
            $xdebugHandler = new XdebugHandler('BURNING');
            $xdebugHandler->check();
        }

        $instanceBurningAutoloader = Autoloader::getInstance();
        $instanceBurningAutoloader->register(static function () {
            /** @var object $composerAutoloadClassname */
            return $composerAutoloadClassname::getLoader();
        });
    }
}

Autoload::init();
