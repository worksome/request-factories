<?php

namespace Worksome\RequestFactories;

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Http\FormRequest;
use InvalidArgumentException;
use ReflectionClass;

final class FactoryManager
{
    /**
     * @var array<class-string<FormRequest>, RequestFactory>
     */
    private array $fakes = [];

    /**
     * @var array<int, class-string<FormRequest>>
     */
    private array $requestsWithResolvers = [];

    public function __construct(private Container $container)
    {
    }

    /**
     * @param class-string<FormRequest> $request
     */
    public function fake(string $request, RequestFactory $factory): void
    {
        $this->fakes[$request] = $factory;

        if (in_array($request, $this->requestsWithResolvers)) {
            return;
        }

        $this->container->resolving(
            $request,
            fn(FormRequest $request) => $this->mergeFactoryIntoRequest($request)
        );

        $this->requestsWithResolvers[] = $request;
    }

    /**
     * @param class-string $request
     */
    public function hasFake(string $request): bool
    {
        return array_key_exists($request, $this->fakes);
    }

    /**
     * @param class-string $request
     */
    public function getFake(string $request): RequestFactory
    {
        if (!$this->hasFake($request)) {
            throw new InvalidArgumentException("[{$request}] was never faked.");
        }

        return $this->fakes[$request];
    }

    private function mergeFactoryIntoRequest(FormRequest $formRequest): void
    {
        $input = $this->getFake($formRequest::class)->create();

        /**
         * It would be nicer to use `mergeIfMissing`, but for the sake
         * of supporting earlier Laravel versions, we might as well
         * do this bit of custom logic instead.
         */
        foreach ($input->input() as $key => $value) {
            if ($formRequest->missing($key)) {
                $formRequest->merge([$key => $value]);
            }
        }

        foreach ($input->files() as $name => $file) {
            if ($formRequest->files->has($name)) {
                continue;
            }

            $formRequest->files->set($name, $file);
        }

        if ($input->hasFiles()) {
            $this->clearFileCache($formRequest);
        }
    }

    /**
     * Laravel caches files prior to our data injection taking place,
     * so if we have placed fake files in the request, we should
     * clear that cache to allow it to rebuild correctly.
     */
    private function clearFileCache(FormRequest $request): void
    {
        $mirror = new ReflectionClass($request);
        $convertedFiles = $mirror->getProperty('convertedFiles');
        $convertedFiles->setAccessible(true);
        $convertedFiles->setValue($request, null);
    }
}
