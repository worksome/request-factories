<?php

declare(strict_types=1);

namespace Worksome\RequestFactories;

use Closure;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Http\Testing\File;
use Illuminate\Http\Testing\FileFactory;
use Illuminate\Http\UploadedFile;

abstract class RequestFactory
{
    protected Generator $faker;

    /**
     * @param array<mixed> $attributes
     * @param array<Closure(array): array|void> $afterCreatingHooks
     */
    final public function __construct(
        protected array $attributes = [],
        protected array $without = [],
        protected array $afterCreatingHooks = [],
    ) {
        $this->faker = Factory::create();
    }

    public static function new(array $attributes = []): static
    {
        return (new static())->state($attributes)->configure();
    }

    /**
     * Here you should provide all data required
     * to form a valid request.
     *
     * @return array<string, mixed>
     */
    abstract public function definition(): array;

    /**
     * Define an array of files that should be included in the request.
     * You may put files in the standard definition if you prefer.
     *
     * @return array<string, File>
     */
    public function files(): array
    {
        return [];
    }

    /**
     * If you would like to perform some form of internal setup,
     * you may override this method and insert any desired
     * logic. It will be called before creating.
     */
    public function configure(): static
    {
        return $this->newInstance();
    }

    /**
     * Provide an array of request keys and desired values
     * to be merged in to any existing attributes.
     *
     * @param array<mixed> $attributes
     */
    public function state(array $attributes): static
    {
        return $this->newInstance(attributes: $attributes);
    }

    public function without(array $attributes): static
    {
        return $this->newInstance(without: $attributes);
    }

    /**
     * Provide a Closure that will be called after the request data
     * has been created. If you return an array from that Closure,
     * it will replace the generated request data.
     *
     * @param Closure(array<mixed> $attributes): (array<mixed>|void) $callback
     */
    public function afterCreating(Closure $callback): static
    {
        return $this->newInstance(afterCreatingHooks: [$callback]);
    }

    /**
     * @return array<mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
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

    /**
     * @param string|null $name
     * @return FileFactory|File
     */
    protected function file(string $name = null): FileFactory|File
    {
        if ($name === null) {
            return UploadedFile::fake();
        }

        return UploadedFile::fake()->create($name);
    }

    protected function faker(): Generator
    {
        return $this->faker;
    }

    protected function newInstance(
        array $attributes = [],
        array $without = [],
        array $afterCreatingHooks = [],
    ): static {
        return new static(
            array_merge($this->attributes, $attributes),
            array_merge($this->without, $without),
            array_merge($this->afterCreatingHooks, $afterCreatingHooks),
        );
    }

    /**
     * Register the factory in its current state to be merged
     * into the next request.
     */
    public function fake(): void
    {
        // @phpstan-ignore-next-line
        app(FactoryManager::class)->fake($this);
    }
}
