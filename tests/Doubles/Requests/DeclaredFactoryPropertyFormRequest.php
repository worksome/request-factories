<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Worksome\RequestFactories\Concerns\HasFactory;
use Worksome\RequestFactories\Tests\Doubles\Factories\ExampleFormRequestFactory;

final class DeclaredFactoryPropertyFormRequest extends FormRequest
{
    use HasFactory;

    public static string $factory = ExampleFormRequestFactory::class;
}
