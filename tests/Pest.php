<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use Worksome\RequestFactories\Actions\CreateFactoryResult;
use Worksome\RequestFactories\RequestFactory;
use Worksome\RequestFactories\Support\CustomisableFinder;
use Worksome\RequestFactories\Support\Result;
use Worksome\RequestFactories\Tests\TestCase;

uses(TestCase::class)->in('Feature');

function tmp(string $path = ''): string
{
    return Str::of($path)
        ->start('/')
        ->prepend(__DIR__ . '/tmp')
        ->__toString();
}

function finder(): CustomisableFinder
{
    return new CustomisableFinder(
        __DIR__ . '/tmp/tests/RequestFactories',
        'Worksome\\RequestFactories\\Tests\\Doubles\\Factories',
    );
}

function creator(RequestFactory $factory): Result
{
    return (new CreateFactoryResult())($factory);
}
