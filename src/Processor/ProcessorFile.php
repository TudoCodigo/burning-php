<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Processor;

use PhpParser\Node;
use TudoCodigo\BurningPHP\Configuration;
use TudoCodigo\BurningPHP\Support\Hash;

class ProcessorFile
{
    public string $hash;

    public string $hashFile;

    public bool $persisted = false;

    public string $sourceDuplicatePath;

    public string $sourceOriginalPath;

    /** @var resource */
    public $sourceOriginalResource;

    public string $sourceProcessedPath;

    public string $sourceStatementsPath;

    /** @var array[] */
    public array $statements = [];

    public function __construct(string $path)
    {
        $configuration = Configuration::getInstance();

        $this->sourceOriginalResource = fopen($path, 'rb');
        $this->sourceOriginalPath     = str_replace('/', DIRECTORY_SEPARATOR, $path);

        $this->hash     = Hash::shortifyFile($path);
        $this->hashFile = sprintf('%s_%s_%s_%s',
            $this->hash,
            $configuration->getBurningSourceHash(),
            $configuration->getImmutableAttributesHash(),
            $this->getBasename()
        );

        $cachePath    = str_replace('/', DIRECTORY_SEPARATOR, $configuration->getControlDirectory() . '/cache/');
        $filehashPath = $cachePath . $this->hashFile;

        $this->sourceDuplicatePath  = sprintf('%s%s_%s.php', $cachePath, $this->hash, $this->getBasename());
        $this->sourceProcessedPath  = $filehashPath . '.php';
        $this->sourceStatementsPath = $filehashPath . '.php.STATEMENTS';

        $this->loadStatements();
    }

    private function loadStatements(): void
    {
        if (is_readable($this->sourceStatementsPath)) {
            $this->persisted  = true;
            $this->statements = json_decode(file_get_contents($this->sourceStatementsPath), true, 512, JSON_THROW_ON_ERROR);
        }
    }

    private function writeStatementsStructure(): void
    {
        if (!$this->persisted && $this->statements) {
            file_put_contents($this->sourceStatementsPath, json_encode($this->statements, JSON_THROW_ON_ERROR), LOCK_EX);

            stream_copy_to_stream($this->sourceOriginalResource, fopen($this->sourceDuplicatePath, 'wb'));
        }
    }

    public function __destruct()
    {
        $this->writeStatementsStructure();
    }

    public function getBasename(): string
    {
        return preg_replace('/\..+?$/', null, basename($this->sourceOriginalPath));
    }

    public function getExecutableSourcePath(): string
    {
        if (!$this->statements && !$this->persisted) {
            return $this->sourceOriginalPath;
        }

        return $this->sourceProcessedPath;
    }

    public function writeProcessedSource(string $processedSource): void
    {
        file_put_contents($this->sourceProcessedPath, $processedSource, LOCK_EX);
    }

    public function writeStatement(string $statementType, Node $node, ?array $statementArguments = null): int
    {
        $nodeOffset = $node->getStartFilePos();

        $statementStructure = [
            'type'   => $statementType,
            'offset' => $nodeOffset,
            'length' => $node->getEndFilePos() - $nodeOffset + 1
        ];

        if ($statementArguments) {
            $statementStructure['arguments'] = $statementArguments;
        }

        $this->statements[] = $statementStructure;

        return count($this->statements) - 1;
    }
}
