<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Support;

use Illuminate\Support\Str;
use Worksome\RequestFactories\Contracts\Finder as FinderContract;

/**
 * @internal
 */
final class Finder implements FinderContract
{
    public function requestFactoriesLocation(string $name = ''): string
    {
        $path = Str::of($name)->start('/')->prepend('tests/RequestFactories')->__toString();

        return $this->withCorrectSeparator($this->basePath($path));
    }

    public function requestFactoriesNamespace(): string
    {
        return 'Tests\\RequestFactories';
    }

    private function basePath(string $path = ''): string
    {
        return base_path($path);
    }

    private function withCorrectSeparator(string $path): string
    {
        return Str::replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}
