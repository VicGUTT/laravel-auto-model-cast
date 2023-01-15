<?php

declare(strict_types=1);

use VicGutt\InspectDb\Inspect;
use VicGutt\InspectDb\Entities\Column;
use VicGutt\ModelsFinder\ModelsFinder;
use Illuminate\Database\Eloquent\Model;
use VicGutt\AutoModelCast\Support\Casts;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use VicGutt\AutoModelCast\Enums\CastTypeEnum;
use VicGutt\AutoModelCast\Support\TypeMapper;
use VicGutt\InspectDb\Types\DoctrineTypeEnum;
use VicGutt\AutoModelCast\Support\Casters\BaseCaster;
use VicGutt\AutoModelCast\Support\Casters\DefaultCaster;
use VicGutt\AutoModelCast\Exceptions\BaseCasterException;
use VicGutt\AutoModelCast\Tests\TestSupport\app\Models\Entity;

it('is `Arrayable`', function (): void {
    expect(Casts::new() instanceof Arrayable)->toEqual(true);
});

it('is `Jsonable`', function (): void {
    expect(Casts::new() instanceof Jsonable)->toEqual(true);
});

it('is `JsonSerializable`', function (): void {
    expect(Casts::new() instanceof JsonSerializable)->toEqual(true);
});

it("allows for the customization of `ModelsFinder`'s params from the named constructor", function (): void {
    $models = Casts::new()->models();

    expect($models->isEmpty())->toEqual(true);

    $models = Casts::new(null)->models();

    expect($models->isEmpty())->toEqual(true);

    $models = Casts::new([
        'directory' => null,
        'basePath' => null,
        'baseNamespace' => null,
    ])->models();

    expect($models->isEmpty())->toEqual(true);

    $models = Casts::new($this->modelsFinderParams())->models();

    expect($models->isEmpty())->toEqual(false);
    expect($models->pluck('class')->toArray())->toEqual([Entity::class]);
});

it("allows for the customization of the default types map from the named constructor", function (): void {
    $casts = Casts::new();

    expect(is_array($casts->toArray()['default_types_map']))->toEqual(true);
    expect(empty($casts->toArray()['default_types_map']))->toEqual(true);

    $casts = Casts::new(defaultTypesMap: null);

    expect(is_array($casts->toArray()['default_types_map']))->toEqual(true);
    expect(empty($casts->toArray()['default_types_map']))->toEqual(true);

    $casts = Casts::new(defaultTypesMap: []);

    expect(is_array($casts->toArray()['default_types_map']))->toEqual(true);
    expect(empty($casts->toArray()['default_types_map']))->toEqual(true);

    $casts = Casts::new(defaultTypesMap: ['yolo' => 'yolo']);

    expect($casts->toArray()['default_types_map'])->toEqual(['yolo' => 'yolo']);
});

it("allows for the customization of the type mapper from the named constructor", function (): void {
    $casts = Casts::new();

    expect($casts->toArray()['type_mapper'])->toEqual(TypeMapper::class);

    $casts = Casts::new(typeMapper: null);

    expect($casts->toArray()['type_mapper'])->toEqual(TypeMapper::class);

    $casts = Casts::new(typeMapper: 'yolo');

    expect($casts->toArray()['type_mapper'])->toEqual('yolo');
});

it("allows for the customization of the default caster from the named constructor", function (): void {
    $casts = Casts::new();

    expect($casts->toArray()['default_caster'])->toEqual(DefaultCaster::class);

    $casts = Casts::new(defaultCaster: null);

    expect($casts->toArray()['default_caster'])->toEqual(DefaultCaster::class);

    $casts = Casts::new(defaultCaster: 'yolo');

    expect($casts->toArray()['default_caster'])->toEqual('yolo');
});

it("allows for the customization of custom casters from the named constructor", function (): void {
    $casts = Casts::new();

    expect($casts->toArray()['custom_casters'])->toEqual([]);

    $casts = Casts::new(customCasters: null);

    expect($casts->toArray()['custom_casters'])->toEqual([]);

    $casts = Casts::new(customCasters: ['yolo' => 'yolo']);

    expect($casts->toArray()['custom_casters'])->toEqual(['yolo' => 'yolo']);
});

