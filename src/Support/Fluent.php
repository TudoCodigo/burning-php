<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Support;

use TudoCodigo\BurningPHP\Support\Traits\HasAttributesTrait;

class Fluent
    implements \JsonSerializable
{
    use HasAttributesTrait;
}
