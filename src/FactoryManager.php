<?php

declare(strict_types=1);

namespace Worksome\RequestFactories;

use Illuminate\Http\Request;
use InvalidArgumentException;
use ReflectionClass;

final class FactoryManager
{
    private RequestFactory|null $fake = null;

    public function fake(RequestFactory $factory): void
    {
        $this->fake = $factory;
    }

    public function mergeFactoryIntoRequest(Request $request): void
    {
        if ($this->fake === null) {
            return;
        }

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
        if ($this->fake === null) {
            throw new InvalidArgumentException("[{$request}] was never faked.");
        }

        return $this->fake;
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
