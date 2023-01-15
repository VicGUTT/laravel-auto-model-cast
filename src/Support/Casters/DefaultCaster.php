<?php

declare(strict_types=1);

namespace VicGutt\AutoModelCast\Support\Casters;

use VicGutt\InspectDb\Entities\Column;
use Illuminate\Database\Eloquent\Model;
use VicGutt\InspectDb\Types\DoctrineTypeEnum;

class DefaultCaster extends BaseCaster
{
    public function handle(Column $column, Model $model): ?string
    {
        return $this->handleColumn($column);
    }

    public function shouldBeHandled(Column $column, Model $model): bool
    {
        /**
         * - Choosing not to handle "string like" types as it's not needed.
         *   Values are typically treated as strings by default.
         * - Choosing not to handle the default timestamps as Laravel already
         *   has backed-in behaviour for that.
         */
        return !$this->isStringLikeColumn($column) && !$this->isDefaultTimestampsColumn($column, $model);
    }

    protected function isStringLikeColumn(Column $column): bool
    {
        return in_array($this->getColumnDoctrineTypeEnum($column), [
            DoctrineTypeEnum::ASCII_STRING,
            DoctrineTypeEnum::STRING,
            DoctrineTypeEnum::TEXT,
        ], true);
    }

    protected function isDefaultTimestampsColumn(Column $column, Model $model): bool
    {
        return in_array($this->getColumnName($column), [
            $model->getCreatedAtColumn(),
            $model->getUpdatedAtColumn(),
        ], true);
    }
}
