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

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @implements Arrayable<TKey, TValue>
 * @implements ArrayAccess<TKey, TValue>
 * @implements IteratorAggregate<TKey, TValue>
 */
final readonly class Result implements Arrayable, ArrayAccess, IteratorAggregate
{
    /** @param  array<TKey, TValue>  $attributes */
    public function __construct(private array $attributes)
    {
    }

    /**
     * All attributes from the factory, including files.
     *
     * @return array<TKey, TValue>
     */
    public function all(): array
    {
        return $this->attributes;
    }

    /**
     * All attributes from the array except files.
     *
     * @return array<TKey, TValue>
     */
    public function input(): array
    {
        return array_filter($this->attributes, fn ($attribute) => ! $attribute instanceof SplFileInfo);
    }

    /**
     * All files from the factory attributes.
     *
     * @return array<TKey, SplFileInfo>
     */
    public function files(): array
    {
        return array_filter($this->attributes, fn (mixed $attribute) => $attribute instanceof SplFileInfo);
    }

    public function hasFiles(): bool
    {
        return count($this->files()) > 0;
    }

    /**
     * @return array<TKey, TValue>
     */
    public function toArray(): array
    {
        return $this->all();
    }

    /**
     * @param TKey $offset
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->all());
    }

    /**
     * @param TKey $offset
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->all()[$offset];
    }

    /**
     * @param TKey $offset
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new BadMethodCallException('Factory results cannot be mutated.');
    }

    /**
     * @param TKey $offset
     */
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
