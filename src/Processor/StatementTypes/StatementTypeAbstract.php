<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\StatementTypes;

abstract class StatementTypeAbstract
{
    abstract public static function getCallInitialData(): array;

    public static function getCallKeyHasher(array $arguments): ?array
    {
        return null;
    }

    abstract public static function updateCallData(array &$callData, array $arguments): void;
}
