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
        if ($node instanceof Node\Stmt\For_ ||
            $node instanceof Node\Stmt\Foreach_) {
            foreach ($node->stmts as $stmt) {
                ExpressionStatement::apply($scopeManager, $stmt);
            }
        }

        return null;
    }
}
