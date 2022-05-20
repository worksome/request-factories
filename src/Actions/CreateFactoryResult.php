<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Actions;

use Closure;
use Illuminate\Support\Arr;
use Worksome\RequestFactories\Contracts\Actions\CreatesFactoryResult;
use Worksome\RequestFactories\RequestFactory;
use Worksome\RequestFactories\Support\Result;

final class CreateFactoryResult implements CreatesFactoryResult
{
    public function __invoke(RequestFactory $factory): Result
    {
        $requestedData = collect(array_merge(
            $factory->definition(),
            $factory->files(),
            $factory->getAttributes(),
        ));

        /**
         * We now need to handle "special" objects in the $requestedData array, such
         * as other Request Factories and Closures. Closures should always resolve
         * after everything else, so we do this step in two separate stages.
         */
        $dataBeforeResolvingClosures = $requestedData->map(fn (mixed $data) => $this->handleData($data));

        $dataBeforeResolvingAfterCreatingHooks = $dataBeforeResolvingClosures
            ->map(fn (mixed $data) => $this->handleClosure($data, $dataBeforeResolvingClosures->all()))
            ->all();

        $dataAfterRemovingWithout = $this->unsetRequestedWithout(
            $dataBeforeResolvingAfterCreatingHooks,
            $factory->getWithout(),
        );

        return new Result($this->invokeAfterCreatingHooks(
            $dataAfterRemovingWithout,
            $factory->getAfterCreatingHooks(),
        ));
    }

    protected function handleData(mixed $data): mixed
    {
        if ($data instanceof RequestFactory) {
            $data = (new self())($data)->input();
        }

        return $data;
    }

    protected function handleClosure(mixed $data, array $attributes): mixed
    {
        if (! $data instanceof Closure) {
            return $data;
        }

        return $data($attributes);
    }

    /**
     * @param array<mixed> $attributes
     * @param array<Closure(array): array|void> $afterCreatingHooks
     * @return array<mixed>
     */
    protected function invokeAfterCreatingHooks(array $attributes, array $afterCreatingHooks): array
    {
        return collect($afterCreatingHooks)->reduce(
            // @phpstan-ignore-next-line
            fn ($latestAttributes, Closure $closure) => $closure($latestAttributes) ?? $latestAttributes,
            $attributes
        );
    }

    /**
     * @param array<mixed> $requestedData
     * @param array<int, string> $without
     * @return array<mixed>
     */
    private function unsetRequestedWithout(array &$requestedData, array $without): array
    {
        Arr::forget($requestedData, $without);

        return $requestedData;
    }
}
