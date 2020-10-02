<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;

class TryCatchStatement
    extends StatementAbstract
{
    public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node
    {
        if ($node instanceof Node\Stmt\TryCatch) {
            $node->stmts = self::applyStatements($scopeManager, $node->stmts, ExpressionStatement::class);

            if ($node->catches) {
                foreach ($node->catches as $catch) {
                    $catch->stmts = self::applyStatements($scopeManager, $catch->stmts, ExpressionStatement::class);
                }
            }

            if ($node->finally) {
                $node->finally->stmts = self::applyStatements($scopeManager, $node->finally->stmts, ExpressionStatement::class);
            }
        }

        return null;
    }
}
