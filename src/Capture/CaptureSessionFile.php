<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Capture;

use TudoCodigo\BurningPHP\Configuration;
use TudoCodigo\BurningPHP\Processor\StatementTypes\StatementTypeAbstract;
use TudoCodigo\BurningPHP\Support\Hash;

class CaptureSessionFile
{
    /** @var array[] */
    public array $calls = [];

    public string $hash;

    public string $path;

    public CaptureSession $session;

    /** @var CaptureSessionStatement[] */
    public array $statements;

    public function __construct(CaptureSession $session, string $path)
    {
        $this->session = $session;
        $this->path    = $path;
        $this->hash    = Hash::shortifyFile($this->path);

        $this->loadStatements();
    }

    /** @param string|StatementTypeAbstract $statementType */
    private static function getCallKeyHash(string $statementType, array $arguments): ?string
    {
        $statementKeyHasher = $statementType::getCallKeyHasher($arguments);

        if ($statementKeyHasher === null) {
            return null;
        }

        return ':' . Hash::shortify(json_encode($statementKeyHasher, JSON_THROW_ON_ERROR));
    }

    private function loadStatements(): void
    {
        $configuration = Configuration::getInstance();

        $this->statements = array_map(function (array $statement) {
            return new CaptureSessionStatement(
                $this,
                $statement['type'],
                $statement['offset'],
                $statement['length'],
                $statement['arguments'] ?? null
            );
        }, json_decode(file_get_contents($configuration->getControlDirectory(sprintf('/cache/%s_%s_%s_%s.STATEMENTS',
            $this->hash,
            $configuration->getBurningSourceHash(),
            $configuration->getImmutableAttributesHash(),
            basename($this->path)
        ))), true, 512, JSON_THROW_ON_ERROR));
    }

    private function writeCallsStructure(): void
    {
        file_put_contents($this->session->getSessionDirectory(
            sprintf('/%s_%s.CALLS', $this->hash, basename($this->path))),
            json_encode($this->calls, JSON_THROW_ON_ERROR)
        );
    }

    public function __destruct()
    {
        $this->writeCallsStructure();
    }

    public function getShortPath(): string
    {
        $configuration = Configuration::getInstance();

        if (str_starts_with($this->path, $configuration->currentWorkingDir)) {
            return substr($this->path, strlen($configuration->currentWorkingDir) + 1);
        }

        return $this->path;
    }

    public function notify(int $statementId, array $arguments): void
    {
        $statement = $this->statements[$statementId];

        if (!$statement->type::validate($arguments)) {
            return;
        }

        $callKey = $statementId . self::getCallKeyHash($statement->type, $arguments);

        if (!array_key_exists($callKey, $this->calls)) {
            $this->calls[$callKey] = $statement->type::getCallInitialData($arguments);
        }

        $statement->type::updateCallData($this->calls[$callKey], $arguments);
    }
}
