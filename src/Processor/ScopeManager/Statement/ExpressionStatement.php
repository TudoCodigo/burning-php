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
        if ($node instanceof Node\Stmt\Expression ||
            $node instanceof Node\Expr\Cast ||
            $node instanceof Node\Expr\Clone_ ||
            $node instanceof Node\Expr\Empty_ ||
            $node instanceof Node\Expr\ErrorSuppress ||
            $node instanceof Node\Stmt\Return_ ||
            $node instanceof Node\Expr\Eval_ ||
            $node instanceof Node\Expr\Exit_ ||
            $node instanceof Node\Expr\Include_ ||
            $node instanceof Node\Expr\Print_) {
            $node->expr = self::apply($scopeManager, $node->expr);
        }
        else if ($node instanceof Node\Expr\Isset_ ||
                 $node instanceof Node\Stmt\Unset_) {
            $node->vars = self::applyStatements($scopeManager, $node->vars, self::class);
        }
        else if ($node instanceof Node\Expr\Instanceof_) {
            $node->expr = self::apply($scopeManager, $node->expr);
            $node->class = self::apply($scopeManager, $node->class);
        }

        return FunctionStatement::apply($scopeManager, $node) ??
               ClassStatement::apply($scopeManager, $node) ??
               FunctionLikeStatement::apply($scopeManager, $node) ??
               ConditionalStatement::apply($scopeManager, $node) ??
               LoopStatement::apply($scopeManager, $node) ??
               BooleanStatement::apply($scopeManager, $node) ??
               AssignStatement::apply($scopeManager, $node) ??
               ArrayStatement::apply($scopeManager, $node) ??
               $node;
    }
}
