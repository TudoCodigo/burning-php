<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;

class AssignStatement
    extends StatementAbstract
{
    public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node
    {
        if ($node instanceof Node\Expr\Assign ||
            $node instanceof Node\Expr\AssignOp) {
            $node->expr = ExpressionStatement::apply($scopeManager, $node->expr);
        }

        return null;
    }
}
