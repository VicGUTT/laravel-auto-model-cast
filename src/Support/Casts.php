<?php

declare(strict_types=1);

namespace VicGutt\AutoModelCast\Support;

use JsonSerializable;
use VicGutt\InspectDb\Inspect;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use VicGutt\ModelsFinder\ModelData;
use VicGutt\InspectDb\Entities\Column;
use VicGutt\ModelsFinder\ModelsFinder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use VicGutt\AutoModelCast\Exceptions\CastsException;
use VicGutt\AutoModelCast\Support\Casters\BaseCaster;
use VicGutt\AutoModelCast\Support\Casters\DefaultCaster;
use VicGutt\AutoModelCast\Exceptions\BaseCasterException;

/**
 * @implements Arrayable<array-key, mixed>
 */
class Casts implements Arrayable, Jsonable, JsonSerializable
{
    private function __construct(
        protected array $discoverModelsUsing,
        protected array $defaultTypesMap,
        protected string $typeMapper,
        protected string $defaultCaster,
        protected array $customCasters,
    ) {
    }

    public static function new(
        null|array $discoverModelsUsing = null,
        null|array $defaultTypesMap = null,
        null|string $typeMapper = null,
        null|string $defaultCaster = null,
        null|array $customCasters = null,
    ): self {
        return new self(
            $discoverModelsUsing ?: [],
            $defaultTypesMap ?: [],
            $typeMapper ?: TypeMapper::class,
            $defaultCaster ?: DefaultCaster::class,
            $customCasters ?: [],
        );
    }

    /**
     * Retrieve all the column cast types for a given model.
     */
    public static function for(string|Model $model): array
    {
        if ($model instanceof Model) {
            return self::forModel($model);
        }

        return self::forModelFqcn($model);
    }

    /**
     * Retrieve all the column cast types for a given model.
     */
    public static function forModel(Model $model): array
    {
        return self::forModelFqcn($model::class);
    }

    /**
     * Retrieve the cast type of a given column for a given model.
     */
    public static function forColumn(string|Model $model, string|Column $column): ?string
    {
        if ($column instanceof Column) {
            $column = $column->name;
        }

        return self::for($model)[$column] ?? null;
    }

    protected static function forModelFqcn(string $model): array
    {
        /** @var array */
        return config("auto-model-cast.casts.{$model}", []);
    }

    /**
     * Parameters to be passed down to `VicGutt\ModelsFinder\ModelsFinder`.
     *
     * @param null|string $directory The directory in which to recusively start searching for models. Defaults to `app_path('Models')`.
     * @param null|string $basePath The autoloaded entry directory of the project where the search will be initiated. Defaults to `base_path()`.
     * @param null|string $baseNamespace The autoloaded base namespace of the project where the search will be initiated. Defaults to `''`.
     *
     * @see https://github.com/vicgutt/laravel-models-finder
     */
    public function discoverModelsUsing(
        ?string $directory = null,
        ?string $basePath = null,
        ?string $baseNamespace = null,
    ): static {
        $this->discoverModelsUsing = [
            'directory' => $directory,
            'basePath' => $basePath,
            'baseNamespace' => $baseNamespace,
        ];

        return $this;
    }

    /**
     * The "type mapper" responsable for mapping a given
     * Doctrine column type into a built-in Laravel cast type.
     * The mapper takes a column in, and spits a cast type out.
     */
    public function useTypeMapper(string $value): static
    {
        $this->typeMapper = $value;

        return $this;
    }

    /**
     * The "caster" to be used by default.
     * A caster is responsable for converting a given collection of
     * database columns into an array of supported Laravel cast types.
     */
    public function useDefaultCaster(string $value): static
    {
        $this->defaultCaster = $value;

        return $this;
    }

