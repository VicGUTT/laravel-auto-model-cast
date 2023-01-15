<?php

declare(strict_types=1);

namespace VicGutt\AutoModelCast\Exceptions;

use VicGutt\InspectDb\Entities\Column;
use VicGutt\AutoModelCast\Support\TypeMapper;
use VicGutt\InspectDb\Types\DoctrineTypeEnum;
use VicGutt\AutoModelCast\Support\Casters\BaseCaster;

class BaseCasterException extends AutoModelCastException
{
    public static function invalidCaster(string $provided): self
    {
        $baseCaster = BaseCaster::class;

        return new self("The provided caster `{$provided}` is invalid, please ensure it inherits the `{$baseCaster}` class.");
    }

    public static function invalidTypeMapper(): self
    {
        $class = TypeMapper::class;

        return new self("The provided type mapper is invalid, please ensure it inherits the `{$class}` class.");
    }

    public static function invalidCastType(Column $column, DoctrineTypeEnum $type, string $castType): self
    {
        return new self(
            "An invalid cast type `{$castType}` was given for the column `{$column->name}` of type `{$type->value}`."
            . " Valid cast types are any value that can be provided to a model's \"cast\" property.",
        );
    }
}
