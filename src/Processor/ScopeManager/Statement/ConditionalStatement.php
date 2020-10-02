<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;

class ConditionalStatement
    extends StatementAbstract
{
    public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node
    {
        if ($node instanceof Node\Stmt\If_) {
            $node->cond = ExpressionStatement::apply($scopeManager, $node->cond);

            foreach ($node->elseifs as $elseIf) {
                $elseIf->cond = ExpressionStatement::apply($scopeManager, $elseIf->cond);

                self::applyStatements($scopeManager, $elseIf->stmts, ExpressionStatement::class);
            }

            if ($node->else) {
                self::applyStatements($scopeManager, $node->else->stmts, ExpressionStatement::class);
            }

            self::applyStatements($scopeManager, $node->stmts, ExpressionStatement::class);
        }
        else if ($node instanceof Node\Expr\Ternary) {
            $node->cond = ExpressionStatement::apply($scopeManager, $node->cond);
            $node->if   = ExpressionStatement::apply($scopeManager, $node->if);
            $node->else = ExpressionStatement::apply($scopeManager, $node->else);
        }

        return null;
    }
}