it("allows for the customization of `ModelsFinder`'s params from a dedicated method", function (): void {
    $models = Casts::new()->discoverModelsUsing()->models();

    expect($models->isEmpty())->toEqual(true);

    $models = Casts::new()->discoverModelsUsing(
        directory: null,
        basePath: null,
        baseNamespace: null,
    )->models();

    expect($models->isEmpty())->toEqual(true);

    $models = Casts::new()->discoverModelsUsing(...$this->modelsFinderParams())->models();

    expect($models->isEmpty())->toEqual(false);
    expect($models->pluck('class')->toArray())->toEqual([Entity::class]);
});

it("allows for the customization of the default types map from a dedicated method", function (): void {
    $casts = Casts::new()->withDefaultTypesMap([]);

    expect(is_array($casts->toArray()['default_types_map']))->toEqual(true);
    expect(empty($casts->toArray()['default_types_map']))->toEqual(true);

    $casts = Casts::new()->withDefaultTypesMap(['yolo' => 'yolo']);

    expect($casts->toArray()['default_types_map'])->toEqual(['yolo' => 'yolo']);
});

it("allows for the customization of the type mapper from a dedicated method", function (): void {
    $casts = Casts::new()->useTypeMapper(TypeMapper::class);

    expect($casts->toArray()['type_mapper'])->toEqual(TypeMapper::class);

    $casts = Casts::new()->useTypeMapper('yolo');

    expect($casts->toArray()['type_mapper'])->toEqual('yolo');
});

it("allows for the customization of the default caster from a dedicated method", function (): void {
    $casts = Casts::new()->useDefaultCaster(DefaultCaster::class);

    expect($casts->toArray()['default_caster'])->toEqual(DefaultCaster::class);

    $casts = Casts::new()->useDefaultCaster('yolo');

    expect($casts->toArray()['default_caster'])->toEqual('yolo');
});

it("allows for the customization of custom casters from a dedicated method", function (): void {
    $casts = Casts::new()->withCustomCasters([]);

    expect($casts->toArray()['custom_casters'])->toEqual([]);

    $casts = Casts::new()->withCustomCasters(['yolo' => 'yolo']);

    expect($casts->toArray()['custom_casters'])->toEqual(['yolo' => 'yolo']);
});

test('Casts@models', function (): void {
    $params = $this->modelsFinderParams();

    expect(Casts::new($params)->models()->toArray())->toEqual(ModelsFinder::find(...$params)->toArray());
});

test('[Casts@casters] - it uses the default caster as a fallback', function (): void {
    $cast = Casts::new($this->modelsFinderParams());

    $models = $cast->models();
    $casters = $cast->casters($models);

    expect($casters->toArray())->toEqual([Entity::class => DefaultCaster::class]);
});

test('[Casts@casters] - it uses the provided custom casters', function (): void {
    $caster = new class () extends BaseCaster {
        public function handle(Column $column, Model $model): ?string
        {
            return null;
        }
    };

    $cast = Casts::new($this->modelsFinderParams())->withCustomCasters([Entity::class => $caster::class]);

    $models = $cast->models();
    $casters = $cast->casters($models);

    expect($casters->toArray())->toEqual([Entity::class => $caster::class]);
});

test('[Casts@casters] - it throws an exception when a given custom caster does not inherit the BaseCaster', function (): void {
    $caster = new class () {};
    $casterFqcn = $caster::class;
    $baseCasterFqcn = BaseCaster::class;

    $cast = Casts::new($this->modelsFinderParams())->withCustomCasters([Entity::class => $casterFqcn]);

    $models = $cast->models();

    expect(static fn () => $cast->casters($models))->toThrow(
        BaseCasterException::class,
        "The provided caster `{$casterFqcn}` is invalid, please ensure it inherits the `{$baseCasterFqcn}` class.",
    );
});

