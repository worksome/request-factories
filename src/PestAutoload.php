<?php

use Pest\Plugin;
use Worksome\RequestFactories\Concerns\Pest\FakesRequests;

if (class_exists(Plugin::class)) {
    Plugin::uses(FakesRequests::class);
}
