<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;

class ExpressionStatement
    extends StatementAbstract
{
    public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node
    {
        if ($node instanceof Node\Stmt\Expression) {
            $node->expr = self::apply($scopeManager, $node->expr);
        }

        return FunctionCallStatement::apply($scopeManager, $node) ??
               IfStatement::apply($scopeManager, $node) ??
               LoopStatement::apply($scopeManager, $node) ??
               BooleanNotStatement::apply($scopeManager, $node) ??
               BooleanOpStatement::apply($scopeManager, $node) ??
               TernaryStatement::apply($scopeManager, $node) ??
               AssignOpStatement::apply($scopeManager, $node) ??
               ReturnStatement::apply($scopeManager, $node) ??
               $node;
    }
}
