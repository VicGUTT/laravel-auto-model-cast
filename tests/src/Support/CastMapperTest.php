<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use VicGutt\InspectDb\Inspect;
use Doctrine\DBAL\Types\StringType;
use VicGutt\InspectDb\Entities\Column;
use VicGutt\AutoModelCast\Enums\CastTypeEnum;
use VicGutt\AutoModelCast\Support\TypeMapper;
use VicGutt\InspectDb\Types\DoctrineTypeEnum;
use Doctrine\DBAL\Schema\Column as SchemaColumn;

$_ = static function (array $items): array {
    return collect($items)
        ->filter()
        ->map(static fn (string $value, string $key): array => [$key, $value])
        ->toArray();
};

$__ = static function (DoctrineTypeEnum $type): array {
    $column = new Column(new SchemaColumn('yolo', new StringType()));
    $value = TypeMapper::new()->handle($column, $type);

    return [$type, $value];
};

it('provides a default array of "Doctrine type" to "cast type" mappings', function (string $doctrineType, string $castType): void {
    expect(!is_null(DoctrineTypeEnum::tryFrom($doctrineType)))->toEqual(true);
    expect(!is_null(CastTypeEnum::tryFrom($castType)))->toEqual(true);
})->with($_(TypeMapper::defaults()));

it('provides an opinionated array of "Doctrine type" to "cast type" mappings', function (string $doctrineType, string $castType): void {
    expect(!is_null(DoctrineTypeEnum::tryFrom($doctrineType)))->toEqual(true);
    expect(!is_null(CastTypeEnum::tryFrom($castType)))->toEqual(true);
})->with($_(TypeMapper::opinionated()));

it('yields "null" for the `Unknown` Doctrine type in the provided default mappings', function (): void {
    expect(TypeMapper::defaults()[DoctrineTypeEnum::UNKNOWN->value])->toEqual(null);
});

it('yields "null" for the `Unknown` Doctrine type in the provided opinionated mappings', function (): void {
    expect(TypeMapper::opinionated()[DoctrineTypeEnum::UNKNOWN->value])->toEqual(null);
});

it('uses all the doctrine types in the default mappings', function (string $doctrineType): void {
    expect(array_key_exists($doctrineType, TypeMapper::defaults()))->toEqual(true);
})->with(DoctrineTypeEnum::values());

it('uses all the doctrine types in the opinionated mappings', function (string $doctrineType): void {
    expect(array_key_exists($doctrineType, TypeMapper::opinionated()))->toEqual(true);
})->with(DoctrineTypeEnum::values());

it('can handle a given column and return a cast type', function (): void {
    /** @var Column $column */
    foreach (Inspect::columns('entities') as $column) {
        $type = DoctrineTypeEnum::from($column->type->toDoctrine());
        $value = TypeMapper::new(TypeMapper::opinionated())->handle($column, $type);

        expect(Str::before($value, ':'))->toEqual(TypeMapper::opinionated()[$type->value]);
    }
});

it('can handle the `ASCII_STRING` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::ASCII_STRING);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `BIGINT` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::BIGINT);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `BINARY` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::BINARY);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `BLOB` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::BLOB);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `BOOLEAN` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::BOOLEAN);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `DATE` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::DATE);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `DATE_IMMUTABLE` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::DATE_IMMUTABLE);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `DATEINTERVAL` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::DATEINTERVAL);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `DATETIME` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::DATETIME);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `DATETIME_IMMUTABLE` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::DATETIME_IMMUTABLE);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `DATETIMETZ` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::DATETIMETZ);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `DATETIMETZ_IMMUTABLE` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::DATETIMETZ_IMMUTABLE);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `DECIMAL` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::DECIMAL);

    $value = Str::before($value, ':');

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `FLOAT` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::FLOAT);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `GUID` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::GUID);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `INTEGER` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::INTEGER);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `JSON` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::JSON);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `SIMPLE_ARRAY` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::SIMPLE_ARRAY);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `SMALLINT` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::SMALLINT);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `STRING` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::STRING);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `TEXT` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::TEXT);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `TIME` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::TIME);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `TIME_IMMUTABLE` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::TIME_IMMUTABLE);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle the `UNKNOWN` type ', function () use ($__): void {
    [$type, $value] = $__(DoctrineTypeEnum::UNKNOWN);

    expect($value)->toEqual(TypeMapper::defaults()[$type->value]);
});

it('can handle `Decimal` types without any given parameter', function (): void {
    $type = DoctrineTypeEnum::DECIMAL;

    $typesMap = [
        ...TypeMapper::defaults(),
        $type->value => 'decimal',
    ];

    $column = Inspect::column('decimal', 'entities');
    $value = TypeMapper::new($typesMap)->handle($column, $type);

    expect($value)->toEqual("decimal:{$column->precision}");
});

it('can handle `Decimal` types with a given precision', function (): void {
    $type = DoctrineTypeEnum::DECIMAL;

    $typesMap = [
        ...TypeMapper::defaults(),
        $type->value => 'decimal:4',
    ];

    $column = Inspect::column('decimal', 'entities');
    $value = TypeMapper::new($typesMap)->handle($column, $type);

    expect($value)->toEqual('decimal:4');
});
