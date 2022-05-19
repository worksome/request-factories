<?php

use App\Http\Requests\DeclaredFactoryPropertyFormRequest;
use App\Http\Requests\ExampleFormRequest;
use App\Http\Requests\RequestFactoryNotFoundFormRequest;
use Illuminate\Http\Request;
use Worksome\RequestFactories\Exceptions\CouldNotLocateRequestFactoryException;
use Worksome\RequestFactories\Support\Map;
use Worksome\RequestFactories\Tests\Doubles\Factories\DeclaredFormRequestPropertyFactory;
use Worksome\RequestFactories\Tests\Doubles\Factories\ExampleFormRequestFactory;
use Worksome\RequestFactories\Tests\Doubles\Factories\RequestNotFoundFormRequestFactory;

it('can automatically locate a RequestFactory from a FormRequest', function () {
    $map = new Map(finder());

    expect($map->formRequestToFactory(ExampleFormRequest::class))
        ->toBe(ExampleFormRequestFactory::class);
});

it('can automatically locate a FormRequest from a RequestFactory', function () {
    $map = new Map(finder());

    expect($map->factoryToFormRequest(ExampleFormRequestFactory::class))
        ->toBe(ExampleFormRequest::class);
});

it('returns a standard Request if a FormRequest cannot be located', function () {
    $map = new Map(finder());

    expect($map->factoryToFormRequest(RequestNotFoundFormRequestFactory::class))
        ->toBe(Request::class);
});

it('throws an exception if a RequestFactory cannot be located', function () {
    $map = new Map(finder());

    $map->formRequestToFactory(RequestFactoryNotFoundFormRequest::class);
})->throws(CouldNotLocateRequestFactoryException::class);

it('can find a FormRequest from a RequestFactory if the RequestFactory contains a formRequest property', function () {
    $map = new Map(finder());

    expect($map->factoryToFormRequest(DeclaredFormRequestPropertyFactory::class))
        ->toBe(ExampleFormRequest::class);
});

it('can find a RequestFactory from a FormRequest if the RequestFactory contains a factory property', function () {
    $map = new Map(finder());

    expect($map->formRequestToFactory(DeclaredFactoryPropertyFormRequest::class))
        ->toBe(ExampleFormRequestFactory::class);
});
