<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Exceptions;

use Exception;

final class CouldNotLocateFormRequestException extends Exception
{
    public static function make(string $factoryFQCN, string $guessedFormRequestFQCN): self
    {
        return new self("
        Could not find [{$guessedFormRequestFQCN}].
        Please specify a relevant FormRequest FQCN using a public static \$formRequest property on
        [{$factoryFQCN}].
        ");
    }
}
