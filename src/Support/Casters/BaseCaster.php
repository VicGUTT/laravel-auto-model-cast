<?php

declare(strict_types=1);

namespace VicGutt\AutoModelCast\Support\Casters;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use VicGutt\InspectDb\Entities\Column;
use Illuminate\Database\Eloquent\Model;
use VicGutt\AutoModelCast\Enums\CastTypeEnum;
use VicGutt\AutoModelCast\Support\TypeMapper;
use VicGutt\InspectDb\Types\DoctrineTypeEnum;
use Illuminate\Contracts\Database\Eloquent\Castable;
use VicGutt\AutoModelCast\Exceptions\BaseCasterException;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;

abstract class BaseCaster
{
    public function __construct(protected readonly ?string $typeMapper = null, protected readonly ?array $typesMap = null)
    {
    }

    abstract public function handle(Column $column, Model $model): ?string;

    public function shouldBeHandled(Column $column, Model $model): bool
    {
        return true; // @codeCoverageIgnore
    }

    /**
     * @param Collection<array-key, Column> $columns
     */
    public function handleColumns(Collection $columns, Model $model): array
    {
        return $columns->reduce(function (array $acc, Column $column) use ($model): array {
            if (!$this->shouldBeHandled($column, $model)) {
                return $acc;
            }

            $handled = $this->handle($column, $model);

            if (!$handled) {
                return $acc;
            }

            $acc[$this->getColumnName($column)] = $handled;

            return $acc;
        }, []);
    }

    /**
     * @throws BaseCasterException
     */
    protected function handleColumn(Column $column): ?string
    {
        $type = $this->getColumnDoctrineTypeEnum($column);
        $castType = $this->determineCastType($column, $type);

        if ($castType && !$this->isValidCastType($castType)) {
            return $this->handleInvalidCastType($column, $type, $castType);
        }

        return $castType;
    }

    protected function getColumnName(Column $column): string
    {
        return $column->name;
    }

    protected function getColumnDoctrineTypeEnum(Column $column): DoctrineTypeEnum
    {
        return DoctrineTypeEnum::fromString((string) $column->type->value);
    }

    /**
     * @throws BaseCasterException
     */
    protected function determineCastType(Column $column, DoctrineTypeEnum $type): ?string
    {
        $mapper = $this->getTypeMapper();

        if (!is_string($mapper) || ($mapper !== TypeMapper::class && !is_subclass_of($mapper, TypeMapper::class))) {
            throw BaseCasterException::invalidTypeMapper();
        }

        /** @var TypeMapper */
        $instance = new $mapper($this->getTypesMap());

        return $instance->handle($column, $type);
    }

    protected function isValidCastType(string $castType): bool
    {
        $castType = Str::before($castType, ':');

        return is_subclass_of($castType, Castable::class)
            ||  is_subclass_of($castType, CastsAttributes::class)
            ||  is_subclass_of($castType, CastsInboundAttributes::class)
            ||  enum_exists($castType)
            ||  in_array($castType, $this->getSupportedCastTypes(), true);
    }

    /**
     * @throws BaseCasterException
     */
    protected function handleInvalidCastType(Column $column, DoctrineTypeEnum $type, string $castType): ?string
    {
        throw BaseCasterException::invalidCastType($column, $type, $castType);
    }

    protected function getSupportedCastTypes(): array
    {
        return CastTypeEnum::values();
    }

    protected function getTypeMapper(): ?string
    {
        return $this->typeMapper;
    }

    protected function getTypesMap(): ?array
    {
        return $this->typesMap;
    }
}
