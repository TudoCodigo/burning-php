<?php

declare(strict_types = 1);

use TudoCodigo\BurningPHP\Capture\CaptureSession;

function burning_capture_return(string $filepath, int $statementId, bool $isReallyGlobal, $callReturn)
{
    if (!$isReallyGlobal) {
        return $callReturn;
    }

    CaptureSession::getInstance()->getFile($filepath)->notify($statementId, [ $callReturn ]);

    return $callReturn;
}
