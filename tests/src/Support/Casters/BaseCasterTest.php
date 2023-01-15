<?php

declare(strict_types=1);

use VicGutt\InspectDb\Inspect;
use VicGutt\InspectDb\Entities\Column;
use Illuminate\Database\Eloquent\Model;
use VicGutt\AutoModelCast\Support\TypeMapper;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Database\Eloquent\Casts\AsStringable;
use VicGutt\AutoModelCast\Support\Casters\BaseCaster;
use VicGutt\AutoModelCast\Exceptions\BaseCasterException;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use VicGutt\AutoModelCast\Tests\TestSupport\app\Models\Entity;
use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;

final class CustomCaster extends BaseCaster
{
    private ?Closure $checker = null;

    public static function new(?string $typeMapper = null, ?array $typesMap = null): static
    {
        return new static(
            $typeMapper ?: TypeMapper::class,
            $typesMap ?: TypeMapper::opinionated(),
        );
    }

    public function shouldBeHandled(Column $column, Model $model): bool
    {
        if (!$this->checker) {
            return true;
        }

        return ($this->checker)($column, $model);
    }

    public function handle(Column $column, Model $model): ?string
    {
        return $this->handleColumn($column);
    }

    public function getTypesMap(): array
    {
        return $this->typesMap;
    }

    public function useChecker(Closure $checker): self
    {
        $this->checker = $checker;

        return $this;
    }
}

/**
 * @see https://laravel.com/docs/9.x/eloquent-mutators#custom-casts
 */
class Json implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return json_decode($value, true);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return json_encode($value);
    }
}

/**
 * @see https://laravel.com/docs/9.x/eloquent-mutators#inbound-casting
 */
class Hash implements CastsInboundAttributes
{
    public function set($model, string $key, $value, array $attributes): string
    {
        return sha1($value);
    }
}

/**
 * @see https://laravel.com/docs/9.x/eloquent-mutators#anonymous-cast-classes
 */
class Address implements Castable
{
    public static function castUsing(array $arguments)
    {
        return new class () implements CastsAttributes {
            public function get($model, $key, $value, $attributes)
            {
                return '';
            }

            public function set($model, $key, $value, $attributes)
            {
                return '';
            }
        };
    }
}

enum IpAddressEnum: string
{
    case CASE1 = 'ip_address';
}

it('can handle a given collection of columns', function (): void {
    $columns = Inspect::columns('entities')->toBase();
    $caster = CustomCaster::new();

    $_ = static fn (string $columnName): string => $caster->getTypesMap()[$columns->get($columnName)->type->toDoctrine()];

    expect($caster->handleColumns($columns, new Entity()))->toEqual([
        'id'                      => $_('id'),
        'big_integer'             => $_('big_integer'),
        'binary'                  => $_('binary'),
        'boolean'                 => $_('boolean'),
        'char'                    => $_('char'),
        'date_time_tz'            => $_('date_time_tz'),
        'date_time'               => $_('date_time'),
        'date'                    => $_('date'),
        'decimal'                 => $_('decimal') . ':8',
        'double'                  => $_('double'),
        'float'                   => $_('float'),
        'integer'                 => $_('integer'),
        'ip_address'              => $_('ip_address'),
        'json'                    => $_('json'),
        'jsonb'                   => $_('jsonb'),
        'long_text'               => $_('long_text'),
        'medium_integer'          => $_('medium_integer'),
        'medium_text'             => $_('medium_text'),
        'small_integer'           => $_('small_integer'),
        'soft_deletes_tz'         => $_('soft_deletes_tz'),
        'soft_deletes'            => $_('soft_deletes'),
        'string'                  => $_('string'),
        'text'                    => $_('text'),
        'time_tz'                 => $_('time_tz'),
        'time'                    => $_('time'),
        'timestamp_tz'            => $_('timestamp_tz'),
        'timestamp'               => $_('timestamp'),
        'tiny_integer'            => $_('tiny_integer'),
        'tiny_text'               => $_('tiny_text'),
        'unsigned_big_integer'    => $_('unsigned_big_integer'),
        'unsigned_decimal'        => $_('unsigned_decimal') . ':8',
        'unsigned_integer'        => $_('unsigned_integer'),
        'unsigned_medium_integer' => $_('unsigned_medium_integer'),
        'unsigned_small_integer'  => $_('unsigned_small_integer'),
        'unsigned_tiny_integer'   => $_('unsigned_tiny_integer'),
        'ulid'                    => $_('ulid'),
        'uuid'                    => $_('uuid'),
        'year'                    => $_('year'),
        'created_at'              => $_('created_at'),
        'updated_at'              => $_('updated_at'),
    ]);
});

it('can ignore certain columns given a condition', function (): void {
    $columns = Inspect::columns('entities')->toBase();
    $caster = CustomCaster::new()->useChecker(static function (Column $column): bool {
        return str_contains($column->name, 'time');
    });

    $_ = static fn (string $columnName): string => $caster->getTypesMap()[$columns->get($columnName)->type->toDoctrine()];

    expect($caster->handleColumns($columns, new Entity()))->toEqual([
        'date_time_tz' => $_('date_time_tz'),
        'date_time'    => $_('date_time'),
        'time_tz'      => $_('time_tz'),
        'time'         => $_('time'),
        'timestamp_tz' => $_('timestamp_tz'),
        'timestamp'    => $_('timestamp'),
    ]);
});

it('can handle a given column', function (): void {
    $columns = Inspect::columns('entities')->toBase();
    $caster = CustomCaster::new();

    /** @var Column $column */
    foreach ($columns as $column) {
        $actual = $caster->handle($column, new Entity());
        $expected = $caster->getTypesMap()[$column->type->toDoctrine()];

        if (str_contains($column->name, 'decimal')) {
            $expected = "{$expected}:{$column->precision}";
        }

        expect($actual)->toEqual($expected);
    }
});

it('supports casting to any valid Laravel cast type', function (): void {
    $columns = Inspect::columns('entities')->toBase();
    $caster = CustomCaster::new(null, [
        'string' => AsStringable::class,
        'json' => Json::class,
        'bigint' => Hash::class,
        'date' => Address::class,
        'ip_address' => IpAddressEnum::class,
    ]);

    // No exception should be thrown here!
    $caster->handleColumns($columns, new Entity());

    expect('All good!')->toEqual('All good!');
});

it('throws an exception when trying to cast to an invalid Laravel cast type', function (): void {
    $columns = Inspect::columns('entities')->toBase();
    $caster = CustomCaster::new(null, [
        'string' => 'nope!',
    ]);

    expect(static fn () => $caster->handleColumns($columns, new Entity()))->toThrow(
        BaseCasterException::class,
        "An invalid cast type `nope!` was given for the column `char` of type `string`. Valid cast types are any value that can be provided to a model's \"cast\" property.",
    );
});

it('throws an exception when trying to use an invalid type mapper', function (): void {
    $columns = Inspect::columns('entities')->toBase();
    $class = TypeMapper::class;

    expect(
        static fn () => CustomCaster::new('nope!')->handleColumns($columns, new Entity()),
    )->toThrow(
        BaseCasterException::class,
        "The provided type mapper is invalid, please ensure it inherits the `{$class}` class.",
    );
});
