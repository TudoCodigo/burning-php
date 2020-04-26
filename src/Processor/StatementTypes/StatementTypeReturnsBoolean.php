<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\StatementTypes;

class StatementTypeReturnsBoolean
    extends StatementTypeAbstract
{
    public static function getCallInitialData(): array
    {
        return [ 'true' => null, 'false' => null ];
    }

    public static function updateCallData(array &$callData, array $arguments): void
    {
        $callData[$arguments[0] ? 'true' : 'false']++;
    }
}
