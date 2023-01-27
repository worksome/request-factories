<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Support;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Worksome\RequestFactories\Contracts\Finder;
use Worksome\RequestFactories\Exceptions\CouldNotLocateRequestFactoryException;
use Worksome\RequestFactories\RequestFactory;

final readonly class Map
{
    public function __construct(private Finder $finder)
    {
    }

    /**
     * @param class-string<FormRequest> $formRequest
     *
     * @return class-string<RequestFactory>
     *
     * @throws CouldNotLocateRequestFactoryException
     */
    public function formRequestToFactory(string $formRequest): string
    {
        if (
            property_exists($formRequest, 'factory')
            && is_string($formRequest::$factory)
            && is_subclass_of($formRequest::$factory, RequestFactory::class)
        ) {
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
}
