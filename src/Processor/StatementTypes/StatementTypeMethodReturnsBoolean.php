<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\StatementTypes;

class StatementTypeMethodReturnsBoolean
    extends StatementTypeAbstract
{
    private static function increaseReturnType(array &$callData, string $key): void
    {
        if (!array_key_exists($key, $callData['returns'])) {
            $callData['returns'][$key] = 1;

            return;
        }

        $callData['returns'][$key]++;
    }

    public static function getCallInitialData(array $arguments): array
    {
        return [ 'method' => implode('::', $arguments[0]), 'returns' => [] ];
    }

    public static function updateCallData(array &$callData, array $arguments): void
    {
        if (is_bool($arguments[1])) {
            self::increaseReturnType($callData, $arguments[1] ? 'true' : 'false');
        }
        else if (is_object($arguments[1])) {
            self::increaseReturnType($callData, get_class($arguments[1]));
        }
        else {
            self::increaseReturnType($callData, gettype($arguments[1]));
        }
    }
}
