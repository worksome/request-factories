<?php

namespace Worksome\RequestFactories;

use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Http\FormRequest;
use InvalidArgumentException;
use ReflectionClass;

final class FactoryManager
{
    /**
     * @var array<string, RequestFactory>
     */
    private array $fakes = [];

    public function __construct(private Container $container)
    {
    }

    /**
     * @param class-string $request
     */
    public function fake(string $request, RequestFactory $factory): void
    {
        $this->fakes[$request] = $factory;

        $this->container->resolving($request, function (FormRequest $request) {
            $input = $this->getFake($request::class)->create();

            $request->mergeIfMissing($input->input());

            foreach ($input->files() as $name => $file) {
                if ($request->files->has($name)) {
                    continue;
                }

                $request->files->set($name, $file);
            }

            if ($input->hasFiles()) {
                $this->clearFileCache($request);
            }
        });
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
       if (! $this->hasFake($request)) {
           throw new InvalidArgumentException("[{$request}] was never faked.");
       }

       return $this->fakes[$request];
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
