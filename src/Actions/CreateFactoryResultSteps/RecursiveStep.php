<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Actions\CreateFactoryResultSteps;

use Closure;
use Illuminate\Support\Collection;
use Worksome\RequestFactories\Contracts\Actions\CreateFactoryResultStep;

final readonly class RecursiveStep implements CreateFactoryResultStep
{
    private function __construct(private CreateFactoryResultStep $decoratedStep)
    {
    }

    public static function using(CreateFactoryResultStep $decoratedStep): self
    {
        return new self($decoratedStep);
    }

    public function handle(Collection $data, Closure $next): Collection
    {
        return $this->decoratedStep->handle(
            $data->map(fn(mixed $item) => $this->walk($item)),
            $next,
        );
    }

    private function walk(mixed $data): mixed
    {
        if (! is_array($data)) {
            return $data;
        }

        $data = Collection::make($data)->map(fn (mixed $item) => $this->walk($item));

        return $this
            ->decoratedStep
            ->handle($data, fn (Collection $alteredData) => $alteredData)
            ->all();
    }
}
