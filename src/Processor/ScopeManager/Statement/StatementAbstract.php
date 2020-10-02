<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;

abstract class StatementAbstract
{
    protected static function isApplicable(Node $node): bool
    {
        $appliedKey = static::class . '@applied';

        if (!$node->hasAttribute($appliedKey)) {
            $node->setAttribute($appliedKey, true);

            return true;
        }

        return false;
    }

    abstract public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node;

    /**
     * @param Node[]                   $statements
     * @param string|StatementAbstract $statementClass
     */
    public static function applyStatements(ScopeManager $scopeManager, ?array $statements, string $statementClass): ?array
    {
        if (!$statements) {
            return $statements;
        }

        foreach ($statements as &$statement) {
            $statement = $statementClass::apply($scopeManager, $statement);
        }

        return $statements;
    }
}
