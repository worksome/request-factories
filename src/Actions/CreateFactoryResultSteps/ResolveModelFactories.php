<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Actions\CreateFactoryResultSteps;

use Closure;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Worksome\RequestFactories\Contracts\Actions\CreateFactoryResultStep;

final readonly class ResolveModelFactories implements CreateFactoryResultStep
{
    public function handle(Collection $data, Closure $next): Collection
    {
        $data = $data->map(fn (mixed $item) => $item instanceof Factory
            ? $item->createOne()->getKey()
            : $item);

        return $next($data);
    }
}
