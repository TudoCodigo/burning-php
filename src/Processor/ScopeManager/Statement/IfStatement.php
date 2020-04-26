<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;

class IfStatement
    extends StatementAbstract
{
    public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node
    {
        if ($node instanceof Node\Stmt\If_) {
            $node->cond = ExpressionStatement::apply($scopeManager, $node->cond);

            foreach ($node->elseifs as $elseIf) {
                $elseIf->cond = ExpressionStatement::apply($scopeManager, $elseIf->cond);

                foreach ($elseIf->stmts as $stmt) {
                    ExpressionStatement::apply($scopeManager, $stmt);
                }
            }

            if ($node->else) {
                foreach ($node->else->stmts as $stmt) {
                    ExpressionStatement::apply($scopeManager, $stmt);
                }
            }

            foreach ($node->stmts as $stmt) {
                ExpressionStatement::apply($scopeManager, $stmt);
            }
        }

        return null;
    }
}