test('[Casts@all] - it works', function (): void {
    $cast = Casts::new($this->modelsFinderParams());

    $defaults = TypeMapper::defaults();

    expect($cast->all())->toEqual([
        Entity::class => [
            'id'                      => $defaults[DoctrineTypeEnum::BIGINT->value],
            'big_integer'             => $defaults[DoctrineTypeEnum::BIGINT->value],
            'binary'                  => $defaults[DoctrineTypeEnum::BINARY->value],
            'boolean'                 => $defaults[DoctrineTypeEnum::BOOLEAN->value],
            'date_time_tz'            => $defaults[DoctrineTypeEnum::DATETIMETZ->value],
            'date_time'               => $defaults[DoctrineTypeEnum::DATETIME->value],
            'date'                    => $defaults[DoctrineTypeEnum::DATE->value],
            'decimal'                 => $defaults[DoctrineTypeEnum::DECIMAL->value] . ':8',
            'double'                  => $defaults[DoctrineTypeEnum::FLOAT->value],
            'float'                   => $defaults[DoctrineTypeEnum::FLOAT->value],
            'integer'                 => $defaults[DoctrineTypeEnum::INTEGER->value],
            'json'                    => $defaults[DoctrineTypeEnum::JSON->value],
            'jsonb'                   => $defaults[DoctrineTypeEnum::JSON->value],
            'medium_integer'          => $defaults[DoctrineTypeEnum::INTEGER->value],
            'small_integer'           => $defaults[DoctrineTypeEnum::INTEGER->value],
            'soft_deletes_tz'         => $defaults[DoctrineTypeEnum::DATETIMETZ->value],
            'soft_deletes'            => $defaults[DoctrineTypeEnum::DATETIME->value],
            'time_tz'                 => $defaults[DoctrineTypeEnum::STRING->value],
            'time'                    => $defaults[DoctrineTypeEnum::STRING->value],
            'timestamp_tz'            => $defaults[DoctrineTypeEnum::DATETIMETZ->value],
            'timestamp'               => $defaults[DoctrineTypeEnum::DATETIME->value],
            'tiny_integer'            => $defaults[DoctrineTypeEnum::BOOLEAN->value],
            'unsigned_big_integer'    => $defaults[DoctrineTypeEnum::BIGINT->value],
            'unsigned_decimal'        => $defaults[DoctrineTypeEnum::DECIMAL->value] . ':8',
            'unsigned_integer'        => $defaults[DoctrineTypeEnum::INTEGER->value],
            'unsigned_medium_integer' => $defaults[DoctrineTypeEnum::INTEGER->value],
            'unsigned_small_integer'  => $defaults[DoctrineTypeEnum::INTEGER->value],
            'unsigned_tiny_integer'   => $defaults[DoctrineTypeEnum::BOOLEAN->value],
            'year'                    => $defaults[DoctrineTypeEnum::DATE->value],
        ],
    ]);
});

