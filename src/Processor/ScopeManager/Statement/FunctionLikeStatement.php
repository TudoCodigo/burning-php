<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;

class FunctionLikeStatement
    extends StatementAbstract
{
    public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node
    {
        if ($node instanceof Node\FunctionLike &&
            $node->stmts) {
            foreach ($node->stmts as $stmt) {
                ExpressionStatement::apply($scopeManager, $stmt);
            }
        }

        return null;
    }
}
