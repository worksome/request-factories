<?php

declare(strict_types=1);

namespace Worksome\RequestFactories;

use Closure;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Http\Testing\File;
use Illuminate\Http\Testing\FileFactory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Worksome\RequestFactories\Contracts\Finder;
use Worksome\RequestFactories\Support\Map;
use Worksome\RequestFactories\Support\Result;

abstract class RequestFactory
{
    protected Generator $faker;

    /**
     * @param array<mixed> $attributes
     * @param array<Closure(array): array|void> $afterCreatingHooks
     */
    public function __construct(
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

    /**
     * Provide a Closure that will be called after the request data
     * has been created. If you return an array from that Closure,
     * it will replace the generated request data.
     *
     * @param Closure(array<mixed> $attributes): array<mixed>|void
     */
    public function afterCreating(Closure $callback): static
    {
        return $this->newInstance(afterCreatingHooks: [$callback]);
    }

    /**
     * Return an array of data to be used as mock input
     * for the relevant Form Request.
     */
    public function create(array $attributes = []): Result
    {
        $requestedData = collect(array_merge(
            $this->definition(),
            $this->attributes,
            $attributes
        ));

        /**
         * We now need to handle "special" objects in the $requestedData array, such
         * as other Request Factories and Closures. Closures should always resolve
         * after everything else, so we do this step in two separate stages.
         */
        $dataBeforeResolvingClosures = $requestedData->map(fn (mixed $data) => $this->handleData($data));

        $dataBeforeResolvingAfterCreatingHooks = $dataBeforeResolvingClosures
            ->map(fn (mixed $data) => $this->handleClosure($data, $dataBeforeResolvingClosures->all()))
            ->all();

        $dataAfterRemovingWithouts = $this->unsetRequestedWithouts($dataBeforeResolvingAfterCreatingHooks);

        return new Result($this->invokeAfterCreatingHooks($dataAfterRemovingWithouts));
    }

    private function unsetRequestedWithouts(array &$requestedData): array
    {
        Arr::forget($requestedData, $this->without);

        return $requestedData;
    }

    protected function faker(): Generator
    {
        return $this->faker;
    }

    protected function handleData(mixed $data): mixed
    {
        if ($data instanceof RequestFactory) {
            $data = $data->create()->input();
        }

        return $data;
    }

    protected function handleClosure(mixed $data, array $attributes): mixed
    {
        if (! $data instanceof Closure) {
            return $data;
        }

        return $data($attributes);
    }

    /**
     * @param array<mixed> $attributes
     * @return array<mixed>
     */
    protected function invokeAfterCreatingHooks(array $attributes): array
    {
        return collect($this->afterCreatingHooks)->reduce(
            fn ($latestAttributes, Closure $closure) => $closure($latestAttributes) ?? $latestAttributes,
            $attributes
        );
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
     * Register the factory in its current state as the one to use
     * when its FormRequest is resolved from the container.
     */
    public function fake(): void
    {
        $map = new Map(app(Finder::class));

        app(FactoryManager::class)->fake(
            $map->factoryToFormRequest(static::class),
            $this
        );
    }
}
