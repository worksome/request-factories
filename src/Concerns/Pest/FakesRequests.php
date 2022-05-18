<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Concerns\Pest;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Worksome\RequestFactories\RequestFactory;

trait FakesRequests
{
    /**
     * @param class-string<FormRequest>|class-string<RequestFactory>|Closure(): RequestFactory $request
     * @param array<mixed> $attributes
     */
    public function fakeRequest(string|Closure $request, array|Closure $attributes = []): self
    {
        $factory = match (true) {
            is_subclass_of($request, FormRequest::class) => $request::factory(),
            is_subclass_of($request, RequestFactory::class) => $request::new(),
            default => $request(),
        };

        $factory->state($attributes)->fake();

        return $this;
    }
}
