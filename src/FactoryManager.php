<?php

declare(strict_types=1);

namespace Worksome\RequestFactories;

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use InvalidArgumentException;
use ReflectionClass;

final class FactoryManager
{
    /**
     * @var array<class-string<Request>, RequestFactory>
     */
    private array $fakes = [];

    /**
     * @var array<int, class-string<Request>>
     */
    private array $requestsWithResolvers = [];

    public function __construct(private Container $container)
    {
    }

    /**
     * @param class-string<Request> $request
     */
    public function fake(string $request, RequestFactory $factory): void
    {
        $this->fakes[$request] = $factory;

        if (in_array($request, $this->requestsWithResolvers)) {
            return;
        }

        if (is_subclass_of($request, FormRequest::class)) {
            $this->container->resolving(
                $request,
                fn(Request $request) => $this->mergeFactoryIntoRequest($request)
            );
        }

        $this->requestsWithResolvers[] = $request;
    }

    /**
     * @param class-string<Request> $request
     */
    public function hasFake(string $request): bool
    {
        return array_key_exists($request, $this->fakes);
    }

    public function hasGenericFake(): bool
    {
        return array_key_exists(Request::class, $this->fakes);
    }

    public function mergeFactoryIntoRequest(Request $request): void
    {
        $input = $this->getFake($request::class)->create();

        /**
         * It would be nicer to use `mergeIfMissing`, but for the sake
         * of supporting earlier Laravel versions, we might as well
         * do this bit of custom logic instead.
         */
        foreach ($input->input() as $key => $value) {
            if ($request->missing($key)) {
                $request->merge([$key => $value]);
            }
        }

        foreach ($input->files() as $name => $file) {
            if ($request->files->has($name)) {
                continue;
            }

            $request->files->set($name, $file);
        }

        if ($input->hasFiles()) {
            $this->clearFileCache($request);
        }
    }

    /**
     * @param class-string<Request> $request
     */
    private function getFake(string $request): RequestFactory
    {
        if (!$this->hasFake($request)) {
            throw new InvalidArgumentException("[{$request}] was never faked.");
        }

        return $this->fakes[$request];
    }

    /**
     * Laravel caches files prior to our data injection taking place,
     * so if we have placed fake files in the request, we should
     * clear that cache to allow it to rebuild correctly.
     */
    private function clearFileCache(Request $request): void
    {
        $mirror = new ReflectionClass($request);
        $convertedFiles = $mirror->getProperty('convertedFiles');
        $convertedFiles->setAccessible(true);
        $convertedFiles->setValue($request, null);
    }
}