test('[Casts@toArray] - it works', function (): void {
    $defaultTypesMap = [
        DoctrineTypeEnum::ASCII_STRING->value         => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::BIGINT->value               => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::BINARY->value               => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::BLOB->value                 => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::BOOLEAN->value              => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::DATE->value                 => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::DATE_IMMUTABLE->value       => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::DATEINTERVAL->value         => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::DATETIME->value             => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::DATETIME_IMMUTABLE->value   => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::DATETIMETZ->value           => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::DATETIMETZ_IMMUTABLE->value => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::DECIMAL->value              => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::FLOAT->value                => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::GUID->value                 => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::INTEGER->value              => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::JSON->value                 => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::SIMPLE_ARRAY->value         => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::SMALLINT->value             => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::STRING->value               => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::TEXT->value                 => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::TIME->value                 => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::TIME_IMMUTABLE->value       => CastTypeEnum::STRING->value,
        DoctrineTypeEnum::UNKNOWN->value              => CastTypeEnum::STRING->value,
    ];

    $mapper = new class ($defaultTypesMap) extends TypeMapper {
        protected function handleBigint(Column $column, DoctrineTypeEnum $type): ?string
        {
            return CastTypeEnum::BOOL->value;
        }
        protected function handleDecimal(Column $column, DoctrineTypeEnum $type): ?string
        {
            return null;
        }
    };

    $defaultCaster = new class () extends DefaultCaster {};
    $customCaster = new class () extends DefaultCaster {
        public function shouldBeHandled(Column $column, Model $model): bool
        {
            return true;
        }
    };

    $customCasters = [
        \App\Models\Yolo::class => \App\Support\AutoCast\YoloCustomCaster::class,
        Entity::class => $customCaster::class,
    ];

    $data = Casts::new()
        ->discoverModelsUsing(...$this->modelsFinderParams())
        ->useTypeMapper($mapper::class)
        ->useDefaultCaster($defaultCaster::class)
        ->withDefaultTypesMap($defaultTypesMap)
        ->withCustomCasters($customCasters)
        ->toArray();

    expect($data)->toEqual([
        'discover_models_using' => $this->modelsFinderParams(),
        'default_types_map' => $defaultTypesMap,
        'type_mapper' => $mapper::class,
        'default_caster' => $defaultCaster::class,
        'custom_casters' => $customCasters,
        'casts' => [
            Entity::class => [
                'id' => 'bool',
                'big_integer' => 'bool',
                'binary' => 'string',
                'boolean' => 'string',
                'char' => 'string',
                'date_time_tz' => 'string',
                'date_time' => 'string',
                'date' => 'string',
                // 'decimal' => Not present because `handleDecimal` returned `null`,
                'double' => 'string',
                'float' => 'string',
                'integer' => 'string',
                'ip_address' => 'string',
                'json' => 'string',
                'jsonb' => 'string',
                'long_text' => 'string',
                'medium_integer' => 'string',
                'medium_text' => 'string',
                'small_integer' => 'string',
                'soft_deletes_tz' => 'string',
                'soft_deletes' => 'string',
                'string' => 'string',
                'text' => 'string',
                'time_tz' => 'string',
                'time' => 'string',
                'timestamp_tz' => 'string',
                'timestamp' => 'string',
                'tiny_integer' => 'string',
                'tiny_text' => 'string',
                'unsigned_big_integer' => 'bool',
                // 'unsigned_decimal' => Not present because `handleDecimal` returned `null`,
                'unsigned_integer' => 'string',
                'unsigned_medium_integer' => 'string',
                'unsigned_small_integer' => 'string',
                'unsigned_tiny_integer' => 'string',
                'ulid' => 'string',
                'uuid' => 'string',
                'year' => 'string',
                'created_at' => 'string',
                'updated_at' => 'string',
            ],
        ],
    ]);
});

test('[Casts@toJson] - it works', function (): void {
    $casts = Casts::new()
        ->discoverModelsUsing(...$this->modelsFinderParams())
        ->withDefaultTypesMap(TypeMapper::opinionated());

    expect($casts->toJson())->toEqual(json_encode($casts->toArray()));

    expect($casts->toJson(JSON_PRETTY_PRINT))->toEqual(json_encode($casts->toArray(), JSON_PRETTY_PRINT));
});

test('[Casts@jsonSerialize] - it works', function (): void {
    $casts = Casts::new()
        ->discoverModelsUsing(...$this->modelsFinderParams())
        ->withDefaultTypesMap(TypeMapper::opinionated());

    expect(json_encode($casts))->toEqual($casts->toJson());

    expect(json_encode($casts, JSON_PRETTY_PRINT))->toEqual($casts->toJson(JSON_PRETTY_PRINT));
});

test('[Casts@__toString] - it works', function (): void {
    $casts = Casts::new()
        ->discoverModelsUsing(...$this->modelsFinderParams())
        ->withDefaultTypesMap(TypeMapper::opinionated());

    expect((string) $casts)->toEqual($casts->toJson());
});

test('[Casts::for] - it works', function (): void {
    $model = Entity::class;

    expect(Casts::for($model))->toEqual(config("auto-model-cast.casts.{$model}", []));

    expect(Casts::for(new $model()))->toEqual(config("auto-model-cast.casts.{$model}", []));

    $model = 'Yolo';

    expect(Casts::for($model))->toEqual([]);
});

test('[Casts::forColumn] - it works', function (): void {
    $model = Entity::class;

    foreach (config("auto-model-cast.casts.{$model}") as $column => $value) {
        expect(Casts::forColumn($model, $column))->toEqual($value);

        expect(Casts::forColumn($model, Inspect::column($column, 'entities')))->toEqual($value);
    }

    expect(Casts::forColumn($model, 'nope'))->toEqual(null);

    expect(Casts::forColumn('Yolo', 'nope'))->toEqual(null);
});
