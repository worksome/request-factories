<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Support;

use ArrayAccess;
use ArrayIterator;
use BadMethodCallException;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use IteratorAggregate;
use SplFileInfo;
use Traversable;

final class Result implements Arrayable, ArrayAccess, IteratorAggregate
{

    public function __construct(private array $attributes)
    {
    }

    /**
     * All attributes from the factory, including files.
     *
     * @return array<mixed>
     */
    public function all(): array
    {
        return $this->attributes;
    }

    /**
     * All attributes from the array except files.
     *
     * @return array<mixed>
     */
    public function input(): array
    {
        return array_filter($this->attributes, fn ($attribute) => ! $attribute instanceof SplFileInfo);
    }

    /**
     * All files from the factory attributes.
     *
     * @return array<SplFileInfo>
     */
    public function files(): array
    {
        return array_filter($this->attributes, fn ($attribute) => $attribute instanceof SplFileInfo);
    }

    public function hasFiles(): bool
    {
        return count($this->files()) > 0;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return $this->all();
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->all());
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->all()[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new BadMethodCallException('Factory results cannot be mutated.');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new BadMethodCallException('Factory results cannot be mutated.');
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->all());
    }

    public function __get(string $name): mixed
    {
        if (! key_exists($name, $this->all())) {
            throw new InvalidArgumentException("[{$name}] was not part of the factory attributes.");
        }

        return $this->all()[$name];
    }
}