    /**
     * Mappings between Doctrine column types
     * and built-in Laravel cast types.
     *
     * @example
     * ```php
     * Casts::new()->withDefaultTypesMap([
     *     DoctrineTypeEnum::BIGINT->value => CastTypeEnum::INT->value,
     *     DoctrineTypeEnum::DATE->value => CastTypeEnum::IMMUTABLE_DATE->value,
     *     DoctrineTypeEnum::JSON->value => CastTypeEnum::AS_ARRAY_OBJECT_CLASS->value,
     * ]);
     * ```
     *
     * @param array<string, string> $values
     */
    public function withDefaultTypesMap(array $values): static
    {
        $this->defaultTypesMap = $values;

        return $this;
    }

    /**
     * Customizing the "caster"s to be used per model.
     *
     * The array keys should be fully quilified class names
     * of models and the values, fully quilified class names
     * of casters, each extending the base caster.
     *
     * @param array<class-string, class-string> $values
     */
    public function withCustomCasters(array $values): static
    {
        $this->customCasters = $values;

        return $this;
    }

    /**
     * Discover and retrieve your project's models.
     *
     * @return Enumerable<int, ModelData>
     */
    public function models(): Enumerable
    {
        return ModelsFinder::find(...$this->discoverModelsUsing);
    }

    /**
     * Retrieve the casters to be used.
     *
     * The keys are fully quilified class names of models
     * and the values are fully quilified class names of casters.
     *
     * @param Enumerable<int, ModelData> $models
     * @throws BaseCasterException
     * @return Enumerable<class-string, class-string>
     */
    public function casters(Enumerable $models): Enumerable
    {
        return $models->reduce(function (Collection $acc, ModelData $modelData): Collection {
            $modelFQCN = $modelData->class;

            $caster = $this->customCasters[$modelFQCN] ?? $this->defaultCaster;

            if (!is_subclass_of($caster, BaseCaster::class)) {
                throw BaseCasterException::invalidCaster($caster);
            }

            $acc[$modelFQCN] = $caster;

            return $acc;
        }, collect());
    }

    /**
     * Retrieve, for all the models found, all the casts that
     * could be automatically determined.
     *
     * The array keys are fully quilified class names of the
     * models and the values are arrays of all the columns
     * and their cast type.
     */
    public function all(): array
    {
        $models = $this->models();
        $casters = $this->casters($models);

        return $this->performCasting($casters)->toArray();
    }

    /**
     * Convert the instance to its array representation.
     */
    public function toArray(): array
    {
        return [
            'discover_models_using' => $this->discoverModelsUsing,
            'default_types_map' => $this->defaultTypesMap,
            'type_mapper' => $this->typeMapper,
            'default_caster' => $this->defaultCaster,
            'custom_casters' => $this->customCasters,
            'casts' => $this->all(),
        ];
    }

    /**
     * Convert the instance to its JSON representation.
     *
     * @param  int  $options
     */
    public function toJson($options = 0): string
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (!$json || JSON_ERROR_NONE !== json_last_error()) {
            throw CastsException::jsonUnencodable($this); // @codeCoverageIgnore
        }

        return $json;
    }

    /**
     * Convert the object into something JSON serializable.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @param Enumerable<class-string, class-string> $casters
     * @return Enumerable<class-string, array<string, string>>
     */
    protected function performCasting(Enumerable $casters): Enumerable
    {
        return $casters->reduce(function (Collection $acc, string $casterFQCN, string $modelFQCN): Collection {
            /** @var Model */
            $model = new $modelFQCN();
            $table = $model->getTable();
            $connection = $model->getConnectionName();

            /** @var BaseCaster */
            $caster = new $casterFQCN($this->typeMapper, $this->defaultTypesMap);
            $columns = Inspect::columns($table, $connection)->toBase();

            $acc[$modelFQCN] = $caster->handleColumns($columns, $model);

            return $acc;
        }, collect());
    }

    /**
     * Convert the instance to its string representation.
     */
    public function __toString(): string
    {
        return $this->toJson();
    }
}
