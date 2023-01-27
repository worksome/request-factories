<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Listeners;

use Illuminate\Routing\Events\RouteMatched;
use Worksome\RequestFactories\Contracts\Actions\MergesFactoryIntoRequest;
use Worksome\RequestFactories\FactoryManager;

final readonly class MergeFactoryIntoRequest
{
    public function __construct(
        private FactoryManager $factoryManager,
        private MergesFactoryIntoRequest $mergeFactoryIntoRequest,
    ) {
    }

    public function handle(RouteMatched $event): void
    {
        if ($this->factoryManager->hasFake()) {
            ($this->mergeFactoryIntoRequest)($this->factoryManager->getFake(), $event->request);
        }
    }
}
