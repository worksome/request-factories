<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Support;

use Illuminate\Support\Str;
use Worksome\RequestFactories\Contracts\Finder;

final class ConfigBasedFinder implements Finder
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(private array $config)
    {
    }

    public function requestFactoriesLocation(string $name = ''): string
    {
        $path = Str::of($name)->start('/')->prepend($this->getName())->__toString();

        return $this->withCorrectSeparator($path);
    }

    public function requestFactoriesNamespace(): string
    {
        return strval($this->config['namespace'] ?? 'Tests\\RequestFactories');
    }

    private function getName(): string
    {
        return strval($this->config['path'] ?? 'tests/RequestFactories');
    }

    private function withCorrectSeparator(string $path): string
    {
        return Str::replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}
