<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Tests\Doubles\Factories;

use Closure;
use Worksome\RequestFactories\RequestFactory;

final class ExampleFormRequestFactory extends RequestFactory
{
    private static Closure|null $configurationCallback = null;

    public function definition(): array
    {
        return [
            'email' => $this->faker()->unique()->safeEmail,
            'name' => $this->faker()->name,
            'address' => AddressFormRequestFactory::new()->withPostCode(),
            'work' => [
                'name' => $this->faker()->company,
                'position' => $this->faker()->jobTitle,
            ],
            'banner_image' => $this->file()->image('banner.png'),
        ];
    }

    public function files(): array
    {
        return [
            'resume' => $this->file('resume.pdf'),
        ];
    }

    public function configure(): static
    {
        /**
         * This acts as a reset between tests so that configuration doesn't
         * leak over and cause strange bugs when running multiple tests
         * at together.
         */
        $instance = $this->afterCreating(function () {
            self::$configurationCallback = null;
        });

        if (self::$configurationCallback !== null) {
            return (self::$configurationCallback)($instance);
        }

        return $instance;
    }

    /**
     * @param Closure(self): self $callback
     */
    public static function configureUsing(Closure $callback): void
    {
        self::$configurationCallback = $callback;
    }

    public function withProfession(string $profession)
    {
        return $this->state(['profession' => $profession]);
    }

    public function withFakerPhoneNumber()
    {
        return $this->state(['number' => $this->faker->e164PhoneNumber]);
    }
}
