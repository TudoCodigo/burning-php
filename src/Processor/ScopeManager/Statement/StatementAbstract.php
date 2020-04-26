<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager\Statement;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;

abstract class StatementAbstract
{
    abstract public static function apply(ScopeManager $scopeManager, ?Node $node): ?Node;
}
