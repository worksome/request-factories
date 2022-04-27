<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Contracts;

interface Finder
{
    public function requestFactoriesLocation(string $name = ''): string;

    public function requestFactoriesNamespace(): string;
}
