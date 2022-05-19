<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Middleware;

use Closure;
use Illuminate\Http\Request;
use Worksome\RequestFactories\FactoryManager;

class InjectFakeDataMiddleware
{
    public function __construct(private FactoryManager $factoryManager)
    {
    }

    public function handle(Request $request, Closure $next): mixed
    {
        if ($this->factoryManager->hasFake()) {
            $this->factoryManager->mergeFactoryIntoRequest($request);
        }

        return $next($request);
    }
}
