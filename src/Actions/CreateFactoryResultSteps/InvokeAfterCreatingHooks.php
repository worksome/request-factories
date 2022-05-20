<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Actions\CreateFactoryResultSteps;

use Closure;
use Illuminate\Support\Collection;

final class InvokeAfterCreatingHooks
{
    public function __construct(private array $hooks)
    {
    }

    public function handle(Collection $data, Closure $next): mixed
    {
        $data = collect($this->hooks)->reduce(
            // @phpstan-ignore-next-line
            fn ($latestAttributes, Closure $closure) => $closure($latestAttributes) ?? $latestAttributes,
            $data->all(),
        );

        // @phpstan-ignore-next-line
        return $next(collect($data));
    }
}
