<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Capture;

use TudoCodigo\BurningPHP\Processor\StatementTypes\StatementTypeAbstract;

class CaptureSessionStatement
{
    public ?array $arguments;

    public int $length;

    public int $offset;

    public CaptureSessionFile $sessionFile;

    /** @var string|StatementTypeAbstract */
    public string $type;

    public function __construct(CaptureSessionFile $sessionFile, string $type, int $offset, int $length, ?array $arguments)
    {
        $this->sessionFile = $sessionFile;
        $this->type        = $type;
        $this->offset      = $offset;
        $this->length      = $length;
        $this->arguments   = $arguments;
    }
}
