<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Capture\Capture;
use TudoCodigo\BurningPHP\Configuration;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;
use TudoCodigo\BurningPHP\Processor\StatementTypes\StatementTypeReturnsBoolean;

class FunctionStatement
    extends StatementAbstract
{
    public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node
    {
        if ($node instanceof Node\Expr\FuncCall) {
            foreach ($node->args as $arg) {
                $arg->value = ExpressionStatement::apply($scopeManager, $arg->value);
            }

            if ($node->name instanceof Node\Name) {
                $functionName = $node->name->toString();

                $configuration = Configuration::getInstance();

                if (in_array($functionName, $configuration->returnsBooleanFunctions, true)) {
                    $statementIndex = $scopeManager->processorFile->writeStatement(StatementTypeReturnsBoolean::class, $node, [ $functionName ]);

                    return Capture::createBurningCaptureReturnCall($scopeManager, $statementIndex, $node);
                }
            }
        }
        else if ($node instanceof Node\Expr\MethodCall) {
            foreach ($node->args as $arg) {
                $arg->value = ExpressionStatement::apply($scopeManager, $arg->value);
            }
        }
        else if ($node instanceof Node\Expr\ArrowFunction) {
            $node->expr = ExpressionStatement::apply($scopeManager, $node->expr);
        }

        return null;
    }
}
