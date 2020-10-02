<?php

declare(strict_types = 1);

use TudoCodigo\BurningPHP\Capture\CaptureSession;

function burning_capture_function_return(string $filepath, int $statementId, bool $isReallyGlobal, $callReturn)
{
    CaptureSession::getInstance()->getFile($filepath)->notify($statementId, [ $isReallyGlobal, $callReturn ]);

    return $callReturn;
}

function burning_capture_method_return(string $filepath, int $statementId, array $methodCallable, array $arguments)
{
    try {
        $reflectionMethod = new ReflectionMethod($methodCallable[0], $methodCallable[1]);
        $reflectionMethod->setAccessible(true);

        $callReturn = $reflectionMethod->invokeArgs(null, $arguments);
    }
    catch (ReflectionException $exception) {
        $callReturn = call_user_func_array($methodCallable, $arguments);
    }

    CaptureSession::getInstance()->getFile($filepath)->notify($statementId, [ $methodCallable, $callReturn ]);

    return $callReturn;
}
