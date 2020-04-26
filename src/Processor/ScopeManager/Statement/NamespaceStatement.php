<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;

class NamespaceStatement
    extends StatementAbstract
{
    public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            foreach ($node->stmts as $stmt) {
                ClassStatement::apply($scopeManager, $stmt) ||
                FunctionLikeStatement::apply($scopeManager, $stmt);
            }
        }

        return null;
    }
}
