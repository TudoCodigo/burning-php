<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\ScopeManager;

use PhpParser\Node\Stmt;
use TudoCodigo\BurningPHP\Processor\ProcessorFile;
use TudoCodigo\BurningPHP\Processor\ScopeManager\Statement\ClassStatement;
use TudoCodigo\BurningPHP\Processor\ScopeManager\Statement\ExpressionStatement;
use TudoCodigo\BurningPHP\Processor\ScopeManager\Statement\FunctionLikeStatement;
use TudoCodigo\BurningPHP\Processor\ScopeManager\Statement\IfStatement;
use TudoCodigo\BurningPHP\Processor\ScopeManager\Statement\NamespaceStatement;

class ScopeManager
{
    public ProcessorFile $processorFile;

    public function __construct(ProcessorFile $processorFile)
    {
        $this->processorFile = $processorFile;
    }

    public function processStatements(array $statements): void
    {
        /** @var Stmt $stmt */
        foreach ($statements as $stmt) {
            NamespaceStatement::apply($this, $stmt) ||
            ClassStatement::apply($this, $stmt) ||
            FunctionLikeStatement::apply($this, $stmt) ||
            ExpressionStatement::apply($this, $stmt) ||
            IfStatement::apply($this, $stmt);
        }
    }
}
