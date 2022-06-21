<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Worksome\RequestFactories\Contracts\Actions\UndotsArrayKeys;

final class UndotArrayKeys implements UndotsArrayKeys
{
    private string $placeholder;

    public function __construct()
    {
        $this->placeholder = Str::random();
    }

    public function __invoke(array $array): array
    {
        $array = $this->replaceEscapedDotsWithPlaceholder($array);
        $array = Arr::undot($array);

        return $this->recursivelyReplacePlaceholderWithDots($array);
    }

    private function recursivelyReplacePlaceholderWithDots(array $array): array
    {
        $array = $this->replacePlaceholderWithDots($array);

        foreach ($array as $key => $item) {
            if (is_array($item)) {
                $array[$key] = $this->recursivelyReplacePlaceholderWithDots($item);
            }
        }

        return $array;
    }

    private function replaceEscapedDotsWithPlaceholder(array $array): array
    {
        $keys = array_map(fn ($key) => str_replace('\.', $this->placeholder, $key), array_keys($array));

        return array_combine($keys, $array);
    }

    private function replacePlaceholderWithDots(array $array): array
    {
        $keys = array_map(fn ($key) => str_replace($this->placeholder, '.', (string) $key), array_keys($array));

        return array_combine($keys, $array);
    }
}
