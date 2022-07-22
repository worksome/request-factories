<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Support;

use Closure;

/**
 * @internal
 */
final class FactoryData
{
    /**
     * @param array<mixed> $definition
     * @param array<mixed> $files
     * @param array<mixed> $attributes
     * @param array<int, string> $without
     * @param array<Closure(array): array|void> $afterCreatingHooks
     */
    public function __construct(
        private array $definition,
        private array $files,
        private array $attributes,
        private array $without,
        private array $afterCreatingHooks,
    ) {
    }

    /**
     * @return array<mixed>
     */
    public function getRequestedData(): array
    {
        return array_replace_recursive($this->definition, $this->files, $this->attributes);
    }

    /**
     * @return array<int, string>
     */
    public function getWithout(): array
    {
        return $this->without;
    }

    /**
     * @return array<Closure(array): array|void>
     */
    public function getAfterCreatingHooks(): array
    {
        return $this->afterCreatingHooks;
    }
}
