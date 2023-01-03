<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Contracts\Actions;

/**
 * @template TValue
 */
interface UndotsArrayKeys
{
    /**
     * @param array<TValue> $array
     *
     * @return array<TValue>
     */
    public function __invoke(array $array): array;
}
