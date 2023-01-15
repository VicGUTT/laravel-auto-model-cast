<?php

declare(strict_types=1);

use VicGutt\InspectDb\Inspect;
use VicGutt\AutoModelCast\Support\TypeMapper;
use VicGutt\AutoModelCast\Support\Casters\DefaultCaster;
use VicGutt\AutoModelCast\Tests\TestSupport\app\Models\Entity;

it('ignores "string like" and default timestamps columns', function (): void {
    $columns = Inspect::columns('entities')->toBase();
    $typesMap = TypeMapper::opinionated();
    $caster = (new DefaultCaster(TypeMapper::class, $typesMap));

    $_ = static fn (string $columnName): string => $typesMap[$columns->get($columnName)->type->toDoctrine()];

    expect($caster->handleColumns($columns, new Entity()))->toEqual([
        'id'                      => $_('id'),
        'big_integer'             => $_('big_integer'),
        'binary'                  => $_('binary'),
        'boolean'                 => $_('boolean'),
        'date_time_tz'            => $_('date_time_tz'),
        'date_time'               => $_('date_time'),
        'date'                    => $_('date'),
        'decimal'                 => $_('decimal') . ':8',
        'double'                  => $_('double'),
        'float'                   => $_('float'),
        'integer'                 => $_('integer'),
        'json'                    => $_('json'),
        'jsonb'                   => $_('jsonb'),
        'medium_integer'          => $_('medium_integer'),
        'small_integer'           => $_('small_integer'),
        'soft_deletes_tz'         => $_('soft_deletes_tz'),
        'soft_deletes'            => $_('soft_deletes'),
        'time_tz'                 => $_('time_tz'),
        'time'                    => $_('time'),
        'timestamp_tz'            => $_('timestamp_tz'),
        'timestamp'               => $_('timestamp'),
        'tiny_integer'            => $_('tiny_integer'),
        'unsigned_big_integer'    => $_('unsigned_big_integer'),
        'unsigned_decimal'        => $_('unsigned_decimal') . ':8',
        'unsigned_integer'        => $_('unsigned_integer'),
        'unsigned_medium_integer' => $_('unsigned_medium_integer'),
        'unsigned_small_integer'  => $_('unsigned_small_integer'),
        'unsigned_tiny_integer'   => $_('unsigned_tiny_integer'),
        'year'                    => $_('year'),
    ]);
});
