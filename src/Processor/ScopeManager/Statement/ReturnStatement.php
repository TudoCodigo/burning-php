<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;

class ReturnStatement
    extends StatementAbstract
{
    public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node
    {
        if ($node instanceof Node\Stmt\Return_) {
            $node->expr = ExpressionStatement::apply($scopeManager, $node->expr);
        }

        return null;
    }
}
