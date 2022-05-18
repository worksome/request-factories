<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Tests\Doubles\Factories;

use App\Http\Requests\ExampleFormRequest;
use Worksome\RequestFactories\RequestFactory;

final class DeclaredFormRequestPropertyFactory extends RequestFactory
{
    public static string $formRequest = ExampleFormRequest::class;

    public function definition(): array
    {
        return [];
    }
}
