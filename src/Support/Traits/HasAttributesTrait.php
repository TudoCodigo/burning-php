<?php

declare(strict_types = 1);

namespace TudoCodigo\BurningPHP\Support\Traits;

use TudoCodigo\BurningPHP\Support\Deterministic;
use TudoCodigo\BurningPHP\Support\Hash;

trait HasAttributesTrait
{
    /** @var mixed[] */
    protected array $attributes = [];

    public function &__get(string $name)
    {
        return $this->attributes[$name];
    }

    public function __set(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return array_key_exists($name, $this->attributes);
    }

    public function getAttributesHash(): string
    {
        return Hash::shortify(serialize($this->attributes));
    }

    public function getImmutableAttributesHash(): string
    {
        return Deterministic::withClosure(function () {
            return $this->getAttributesHash();
        });
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function mergeAttributesWith(array $attributes): void
    {
        foreach ($attributes as $attributeKey => &$attributeValue) {
            if (is_array($attributeValue) &&
                in_array('@parent', $attributeValue, true)) {
                if (!array_key_exists($attributeKey, $this->attributes)) {
                    $attributeValue = array_values(array_filter($attributeValue, static function ($attributeValue) {
                        return $attributeValue !== '@parent';
                    }));
                }
                else if (is_array($this->attributes[$attributeKey])) {
                    $attributeValue = array_merge(... array_map(function ($attributeValueItems) use ($attributeKey) {
                        if ($attributeValueItems === '@parent') {
                            return $this->attributes[$attributeKey];
                        }

                        return [ $attributeValueItems ];
                    }, $attributeValue));
                }
            }
        }

        unset($attributeValue);

        $this->attributes = (array) array_replace_recursive($this->attributes, $attributes);
    }

    public function toArray(): array
    {
        $attributes = $this->attributes;

        foreach ((new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $attributes[$reflectionProperty->name] = $reflectionProperty->getValue($this);
        }

        return array_map(static function ($value) {
            return $value instanceof \JsonSerializable
                ? $value->jsonSerialize()
                : $value;
        }, $attributes);
    }
}
