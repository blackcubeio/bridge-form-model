<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeFormModel\Components;

use LogicException;

/**
 * Bridge manager
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class Bridge
{
    private array $endpoints = [];
    private array $meta = [];
    private array $rules = [];

    public function __construct(
        private string $name,
    ) {
    }

    public function setEndpoint(
        string $className,
        ?string $getter = null,
        ?string $setter = null,
        string|false|null $property = null,
        ?string $type = null,
        ?string $format = null,
        bool $isNullable = false,
        bool $isElastic = false,
    ): void {
        $existing = $this->endpoints[$className] ?? [
            'getter' => null,
            'setter' => null,
            'property' => null,
            'type' => null,
            'format' => null,
            'isNullable' => false,
            'isElastic' => false,
        ];

        $candidates = [
            'getter' => $getter,
            'setter' => $setter,
            'property' => $property,
            'type' => $type,
            'format' => $format,
            'isNullable' => $isNullable === true ? true : null,
            'isElastic' => $isElastic === true ? true : null,
        ];
        $update = [];
        foreach ($candidates as $key => $value) {
            if ($value !== null) {
                $update[$key] = $value;
            }
        }

        $this->endpoints[$className] = [
            ...$existing,
            ...$update,
        ];
    }

    public function get(mixed $model): mixed
    {
        $className = get_class($model);
        if (isset($this->endpoints[$className]) === false) {
            throw new LogicException('Model class not registered: ' . $className);
        }

        $endpoint = $this->endpoints[$className];
        if ($endpoint['getter'] !== null) {
            $value = $model->{$endpoint['getter']}();
        } elseif ($endpoint['property'] === false) {
            $value = null;
        } else {
            $prop = $endpoint['property'] ?? $this->name;
            $value = $model->{$prop};
        }
        return $value;
    }

    public function set(mixed $model, mixed $value): void
    {
        $className = get_class($model);
        if (isset($this->endpoints[$className]) === false) {
            throw new LogicException('Model class not registered: ' . $className);
        }

        $endpoint = $this->endpoints[$className];
        if ($endpoint['setter'] !== null) {
            $model->{$endpoint['setter']}($value);
        } elseif ($endpoint['property'] !== false) {
            $prop = $endpoint['property'] ?? $this->name;
            $model->{$prop} = $value;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function hasEndpoint(string $className): bool
    {
        return isset($this->endpoints[$className]) === true;
    }

    public function getEndpoint(string $className): ?array
    {
        return $this->endpoints[$className] ?? null;
    }

    public function getType(mixed $model): ?string
    {
        $className = get_class($model);
        return $this->endpoints[$className]['type'] ?? null;
    }

    public function getFormat(mixed $model): ?string
    {
        $className = get_class($model);
        return $this->endpoints[$className]['format'] ?? null;
    }

    public function isNullable(mixed $model): bool
    {
        $className = get_class($model);
        return $this->endpoints[$className]['isNullable'] ?? false;
    }

    public function isElastic(string $className): bool
    {
        return $this->endpoints[$className]['isElastic'] ?? false;
    }

    public function isTransferable(): bool
    {
        return count($this->endpoints) >= 2;
    }

    public function setMeta(array $meta): void
    {
        $this->meta = $meta;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function setRules(array $rules): void
    {
        $this->rules = $rules;
    }

    public function getRules(): array
    {
        return $this->rules;
    }
}