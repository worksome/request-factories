<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Actions\CreateFactoryResultSteps;

use Closure;
use Illuminate\Support\Collection;
use Worksome\RequestFactories\Contracts\Actions\CreateFactoryResultStep;

final class InvokeAfterCreatingHooks implements CreateFactoryResultStep
{
    public function __construct(private array $hooks)
    {
    }

    public function handle(Collection $data, Closure $next): Collection
    {
        $data = collect($this->hooks)->reduce(
            fn ($latestAttributes, Closure $closure) => $closure($latestAttributes) ?? $latestAttributes,
            $data->all(),
        );

        // @phpstan-ignore-next-line
        return $next(collect($data));
    }
}
