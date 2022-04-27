<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Tests\Doubles;

use Illuminate\Support\Str;
use Worksome\RequestFactories\Contracts\Finder as FinderContract;
use Worksome\RequestFactories\Support\Finder;

final class TestFinder implements FinderContract
{
    public function __construct(private Finder $baseFinder)
    {
    }

    public function requestFactoriesLocation(string $name = ''): string
    {
        $path = Str::of($name)->start('/')->prepend('tests/RequestFactories')->__toString();

        return $this->withCorrectSeparator($this->basePath($path));
    }

    public function requestFactoriesNamespace(): string
    {
        return $this->baseFinder->requestFactoriesNamespace();
    }

    private function withCorrectSeparator(string $path): string
    {
        return Str::replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    private function basePath(string $path = ''): string
    {
        $path = Str::of($path)
            ->start('/')
            ->prepend(__DIR__ . '/../tmp')
            ->__toString();

        return $this->withCorrectSeparator($path);
    }
}
