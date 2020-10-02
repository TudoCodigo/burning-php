<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Capture;

use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;
use TudoCodigo\BurningPHP\Support\Deterministic;

class Capture
{
    public static function createBurningCaptureFunctionReturnCall(ScopeManager $scopeManager, int $statementIndex, FuncCall $originalCall): FuncCall
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

        return $builderFactory->funcCall('\\burning_capture_function_return', [
            $builderFactory->val($scopeManager->processorFile->sourceOriginalPath),
            $builderFactory->val($statementIndex),
            $isReallyGlobalArgument ?? $builderFactory->val(true),
            $originalCall
        ]);
    }

    public static function createBurningCaptureUserlandMethodReturnCall(ScopeManager $scopeManager, int $statementIndex, StaticCall $originalCall): FuncCall
    {
        $builderFactory = self::getBuilderFactory();

        return $builderFactory->funcCall('\\burning_capture_method_return', [
            $builderFactory->val($scopeManager->processorFile->sourceOriginalPath),
            $builderFactory->val($statementIndex),
            $builderFactory->val([
                $originalCall->class instanceof Node\Name
                    ? $builderFactory->classConstFetch($originalCall->class, 'class')
                    : $originalCall->class,
                $builderFactory->val($originalCall->name->toString())
            ]),
            array_map(static function (Node\Arg $arg) {
                return new Node\Expr\ArrayItem(
                    $arg->value,
                    null,
                    !$arg->unpack && $arg->value instanceof Node\Expr\Variable,
                    [],
                    $arg->unpack
                );
            }, $originalCall->args)
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
