<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Actions;

use Illuminate\Http\Request;
use ReflectionClass;
use Worksome\RequestFactories\Contracts\Actions\CreatesFactoryResult;
use Worksome\RequestFactories\Contracts\Actions\MergesFactoryIntoRequest;
use Worksome\RequestFactories\RequestFactory;

final readonly class MergeFactoryIntoRequest implements MergesFactoryIntoRequest
{
    public function __construct(private CreatesFactoryResult $createFactoryResult)
    {
    }

    public function __invoke(RequestFactory $factory, Request $request): Request
    {
        $input = ($this->createFactoryResult)($factory);

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

        return $request;
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
