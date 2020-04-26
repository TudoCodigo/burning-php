<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Capture;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;
use TudoCodigo\BurningPHP\Support\Deterministic;

class Capture
{
    public static function createBurningCaptureReturnCall(ScopeManager $scopeManager, int $statementIndex, Node\Expr\FuncCall $originalCall): Node\Expr\FuncCall
    {
        $builderFactory = self::getBuilderFactory();

        $isReallyGlobalArgument = null;

        if (!$originalCall->name->isFullyQualified()) {
            /** @var Node\Name\FullyQualified $namespacedName */
            $namespacedName = $originalCall->name->getAttribute('namespacedName');

            $isReallyGlobalArgument = new Node\Expr\BooleanNot($builderFactory->funcCall('function_exists', [
                $namespacedName->toString()
            ]));
        }

        return $builderFactory->funcCall('\\burning_capture_return', [
            $builderFactory->val($scopeManager->processorFile->sourceOriginalPath),
            $builderFactory->val($statementIndex),
            $isReallyGlobalArgument ?? $builderFactory->val(true),
            $originalCall
        ]);
    }

    public static function getBuilderFactory(): BuilderFactory
    {
        return Deterministic::withClosure(static function () {
            return new BuilderFactory;
        });
    }

    public static function register(): void
    {
        require_once __DIR__ . '/CaptureFunctions.php';
    }
}
