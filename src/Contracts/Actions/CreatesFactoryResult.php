<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Contracts\Actions;

use Worksome\RequestFactories\RequestFactory;
use Worksome\RequestFactories\Support\Result;

interface CreatesFactoryResult
{
    /** @return Result<array-key, mixed> */
    public function __invoke(RequestFactory $factory): Result;
}
