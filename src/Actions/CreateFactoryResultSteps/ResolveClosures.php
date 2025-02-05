<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Actions\CreateFactoryResultSteps;

use Closure;
use Illuminate\Support\Collection;
use Worksome\RequestFactories\Contracts\Actions\CreateFactoryResultStep;

final readonly class ResolveClosures implements CreateFactoryResultStep
{
    public function handle(Collection $data, Closure $next): Collection
    {
        $data = $data->reduce(
            fn(Collection $carry, mixed $item, mixed $key) => $carry->put($key, $item instanceof Closure
                ? $item($carry->all())
                : $item),
            $data
        );

        return $next($data);
    }
}
