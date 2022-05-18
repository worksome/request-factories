<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Support;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Worksome\RequestFactories\Contracts\Finder;
use Worksome\RequestFactories\Exceptions\CouldNotLocateFormRequestException;
use Worksome\RequestFactories\Exceptions\CouldNotLocateRequestFactoryException;
use Worksome\RequestFactories\RequestFactory;

final class Map
{
    public function __construct(private Finder $finder)
    {
    }

    /**
     * @param class-string<FormRequest> $formRequest
     * @return class-string<RequestFactory>
     * @throws CouldNotLocateRequestFactoryException
     */
    public function formRequestToFactory(string $formRequest): string
    {
        if (property_exists($formRequest, 'factory')) {
            return $formRequest::$factory;
        }

        $requestPartialFQCN = Str::after($formRequest, "App\\Http\\Requests\\");
        $factoryNamespace = $this->finder->requestFactoriesNamespace();
        $guessedFactoryFQCN = $factoryNamespace . '\\' . $requestPartialFQCN . 'Factory';

        if (class_exists($guessedFactoryFQCN)) {
            return $guessedFactoryFQCN;
        }

        throw CouldNotLocateRequestFactoryException::make($formRequest, $guessedFactoryFQCN);
    }

    /**
     * @param class-string<RequestFactory> $factory
     * @return class-string<FormRequest>
     * @throws CouldNotLocateFormRequestException
     */
    public function factoryToFormRequest(string $factory): string
    {
        if (property_exists($factory, 'formRequest')) {
            return $factory::$formRequest;
        }

        $factoryPartialFQCN = Str::of($factory)
            ->after($this->finder->requestFactoriesNamespace() . '\\')
            ->beforeLast('Factory')
            ->__toString();

        $guessedFormRequestFQCN =  'App\\Http\\Requests\\' . $factoryPartialFQCN;

        if (class_exists($guessedFormRequestFQCN)) {
            return $guessedFormRequestFQCN;
        }

        throw CouldNotLocateFormRequestException::make($factory, $guessedFormRequestFQCN);
    }
}
