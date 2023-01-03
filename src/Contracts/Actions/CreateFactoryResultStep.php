<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Contracts\Actions;

use Closure;
use Illuminate\Support\Collection;

interface CreateFactoryResultStep
{
    /**
     * @param Collection<mixed>                             $data
     * @param Closure(Collection<mixed>): Collection<mixed> $next
     *
     * @return Collection<mixed>
     */
    public function handle(Collection $data, Closure $next): Collection;
}
