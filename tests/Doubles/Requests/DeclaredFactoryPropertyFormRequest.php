<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Worksome\RequestFactories\Tests\Doubles\Factories\ExampleFormRequestFactory;

final class DeclaredFactoryPropertyFormRequest extends FormRequest
{
    public static string $factory = ExampleFormRequestFactory::class;
}
