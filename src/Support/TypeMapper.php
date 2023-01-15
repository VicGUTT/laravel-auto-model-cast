<?php

declare(strict_types=1);

namespace VicGutt\AutoModelCast\Support;

use VicGutt\InspectDb\Entities\Column;
use VicGutt\AutoModelCast\Enums\CastTypeEnum;
use VicGutt\InspectDb\Types\DoctrineTypeEnum;

class TypeMapper
{
    public function __construct(protected readonly ?array $typesMap = null)
    {
    }

    /**
     * Default types map. Here the Doctrine column types are
     * simply mapped to their homologous Laravel cast type.
     */
    public static function defaults(): array
    {
        return [
            DoctrineTypeEnum::ASCII_STRING->value         => CastTypeEnum::STRING->value,
            DoctrineTypeEnum::BIGINT->value               => CastTypeEnum::STRING->value,
            DoctrineTypeEnum::BINARY->value               => CastTypeEnum::STRING->value,
            DoctrineTypeEnum::BLOB->value                 => CastTypeEnum::STRING->value,
            DoctrineTypeEnum::BOOLEAN->value              => CastTypeEnum::BOOL->value,
            DoctrineTypeEnum::DATE->value                 => CastTypeEnum::DATE->value,
            DoctrineTypeEnum::DATE_IMMUTABLE->value       => CastTypeEnum::IMMUTABLE_DATE->value,
            DoctrineTypeEnum::DATEINTERVAL->value         => CastTypeEnum::DATE->value,
            DoctrineTypeEnum::DATETIME->value             => CastTypeEnum::DATETIME->value,
            DoctrineTypeEnum::DATETIME_IMMUTABLE->value   => CastTypeEnum::IMMUTABLE_DATETIME->value,
            DoctrineTypeEnum::DATETIMETZ->value           => CastTypeEnum::DATETIME->value,
            DoctrineTypeEnum::DATETIMETZ_IMMUTABLE->value => CastTypeEnum::IMMUTABLE_DATETIME->value,
            DoctrineTypeEnum::DECIMAL->value              => CastTypeEnum::DECIMAL->value,
            DoctrineTypeEnum::FLOAT->value                => CastTypeEnum::FLOAT->value,
            DoctrineTypeEnum::GUID->value                 => CastTypeEnum::STRING->value,
            DoctrineTypeEnum::INTEGER->value              => CastTypeEnum::INT->value,
            DoctrineTypeEnum::JSON->value                 => CastTypeEnum::JSON->value,
            DoctrineTypeEnum::SIMPLE_ARRAY->value         => CastTypeEnum::ARRAY->value,
            DoctrineTypeEnum::SMALLINT->value             => CastTypeEnum::INT->value,
            DoctrineTypeEnum::STRING->value               => CastTypeEnum::STRING->value,
            DoctrineTypeEnum::TEXT->value                 => CastTypeEnum::STRING->value,
            DoctrineTypeEnum::TIME->value                 => CastTypeEnum::STRING->value,
            DoctrineTypeEnum::TIME_IMMUTABLE->value       => CastTypeEnum::STRING->value,
            DoctrineTypeEnum::UNKNOWN->value              => null,
        ];
    }

    /**
     * Opinionated types map. Here we extend the defaults while
     * providing a sensible opinionated mapping.
     * Essentially casting any date related types to be immutable.
     */
    public static function opinionated(): array
    {
        return [
            ...self::defaults(),
            DoctrineTypeEnum::BIGINT->value       => CastTypeEnum::INT->value,
            DoctrineTypeEnum::DATE->value         => CastTypeEnum::IMMUTABLE_DATE->value,
            DoctrineTypeEnum::DATEINTERVAL->value => CastTypeEnum::IMMUTABLE_DATE->value,
            DoctrineTypeEnum::DATETIME->value     => CastTypeEnum::IMMUTABLE_DATETIME->value,
            DoctrineTypeEnum::DATETIMETZ->value   => CastTypeEnum::IMMUTABLE_DATETIME->value,
            DoctrineTypeEnum::JSON->value         => CastTypeEnum::AS_ARRAY_OBJECT_CLASS->value,
        ];
    }

    public static function new(?array $typesMap = null): self
    {
        return new self($typesMap);
    }

    public function handle(Column $column, DoctrineTypeEnum $type): ?string
    {
        return match ($type) {
            DoctrineTypeEnum::ASCII_STRING => $this->handleAsciiString($column, $type),
            DoctrineTypeEnum::BIGINT => $this->handleBigint($column, $type),
            DoctrineTypeEnum::BINARY => $this->handleBinary($column, $type),
            DoctrineTypeEnum::BLOB => $this->handleBlob($column, $type),
            DoctrineTypeEnum::BOOLEAN => $this->handleBoolean($column, $type),
            DoctrineTypeEnum::DATE => $this->handleDate($column, $type),
            DoctrineTypeEnum::DATE_IMMUTABLE => $this->handleDateImmutable($column, $type),
            DoctrineTypeEnum::DATEINTERVAL => $this->handleDateInterval($column, $type),
            DoctrineTypeEnum::DATETIME => $this->handleDatetime($column, $type),
            DoctrineTypeEnum::DATETIME_IMMUTABLE => $this->handleDatetimeImmutable($column, $type),
            DoctrineTypeEnum::DATETIMETZ => $this->handleDatetimetz($column, $type),
            DoctrineTypeEnum::DATETIMETZ_IMMUTABLE => $this->handleDatetimetzImmutable($column, $type),
            DoctrineTypeEnum::DECIMAL => $this->handleDecimal($column, $type),
            DoctrineTypeEnum::FLOAT => $this->handleFloat($column, $type),
            DoctrineTypeEnum::GUID => $this->handleGuid($column, $type),
            DoctrineTypeEnum::INTEGER => $this->handleInteger($column, $type),
            DoctrineTypeEnum::JSON => $this->handleJson($column, $type),
            DoctrineTypeEnum::SIMPLE_ARRAY => $this->handleSimpleArray($column, $type),
            DoctrineTypeEnum::SMALLINT => $this->handleSmallint($column, $type),
            DoctrineTypeEnum::STRING => $this->handleString($column, $type),
            DoctrineTypeEnum::TEXT => $this->handleText($column, $type),
            DoctrineTypeEnum::TIME => $this->handleTime($column, $type),
            DoctrineTypeEnum::TIME_IMMUTABLE => $this->handleTimeImmutable($column, $type),
            DoctrineTypeEnum::UNKNOWN => $this->handleUnknowType($column, $type),
        };
    }

    protected function handleAsciiString(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleBigint(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleBinary(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleBlob(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleBoolean(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleDate(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleDateImmutable(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleDateInterval(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleDatetime(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleDatetimeImmutable(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleDatetimetz(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleDatetimetzImmutable(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleDecimal(Column $column, DoctrineTypeEnum $type): ?string
    {
        $value = $this->getCastFromDoctrineType($type);

        if (!$value || str_contains($value, ':')) {
            return $value;
        }

        return "{$value}:{$column->precision}";
    }

    protected function handleFloat(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleGuid(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleInteger(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleJson(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleSimpleArray(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleSmallint(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleString(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleText(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleTime(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleTimeImmutable(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function handleUnknowType(Column $column, DoctrineTypeEnum $type): ?string
    {
        return $this->getCastFromDoctrineType($type);
    }

    protected function getCastFromDoctrineType(DoctrineTypeEnum $type): ?string
    {
        return $this->getTypesMap()[$type->value] ?? null;
    }

    protected function getTypesMap(): array
    {
        return $this->typesMap ?: self::defaults();
    }
}
