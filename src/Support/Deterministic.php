<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Support;

class Deterministic
{
    /** @var mixed[] */
    private static array $cached = [];

    public static function withClosure(\Closure $closure, ?array $arguments = null)
    {
        $closureReflection = new \ReflectionFunction($closure);
        $closureThis       = $closureReflection->getClosureThis();

        $closureHash = hash('sha256', sprintf('%s:%u:%s',
            (string) $closureReflection,
            $closureThis ? spl_object_id($closureThis) : null,
            json_encode($arguments, JSON_THROW_ON_ERROR)
        ));

        if (array_key_exists($closureHash, self::$cached)) {
            return self::$cached[$closureHash];
        }

        return self::$cached[$closureHash] = $closure();
    }
}
