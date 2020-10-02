<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;

class BooleanStatement
    extends StatementAbstract
{
    public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node
    {
        if ($node instanceof Node\Expr\BooleanNot) {
            $node->expr = ExpressionStatement::apply($scopeManager, $node->expr);
        }
        else if ($node instanceof Node\Expr\BinaryOp) {
            $node->left  = ExpressionStatement::apply($scopeManager, $node->left);
            $node->right = ExpressionStatement::apply($scopeManager, $node->right);
        }

        return null;
    }
}
