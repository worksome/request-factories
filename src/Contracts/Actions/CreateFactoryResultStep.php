<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Contracts\Actions;

use Closure;
use Illuminate\Support\Collection;

interface CreateFactoryResultStep
{
    /**
     * @param Collection<array-key, mixed>                                        $data
     * @param Closure(Collection<array-key, mixed>): Collection<array-key, mixed> $next
     *
     * @return Collection<array-key, mixed>
     */
    public function handle(Collection $data, Closure $next): Collection;
}
