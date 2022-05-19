<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Worksome\RequestFactories\Contracts\Finder;

final class MakeCommand extends GeneratorCommand
{
    public $signature = 'make:request-factory
                         {name : The name of the request or request factory.}';

    public $description = 'Generate a new FormRequest Factory.';

    public function __construct(Filesystem $files, private Finder $finder)
    {
        parent::__construct($files);
    }

    protected function getNameInput(): string
    {
        $name = parent::getNameInput();

        if (! class_exists($name)) {
            return $name;
        }

        if (! is_subclass_of($name, FormRequest::class)) {
            return $name;
        }

        return Str::of($name)
            ->after('App\\Http\\Requests\\')
            ->append('Factory')
            ->__toString();
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/request-factory.stub';
    }

    protected function getPath($name): string
    {
        $name = Str::after($name, $this->rootNamespace());

        return "{$this->finder->requestFactoriesLocation($name)}.php";
    }

    protected function rootNamespace(): string
    {
        return $this->finder->requestFactoriesNamespace();
    }
}
