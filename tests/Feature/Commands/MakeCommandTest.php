<?php

declare(strict_types=1);

use Worksome\RequestFactories\Commands\MakeCommand;
use Illuminate\Support\Facades\File;

use function Pest\Laravel\artisan;

afterEach(function () {
    File::deleteDirectories(tmp());
});

it('generates a new factory', function ($name, $namespace) {
    artisan(MakeCommand::class, ['name' => $name])->assertSuccessful()->execute();

    $filePath = tmp("tests/RequestFactories/{$name}.php");
    $basename = class_basename($name);

    expect($filePath)
        ->toBeReadableFile()->toBeWritableFile()
        ->and(file_get_contents($filePath))
        ->toContain("namespace {$namespace};")
        ->toContain("class {$basename} extends RequestFactory");
})->with([
    ['name' => 'SignupRequestFactory', 'namespace' => finder()->requestFactoriesNamespace()],
    ['name' => 'SubDirectory/SignupRequestFactory', 'namespace' => finder()->requestFactoriesNamespace() . '\\SubDirectory'],
]);

it('can generate a factory name if a FormRequest FQCN is given as the name', function ($formRequest, $fileName) {
    artisan(MakeCommand::class, ['name' => $formRequest])->execute();

    expect(tmp("tests/RequestFactories/{$fileName}"))
        ->toBeReadableFile()->toBeWritableFile();
})->with([
    ['formRequest' => 'App\\Http\\Requests\\ExampleFormRequest', 'fileName' => 'ExampleFormRequestFactory.php'],
    ['formRequest' => 'App/Http/Requests/ExampleFormRequest', 'fileName' => 'ExampleFormRequestFactory.php'],
    ['formRequest' => 'App\\Http\\Requests\\Nested\\NestedExampleFormRequest', 'fileName' => 'Nested/NestedExampleFormRequestFactory.php'],
    ['formRequest' => 'App/Http/Requests/Nested/NestedExampleFormRequest', 'fileName' => 'Nested/NestedExampleFormRequestFactory.php'],
]);
