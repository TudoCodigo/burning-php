<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;

class ArrayStatement
    extends StatementAbstract
{
    public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node
    {
        if ($node instanceof Node\Expr\ArrayDimFetch) {
            $node->dim = ExpressionStatement::apply($scopeManager, $node->dim);
        }
        else if ($node instanceof Node\Expr\Array_) {
            $node->items = self::applyStatements($scopeManager, $node->items, ExpressionStatement::class);
        }
        else if ($node instanceof Node\Expr\ArrayItem) {
            $node->key   = ExpressionStatement::apply($scopeManager, $node->key);
            $node->value = ExpressionStatement::apply($scopeManager, $node->value);
        }

        return null;
    }
}
