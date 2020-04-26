<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;

class TernaryStatement
    extends StatementAbstract
{
    public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node
    {
        if ($node instanceof Node\Expr\Ternary) {
            $node->cond = ExpressionStatement::apply($scopeManager, $node->cond);
            $node->if   = ExpressionStatement::apply($scopeManager, $node->if);
            $node->else = ExpressionStatement::apply($scopeManager, $node->else);
        }

        return null;
    }
}
