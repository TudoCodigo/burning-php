<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Capture\Capture;
use TudoCodigo\BurningPHP\Configuration;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;
use TudoCodigo\BurningPHP\Processor\StatementTypes\StatementTypeFunctionReturnsBoolean;
use TudoCodigo\BurningPHP\Processor\StatementTypes\StatementTypeMethodReturnsBoolean;

class FunctionStatement
    extends StatementAbstract
{
    public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node
    {
        if ($node instanceof Node\Expr\FuncCall) {
            if (self::isApplicable($node)) {
                foreach ($node->args as $arg) {
                    $arg->value = ExpressionStatement::apply($scopeManager, $arg->value);
                }

                if ($node->name instanceof Node\Name) {
                    $functionName = $node->name->toString();

                    $configuration = Configuration::getInstance();

                    if (in_array($functionName, $configuration->returnsBooleanFunctions, true)) {
                        $statementIndex = $scopeManager->processorFile->writeStatement(StatementTypeFunctionReturnsBoolean::class, $node, [ $functionName ]);

                        return Capture::createBurningCaptureFunctionReturnCall($scopeManager, $statementIndex, $node);
                    }
                }
            }
        }
        else if ($node instanceof Node\Expr\MethodCall ||
                 $node instanceof Node\Expr\NullsafeMethodCall) {
            $node->var  = ExpressionStatement::apply($scopeManager, $node->var);
            $node->name = ExpressionStatement::apply($scopeManager, $node->name);

            foreach ($node->args as $arg) {
                $arg->value = ExpressionStatement::apply($scopeManager, $arg->value);
            }
        }
        else if ($node instanceof Node\Expr\ArrowFunction) {
            $node->expr = ExpressionStatement::apply($scopeManager, $node->expr);
        }
        else if ($node instanceof Node\Expr\StaticCall) {
            if (self::isApplicable($node)) {
                $node->class = ExpressionStatement::apply($scopeManager, $node->class);
                $node->name  = ExpressionStatement::apply($scopeManager, $node->name);

                foreach ($node->args as $arg) {
                    $arg->value = ExpressionStatement::apply($scopeManager, $arg->value);
                }

                if ($node->name instanceof Node\Identifier &&
                    $node->name->toString() !== '__construct') {
                    if (!$node->class instanceof Node\Name ||
                        $node->class->toString() !== 'parent') {
                        $statementIndex = $scopeManager->processorFile->writeStatement(StatementTypeMethodReturnsBoolean::class, $node);

                        return Capture::createBurningCaptureUserlandMethodReturnCall($scopeManager, $statementIndex, $node);
                    }
                }
            }
        }
        else if ($node instanceof Node\Expr\PropertyFetch ||
                 $node instanceof Node\Expr\NullsafePropertyFetch) {
            $node->var  = ExpressionStatement::apply($scopeManager, $node->var);
            $node->name = ExpressionStatement::apply($scopeManager, $node->name);
        }
        else if ($node instanceof Node\Stmt\Declare_) {
            foreach ($node->declares as $declare) {
                $declare->value = ExpressionStatement::apply($scopeManager, $declare->value);
            }

            self::applyStatements($scopeManager, $node->stmts, ExpressionStatement::class);
        }

        return null;
    }
}
