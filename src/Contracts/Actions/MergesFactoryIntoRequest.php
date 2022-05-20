<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Contracts\Actions;

use Illuminate\Http\Request;
use Worksome\RequestFactories\RequestFactory;

interface MergesFactoryIntoRequest
{
    public function __invoke(RequestFactory $factory, Request $request): Request;
}
