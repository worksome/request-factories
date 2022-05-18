<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Exceptions;

use Exception;

final class CouldNotLocateRequestFactoryException extends Exception
{
    public static function make(string $formRequestFQCN, string $guessedFactoryFQCN): self
    {
        return new self("
        Could not find [{$guessedFactoryFQCN}].
        Please specify a relevant RequestFactory FQCN using a public static \$factory property on
        [{$formRequestFQCN}].
        ");
    }
}
