<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\StatementTypes;

class StatementTypeFunctionReturnsBoolean
    extends StatementTypeAbstract
{
    public static function getCallInitialData(array $arguments): array
    {
        return [ 'true' => null, 'false' => null ];
    }

    public static function updateCallData(array &$callData, array $arguments): void
    {
        $callData[$arguments[1] ? 'true' : 'false']++;
    }

    public static function validate(array $arguments): bool
    {
        return $arguments[0];
    }
}
