<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Support;

use Illuminate\Support\Str;
use Worksome\RequestFactories\Contracts\Finder;

final class CustomisableFinder implements Finder
{
    public function __construct(private string $path, private string $namespace)
    {
    }

    public function requestFactoriesLocation(string $name = ''): string
    {
        $path = Str::of($name)->start('/')->prepend($this->path)->__toString();

        return $this->withCorrectSeparator($path);
    }

    public function requestFactoriesNamespace(): string
    {
        return $this->namespace;
    }

    private function withCorrectSeparator(string $path): string
    {
        return Str::replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}
