<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use TudoCodigo\BurningPHP\Processor\NodeVisitor\MagicConstNodeVisitor;
use TudoCodigo\BurningPHP\Processor\ScopeManager\ScopeManager;
use TudoCodigo\BurningPHP\Support\Deterministic;
use TudoCodigo\BurningPHP\Support\Traits\SingletonPatternTrait;

class Processor
{
    use SingletonPatternTrait;

    /** @var Parser */
    private Parser $parser;

    private static function createParser(): Parser
    {
        return (new ParserFactory)->create(ParserFactory::PREFER_PHP7, new Lexer\Emulative([
            'usedAttributes' => [ 'startFilePos', 'endFilePos' ]
        ]));
    }

    public static function getPrettyPrinter(): PrettyPrinter
    {
        return Deterministic::withClosure(static function () {
            return new PrettyPrinter([ 'shortArraySyntax' => true ]);
        });
    }

    public function initialize(): void
    {
        $this->parser = self::createParser();
    }

    public function process(string $filePath): ProcessorFile
    {
        $processorFile = new ProcessorFile($filePath);

        $fileStatements = $this->parser->parse(file_get_contents($filePath));

        $traverser = new NodeTraverser;
        $traverser->addVisitor(new NameResolver);
        $traverser->addVisitor(new MagicConstNodeVisitor($processorFile->sourceOriginalPath));

        $modifiedFileStatements = $traverser->traverse($fileStatements);

        $scopeManager = new ScopeManager($processorFile);
        $scopeManager->processStatements($modifiedFileStatements);

        if ($processorFile->statements) {
            $prettyPrinter = static::getPrettyPrinter();

            $processorFile->writeProcessedSource($prettyPrinter->prettyPrintFile($modifiedFileStatements));
        }

        return $processorFile;
    }
}
