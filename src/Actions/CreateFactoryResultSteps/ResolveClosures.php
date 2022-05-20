<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Actions\CreateFactoryResultSteps;

use Closure;
use Illuminate\Support\Collection;

final class ResolveClosures
{
    public function handle(Collection $data, Closure $next): mixed
    {
        $data = $data->map(fn (mixed $item) => $item instanceof Closure
            ? $item($data->all())
            : $item);

        return $next($data);
    }
}
