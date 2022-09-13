<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Actions\CreateFactoryResultSteps;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Worksome\RequestFactories\Contracts\Actions\CreateFactoryResultStep;

final class RemoveWithout implements CreateFactoryResultStep
{
    /**
     * @param array<int, string> $without
     */
    public function __construct(private array $without)
    {
    }

    /**
     * @param Collection<mixed> $data
     */
    public function handle(Collection $data, Closure $next): Collection
    {
        $data = $data->all();
        Arr::forget($data, $this->without);

        // @phpstan-ignore-next-line
        return $next(collect($data));
    }
}
