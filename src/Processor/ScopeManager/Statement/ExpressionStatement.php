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
            $node instanceof Node\Expr\Print_ ||
            $node instanceof Node\Expr\UnaryMinus ||
            $node instanceof Node\Expr\UnaryPlus ||
            $node instanceof Node\Stmt\Throw_ ||
            $node instanceof Node\Expr\Throw_) {
            $node->expr = self::apply($scopeManager, $node->expr);
        }
        else if ($node instanceof Node\Expr\Isset_ ||
                 $node instanceof Node\Stmt\Unset_ ||
                 $node instanceof Node\Stmt\Global_) {
            $node->vars = self::applyStatements($scopeManager, $node->vars, self::class);
        }
        else if ($node instanceof Node\Expr\Instanceof_) {
            $node->expr  = self::apply($scopeManager, $node->expr);
            $node->class = self::apply($scopeManager, $node->class);
        }
        else if ($node instanceof Node\Stmt\Break_ ||
                 $node instanceof Node\Stmt\Continue_) {
            $node->num = self::apply($scopeManager, $node->num);
        }
        else if ($node instanceof Node\Expr\PostDec ||
                 $node instanceof Node\Expr\PostInc ||
                 $node instanceof Node\Expr\PreDec ||
                 $node instanceof Node\Expr\PreInc) {
            $node->var = self::apply($scopeManager, $node->var);
        }
        else if ($node instanceof Node\Stmt\Echo_) {
            $node->exprs = self::applyStatements($scopeManager, $node->exprs, self::class);
        }
        else if ($node instanceof Node\Expr\Variable &&
                 $node->name instanceof Node) {
            $node->name = self::apply($scopeManager, $node->name);
        }

        return FunctionStatement::apply($scopeManager, $node) ??
               ClassStatement::apply($scopeManager, $node) ??
               FunctionLikeStatement::apply($scopeManager, $node) ??
               ConditionalStatement::apply($scopeManager, $node) ??
               LoopStatement::apply($scopeManager, $node) ??
               BooleanStatement::apply($scopeManager, $node) ??
               AssignStatement::apply($scopeManager, $node) ??
               ArrayStatement::apply($scopeManager, $node) ??
               TryCatchStatement::apply($scopeManager, $node) ??
               $node;
    }
}
