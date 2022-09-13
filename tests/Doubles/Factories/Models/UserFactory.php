<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Tests\Doubles\Factories\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class UserFactory extends Factory
{
    private static int $id = 1;
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => 'Luke Downing',
            'description' => 'Laravel developer',
        ];
    }

    public static function resetId(): void
    {
        self::$id = 1;
    }

    /**
     * We override this method because we want to avoid having to do
     * any database setup for testing this package. The ID will
     * simply be set to the auto-incrementing ID.
     */
    protected function store(Collection $results): void
    {
        $results->each(fn(Model $model, int $key) => $model->{$model->getKeyName()} = self::$id++);
    }
}
