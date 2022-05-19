<?php

use App\Http\Requests\DeclaredFactoryPropertyFormRequest;
use App\Http\Requests\ExampleFormRequest;
use App\Http\Requests\RequestFactoryNotFoundFormRequest;
use Worksome\RequestFactories\Exceptions\CouldNotLocateRequestFactoryException;
use Worksome\RequestFactories\Support\Map;
use Worksome\RequestFactories\Tests\Doubles\Factories\ExampleFormRequestFactory;

it('can automatically locate a RequestFactory from a FormRequest', function () {
    $map = new Map(finder());

    expect($map->formRequestToFactory(ExampleFormRequest::class))
        ->toBe(ExampleFormRequestFactory::class);
});

it('throws an exception if a RequestFactory cannot be located', function () {
    $map = new Map(finder());

    $map->formRequestToFactory(RequestFactoryNotFoundFormRequest::class);
})->throws(CouldNotLocateRequestFactoryException::class);

it('can find a RequestFactory from a FormRequest if the RequestFactory contains a factory property', function () {
    $map = new Map(finder());

    expect($map->formRequestToFactory(DeclaredFactoryPropertyFormRequest::class))
        ->toBe(ExampleFormRequestFactory::class);
});
