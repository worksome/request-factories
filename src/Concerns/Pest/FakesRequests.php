<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Concerns\Pest;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Worksome\RequestFactories\RequestFactory;

trait FakesRequests
{
    /**
     * @param class-string<FormRequest> $request
     * @param array<mixed>|Closure(RequestFactory): RequestFactory $attributes
     */
    public function fakeRequest(string $request, array|Closure $attributes = []): self
    {
        $request::fake($attributes);

        return $this;
    }
}
