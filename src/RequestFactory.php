<?php

declare(strict_types=1);

namespace Worksome\RequestFactories;

use Closure;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Http\Testing\File;
use Illuminate\Http\Testing\FileFactory;
use Illuminate\Http\UploadedFile;
use Worksome\RequestFactories\Actions\UndotArrayKeys;
use Worksome\RequestFactories\Contracts\Actions\CreatesFactoryResult;
use Worksome\RequestFactories\Contracts\Actions\UndotsArrayKeys;
use Worksome\RequestFactories\Support\FactoryData;

abstract class RequestFactory
{
    /**
     * @var (Closure(): Generator)|null
     */
    private static Closure|null $fakerResolver = null;

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
        $this->faker = $this->withFaker();
    }

    /**
     * @param Closure(): Generator $resolver
     */
    public static function setFakerResolver(Closure $resolver): void
    {
        self::$fakerResolver = $resolver;
    }

    /**
     * @param array<string, mixed>|static $attributes
     */
    public static function new(array|self $attributes = []): static
    {
        /**
         * When working with datasets, you may have a mixture of plain arrays and
         * factories to work with depending on the complexity of the test. Here
         * we avoid having to manually check for an array or factory instance.
         */
        if ($attributes instanceof static) {
            return new static(
                $attributes->attributes,
                $attributes->without,
                $attributes->afterCreatingHooks
            );
        }

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
        return $this->newInstance(attributes: $this->getUndotsArrayKeysAction()($attributes));
    }

    /**
     * Indicate that the given attributes should be omitted from the
     * request. You can use dot syntax here to unset deeply nested
     * keys in request data.
     *
     * @param array<int, string> $attributes
     */
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
     * Create the factory and return an array of attributes that can be
     * passed as data to a request.
     *
     * @param array<mixed> $attributes
     * @return array<mixed>
     */
    public function create(array $attributes = []): array
    {
        // @phpstan-ignore-next-line
        return app(CreatesFactoryResult::class)($this->state($attributes))->all();
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

    protected function image(string $name, int $width = 10, int $height = 10): File
    {
        return $this->file()->image($name, $width, $height);
    }

    protected function faker(): Generator
    {
        return $this->faker;
    }

    protected function getUndotsArrayKeysAction(): UndotsArrayKeys
    {
        return new UndotArrayKeys();
    }

    protected function newInstance(
        array $attributes = [],
        array $without = [],
        array $afterCreatingHooks = [],
    ): static {
        return new static(
            array_replace_recursive($this->attributes, $attributes),
            array_merge($this->without, $without),
            array_merge($this->afterCreatingHooks, $afterCreatingHooks),
        );
    }

    protected function withFaker(): Generator
    {
        return self::$fakerResolver ? (self::$fakerResolver)() : Factory::create();
    }

    public function getFactoryData(): FactoryData
    {
        return new FactoryData(
            $this->definition(),
            $this->files(),
            $this->attributes,
            $this->without,
            $this->afterCreatingHooks,
        );
    }
}
