<?php

declare(strict_types=1);

namespace Worksome\RequestFactories;

use BadMethodCallException;

final class FactoryManager
{
    private RequestFactory|null $fake = null;

    public function fake(RequestFactory $factory): void
    {
        $this->fake = $factory;
    }

    public function hasFake(): bool
    {
        return $this->fake !== null;
    }

    public function getFake(): RequestFactory
    {
        if ($this->fake === null) {
            throw new BadMethodCallException('No fake has been configured for the request.');
        }

        return $this->fake;
    }
}
