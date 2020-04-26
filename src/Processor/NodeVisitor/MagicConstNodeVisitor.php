<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class MagicConstNodeVisitor
    extends NodeVisitorAbstract
{
    protected string $workingDirectory;

    public function __construct(string $workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;
    }

    public function enterNode(Node $node): ?Node\Scalar\String_
    {
        if ($node instanceof Node\Scalar\MagicConst\Dir) {
            return new Node\Scalar\String_(dirname($this->workingDirectory));
        }

        if ($node instanceof Node\Scalar\MagicConst\File) {
            return new Node\Scalar\String_($this->workingDirectory);
        }

        return parent::enterNode($node);
    }
}
