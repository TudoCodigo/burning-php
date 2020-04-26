<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Support\Traits;

trait SingletonPatternTrait
{
    /** @var static[] */
    protected static array $instances = [];

    private static function getInstanceNullable(): ?self
    {
        return self::$instances[static::class] ?? null;
    }

    public static function destructInstance(): void
    {
        if (array_key_exists(static::class, self::$instances)) {
            unset(self::$instances[static::class]);
        }
    }

    /** @return static */
    public static function getInstance(?bool $reinitialize = null): self
    {
        if ($reinitialize === true) {
            $instance = new static;
            $instance->initialize();

            return self::$instances[static::class] = $instance;
        }

        $instance = self::getInstanceNullable();

        if ($instance) {
            return $instance;
        }

        return static::getInstance(true);
    }

    public function initialize(): void
    {
    }
}
