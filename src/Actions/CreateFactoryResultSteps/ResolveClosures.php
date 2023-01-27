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
        $data = $data->map(fn (mixed $item) => $item instanceof Closure
            ? $item($data->all())
            : $item);

        return $next($data);
    }
}
