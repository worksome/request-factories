<?php

declare(strict_types=1);

use Worksome\RequestFactories\Actions\UndotArrayKeys;

it('can undot an array key', function ($givenArray, $resultingArray) {
    $action = new UndotArrayKeys();

    $result = $action($givenArray);

    expect($result)->toBe($resultingArray);
})->with([
    [
        ['foo.bar' => 'baz'],
        ['foo' => ['bar' => 'baz']]
    ],
    [
        ['foo.bar.baz' => 'boom'],
        ['foo' => ['bar' => ['baz' => 'boom']]],
    ],
    [
        ['foo.bar' => 'baz', 'luke' => 'downing'],
        ['foo' => ['bar' => 'baz'], 'luke' => 'downing'],
    ]
]);

it('can escape dots with the \\ character', function ($givenArray, $resultingArray) {
    $action = new UndotArrayKeys();

    $result = $action($givenArray);

    expect($result)->toBe($resultingArray);
})->with([
    [
        ['foo\.bar' => 'baz'],
        ['foo.bar' => 'baz']
    ],
    [
        ['foo\.bar\.baz' => 'boom'],
        ['foo.bar.baz' => 'boom'],
    ],
    [
        ['foo.bar\.baz' => 'boom'],
        ['foo' => ['bar.baz' => 'boom']],
    ]
]);
