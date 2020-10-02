<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;

class ClassStatement
    extends StatementAbstract
{
    public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node
    {
        if ($node instanceof Node\Stmt\Class_) {
            self::applyStatements($scopeManager, $node->stmts, ExpressionStatement::class);
        }
        else if ($node instanceof Node\Expr\New_) {
            $node->class = ExpressionStatement::apply($scopeManager, $node->class);

            foreach ($node->args as $arg) {
                $arg->value = ExpressionStatement::apply($scopeManager, $arg->value);
            }
        }
        else if ($node instanceof Node\Stmt\ClassConst ||
                 $node instanceof Node\Stmt\Const_) {
            foreach ($node->consts as $const) {
                $const->value = ExpressionStatement::apply($scopeManager, $const->value);
            }
        }

        return null;
    }
}
