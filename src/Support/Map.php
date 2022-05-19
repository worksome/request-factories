<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Support;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Worksome\RequestFactories\Contracts\Finder;
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
        if (property_exists($formRequest, 'factory') && is_string($formRequest::$factory) && is_subclass_of($formRequest::$factory, RequestFactory::class)) {
            return $formRequest::$factory;
        }

        $requestPartialFQCN = Str::after($formRequest, "App\\Http\\Requests\\");
        $factoryNamespace = $this->finder->requestFactoriesNamespace();
        $guessedFactoryFQCN = $factoryNamespace . '\\' . $requestPartialFQCN . 'Factory';

        if (class_exists($guessedFactoryFQCN) && is_subclass_of($guessedFactoryFQCN, RequestFactory::class)) {
            return $guessedFactoryFQCN;
        }

        throw CouldNotLocateRequestFactoryException::make($formRequest, $guessedFactoryFQCN);
    }

    /**
     * @param class-string<RequestFactory> $factory
     * @return class-string<Request>
     */
    public function factoryToFormRequest(string $factory): string
    {
        if (property_exists($factory, 'formRequest') && is_string($factory::$formRequest) && is_subclass_of($factory::$formRequest, FormRequest::class)) {
            return $factory::$formRequest;
        }

        $factoryPartialFQCN = Str::of($factory)
            ->after($this->finder->requestFactoriesNamespace() . '\\')
            ->beforeLast('Factory')
            ->__toString();

        $guessedFormRequestFQCN =  'App\\Http\\Requests\\' . $factoryPartialFQCN;

        if (class_exists($guessedFormRequestFQCN) && is_subclass_of($guessedFormRequestFQCN, FormRequest::class)) {
            return $guessedFormRequestFQCN;
        }

        return Request::class;
    }
}
