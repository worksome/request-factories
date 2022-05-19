<?php

declare(strict_types=1);

namespace Worksome\RequestFactories;

use Worksome\RequestFactories\Contracts\Finder;
use Worksome\RequestFactories\Support\CustomisableFinder;

final class RequestFactories
{
    public static function location(string $path, string $namespace): void
    {
        /** @phpstan-ignore-next-line  */
        app()->singleton(Finder::class, fn () => new CustomisableFinder($path, $namespace));
    }
}
