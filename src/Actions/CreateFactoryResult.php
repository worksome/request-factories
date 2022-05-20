<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Actions;

use Illuminate\Routing\Pipeline;
use Illuminate\Support\Collection;
use Worksome\RequestFactories\Actions\CreateFactoryResultSteps\InvokeAfterCreatingHooks;
use Worksome\RequestFactories\Actions\CreateFactoryResultSteps\RemoveWithout;
use Worksome\RequestFactories\Actions\CreateFactoryResultSteps\ResolveClosures;
use Worksome\RequestFactories\Actions\CreateFactoryResultSteps\ResolveNestedFactories;
use Worksome\RequestFactories\Contracts\Actions\CreatesFactoryResult;
use Worksome\RequestFactories\RequestFactory;
use Worksome\RequestFactories\Support\Result;

final class CreateFactoryResult implements CreatesFactoryResult
{
    public function __invoke(RequestFactory $factory): Result
    {
        [$definition, $files, $attributes, $without, $afterCreatingHooks] = $factory->getDataNeededForCreatingResult();

        $requestedData = collect(array_merge($definition, $files, $attributes));

        /** @var Collection<mixed> $result */
        $result = (new Pipeline())
            ->send($requestedData)
            ->through([
                new ResolveNestedFactories($this),
                new ResolveClosures(),
                new RemoveWithout($without),
                new InvokeAfterCreatingHooks($afterCreatingHooks),
            ])
            ->thenReturn();

        return new Result($result->all());
    }
}
