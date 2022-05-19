<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Concerns\Pest;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Worksome\RequestFactories\RequestFactory;
use Worksome\RequestFactories\Support\HigherOrderRequestFactory;

trait FakesRequests
{
    /**
     * @param class-string<FormRequest>|class-string<RequestFactory>|Closure(): RequestFactory $request
     * @param array<mixed> $attributes
     */
    public function fakeRequest(string|Closure $request, array $attributes = []): HigherOrderRequestFactory
    {
        $factory = match (true) {
            is_subclass_of($request, FormRequest::class) => $request::factory()->state($attributes),
            is_subclass_of($request, RequestFactory::class) => $request::new($attributes),
            default => $request(),
        };

        $factory->fake();

        return new HigherOrderRequestFactory($factory);
    }
}
