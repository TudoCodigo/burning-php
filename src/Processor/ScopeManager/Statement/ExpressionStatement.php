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

        return FunctionStatement::apply($scopeManager, $node) ??
               FunctionLikeStatement::apply($scopeManager, $node) ??
               ConditionalStatement::apply($scopeManager, $node) ??
               LoopStatement::apply($scopeManager, $node) ??
               BooleanStatement::apply($scopeManager, $node) ??
               AssignStatement::apply($scopeManager, $node) ??
               ReturnStatement::apply($scopeManager, $node) ??
               ArrayStatement::apply($scopeManager, $node) ??
               CastStatement::apply($scopeManager, $node) ??
               CloneStatement::apply($scopeManager, $node) ??
               EmptyStatement::apply($scopeManager, $node) ??
               $node;
    }
}
