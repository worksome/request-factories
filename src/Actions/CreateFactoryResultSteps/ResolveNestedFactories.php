<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Actions\CreateFactoryResultSteps;

use Closure;
use Illuminate\Support\Collection;
use Worksome\RequestFactories\Contracts\Actions\CreatesFactoryResult;
use Worksome\RequestFactories\RequestFactory;

final class ResolveNestedFactories
{
    public function __construct(private CreatesFactoryResult $createFactoryResult)
    {
    }

    public function handle(Collection $data, Closure $next): mixed
    {
        $data = $data->map(fn (mixed $item) => $item instanceof RequestFactory
            ? $this->createFactoryResult->__invoke($item)->all()
            : $item);

        return $next($data);
    }
}
