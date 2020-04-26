<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Support;

class Hash
{
    public static function shortify(?string $data): string
    {
        return strtoupper(substr(hash('sha256', $data ?? ''), 0, 8));
    }

    public static function shortifyFile(string $path): string
    {
        return strtoupper(substr(hash_file('sha256', $path), 0, 8));
    }
}
