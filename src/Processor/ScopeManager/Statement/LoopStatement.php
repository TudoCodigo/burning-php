<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;

class LoopStatement
    extends StatementAbstract
{
    public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node
    {
        if ($node instanceof Node\Stmt\For_) {
            $node->init = self::applyStatements($scopeManager, $node->init, ExpressionStatement::class);
            $node->cond = self::applyStatements($scopeManager, $node->cond, ExpressionStatement::class);
            $node->loop = self::applyStatements($scopeManager, $node->loop, ExpressionStatement::class);
        }
        else if ($node instanceof Node\Stmt\Foreach_) {
            $node->expr = ExpressionStatement::apply($scopeManager, $node->expr);
        }
        else if ($node instanceof Node\Stmt\While_ ||
                 $node instanceof Node\Stmt\Do_) {
            $node->cond = ExpressionStatement::apply($scopeManager, $node->cond);
        }

        if ($node instanceof Node\Stmt\For_ ||
            $node instanceof Node\Stmt\Foreach_ ||
            $node instanceof Node\Stmt\While_ ||
            $node instanceof Node\Stmt\Do_) {
            self::applyStatements($scopeManager, $node->stmts, ExpressionStatement::class);
        }

        return null;
    }
}
