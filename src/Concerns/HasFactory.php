<?php

declare(strict_types=1);

namespace Worksome\RequestFactories\Concerns;

use Closure;
use Worksome\RequestFactories\RequestFactory;

/**
 * @deprecated These methods are now made available via macros, allowing you to safely remove this trait from your FormRequests.
 *
 * @method static RequestFactory factory()
 * @method static void           fake(array|Closure $attributes = [])
 */
trait HasFactory
{
}
