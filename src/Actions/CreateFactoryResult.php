<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Actions;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Worksome\RequestFactories\Actions\CreateFactoryResultSteps\InvokeAfterCreatingHooks;
use Worksome\RequestFactories\Actions\CreateFactoryResultSteps\RecursiveStep;
use Worksome\RequestFactories\Actions\CreateFactoryResultSteps\RemoveWithout;
use Worksome\RequestFactories\Actions\CreateFactoryResultSteps\ResolveClosures;
use Worksome\RequestFactories\Actions\CreateFactoryResultSteps\ResolveModelFactories;
use Worksome\RequestFactories\Actions\CreateFactoryResultSteps\ResolveNestedRequestFactories;
use Worksome\RequestFactories\Contracts\Actions\CreatesFactoryResult;
use Worksome\RequestFactories\RequestFactory;
use Worksome\RequestFactories\Support\Result;

final readonly class CreateFactoryResult implements CreatesFactoryResult
{
    public function __invoke(RequestFactory $factory): Result
    {
        $data = $factory->getFactoryData();

        /** @var Collection<array-key, mixed> $result */
        $result = (new Pipeline())
            ->send(collect($data->getRequestedData()))
            ->through([
                RecursiveStep::using(new ResolveNestedRequestFactories($this)),
                RecursiveStep::using(new ResolveClosures()),
                new RemoveWithout($data->getWithout()),
                RecursiveStep::using(new ResolveModelFactories()),
                new InvokeAfterCreatingHooks($data->getAfterCreatingHooks()),
            ])
            ->thenReturn();

        return new Result($result->all());
    }
}
