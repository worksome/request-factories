<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Tests\Doubles\Factories;

use Closure;
use Worksome\RequestFactories\RequestFactory;
use Worksome\RequestFactories\Support\Result;

final class ExampleFormRequestFactory extends RequestFactory
{
    private static Closure|null $configurationCallback = null;

    public function definition(): array
    {
        return [
            'email' => $this->faker()->unique()->safeEmail,
            'name' => $this->faker()->name,
            'address' => AddressFormRequestFactory::new()->withPostCode(),
            'banner_image' => $this->file()->image('banner.png'),
            'resume' => $this->file('resume.pdf'),
        ];
    }

    public function configure(): static
    {
        if (self::$configurationCallback !== null) {
            return (self::$configurationCallback)($this);
        }

        return $this;
    }

    /**
     * @param Closure(self): self $callback
     */
    public static function configureUsing(Closure $callback): void
    {
        self::$configurationCallback = $callback;
    }

    public function create(array $attributes = []): Result
    {
        $result = parent::create($attributes);

        /**
         * We don't want this static value to seep into other tests in the same process,
         * so we'll clear the variable after we know that configure has been called.
         * This acts as an automated reset between tests.
         */
        static::$configurationCallback = null;

        return $result;
    }
}
