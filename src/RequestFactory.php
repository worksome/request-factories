<?php

declare(strict_types=1);

namespace Worksome\RequestFactories;

use Faker\Factory;
use Faker\Generator;

abstract class RequestFactory
{
    protected Generator $faker;

    public function __construct(protected array $attributes = [])
    {
        $this->faker = Factory::create();
    }

    public static function new(array $attributes = []): static
    {
        return new static($attributes);
    }

    /**
     * Here you should provide all data required
     * to form a valid request.
     *
     * @return array<string, mixed>
     */
    abstract public function definition(): array;

    protected function faker(): Generator
    {
        return $this->faker;
    }

    /**
     * @param array<mixed> $attributes
     */
    public function state(array $attributes): static
    {
        return new static(
            array_merge($this->attributes, $attributes)
        );
    }

    /**
     * Return an array of data to be used as mock input
     * for the relevant Form Request.
     *
     * @return array<mixed>
     */
    public function create(array $attributes = []): array
    {
        $requestedData = collect(array_merge(
            $this->definition(),
            $this->attributes,
            $attributes
        ));

        return $requestedData->map(fn (mixed $data) => $this->handleData($data))->all();
    }

    protected function handleData(mixed $data): mixed
    {
        if ($data instanceof RequestFactory) {
            $data = $data->create();
        }

        return $data;
    }
}
