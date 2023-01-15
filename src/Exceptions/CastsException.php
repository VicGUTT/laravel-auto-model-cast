<?php

declare(strict_types=1);

namespace VicGutt\AutoModelCast\Exceptions;

use VicGutt\AutoModelCast\Support\Casts;

/**
 * @codeCoverageIgnore
 */
class CastsException extends AutoModelCastException
{
    public static function jsonUnencodable(Casts $instance): self
    {
        return new self('The casts instance `' . $instance::class . '` could not be json encoded.');
    }
}
