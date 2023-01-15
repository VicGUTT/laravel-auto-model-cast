<?php

declare(strict_types=1);

use Carbon\Carbon;
use VicGutt\InspectDb\Inspect;
use VicGutt\InspectDb\Types\Type;
use VicGutt\AutoModelCast\Tests\TestSupport\app\Models\Entity;

test('HasAutoCasting@getAutoCasts', function (): void {
    $defaultTypesMap = config('auto-model-cast.default_types_map');
    $columns = Inspect::columns('entities');
    $type = static fn (string $columnName): Type => $columns->get($columnName)->type;

    $decimalPrecision = $columns->get('decimal')->precision;

    expect((new Entity())->getAutoCasts())->toEqual([
        'id'                      => $defaultTypesMap[$type('id')->toDoctrine()],
        'big_integer'             => $defaultTypesMap[$type('big_integer')->toDoctrine()],
        'binary'                  => $defaultTypesMap[$type('binary')->toDoctrine()],
        'boolean'                 => $defaultTypesMap[$type('boolean')->toDoctrine()],
        'date_time_tz'            => $defaultTypesMap[$type('date_time_tz')->toDoctrine()],
        'date_time'               => $defaultTypesMap[$type('date_time')->toDoctrine()],
        'date'                    => $defaultTypesMap[$type('date')->toDoctrine()],
        'decimal'                 => $defaultTypesMap[$type('decimal')->toDoctrine()] . ":{$decimalPrecision}",
        'double'                  => $defaultTypesMap[$type('double')->toDoctrine()],
        'float'                   => $defaultTypesMap[$type('float')->toDoctrine()],
        'integer'                 => $defaultTypesMap[$type('integer')->toDoctrine()],
        'json'                    => $defaultTypesMap[$type('json')->toDoctrine()],
        'jsonb'                   => $defaultTypesMap[$type('jsonb')->toDoctrine()],
        'medium_integer'          => $defaultTypesMap[$type('medium_integer')->toDoctrine()],
        'small_integer'           => $defaultTypesMap[$type('small_integer')->toDoctrine()],
        'soft_deletes_tz'         => $defaultTypesMap[$type('soft_deletes_tz')->toDoctrine()],
        'soft_deletes'            => $defaultTypesMap[$type('soft_deletes')->toDoctrine()],
        'time_tz'                 => $defaultTypesMap[$type('time_tz')->toDoctrine()],
        'time'                    => $defaultTypesMap[$type('time')->toDoctrine()],
        'timestamp_tz'            => $defaultTypesMap[$type('timestamp_tz')->toDoctrine()],
        'timestamp'               => $defaultTypesMap[$type('timestamp')->toDoctrine()],
        'tiny_integer'            => $defaultTypesMap[$type('tiny_integer')->toDoctrine()],
        'unsigned_big_integer'    => $defaultTypesMap[$type('unsigned_big_integer')->toDoctrine()],
        'unsigned_decimal'        => $defaultTypesMap[$type('unsigned_decimal')->toDoctrine()] . ":{$decimalPrecision}",
        'unsigned_integer'        => $defaultTypesMap[$type('unsigned_integer')->toDoctrine()],
        'unsigned_medium_integer' => $defaultTypesMap[$type('unsigned_medium_integer')->toDoctrine()],
        'unsigned_small_integer'  => $defaultTypesMap[$type('unsigned_small_integer')->toDoctrine()],
        'unsigned_tiny_integer'   => $defaultTypesMap[$type('unsigned_tiny_integer')->toDoctrine()],
        'year'                    => $defaultTypesMap[$type('year')->toDoctrine()],
    ]);
});

test('HasAutoCasting@getCasts', function (): void {
    $defaultTypesMap = config('auto-model-cast.default_types_map');
    $columns = Inspect::columns('entities');
    $type = static fn (string $columnName): Type => $columns->get($columnName)->type;

    $decimalPrecision = $columns->get('decimal')->precision;

    expect((new Entity())->getCasts())->toEqual([
        'id'                      => 'should not be overwritten',
        'big_integer'             => $defaultTypesMap[$type('big_integer')->toDoctrine()],
        'binary'                  => $defaultTypesMap[$type('binary')->toDoctrine()],
        'boolean'                 => $defaultTypesMap[$type('boolean')->toDoctrine()],
        'date_time_tz'            => $defaultTypesMap[$type('date_time_tz')->toDoctrine()],
        'date_time'               => $defaultTypesMap[$type('date_time')->toDoctrine()],
        'date'                    => $defaultTypesMap[$type('date')->toDoctrine()],
        'decimal'                 => $defaultTypesMap[$type('decimal')->toDoctrine()] . ":{$decimalPrecision}",
        'double'                  => $defaultTypesMap[$type('double')->toDoctrine()],
        'float'                   => $defaultTypesMap[$type('float')->toDoctrine()],
        'integer'                 => $defaultTypesMap[$type('integer')->toDoctrine()],
        'json'                    => $defaultTypesMap[$type('json')->toDoctrine()],
        'jsonb'                   => $defaultTypesMap[$type('jsonb')->toDoctrine()],
        'medium_integer'          => $defaultTypesMap[$type('medium_integer')->toDoctrine()],
        'small_integer'           => $defaultTypesMap[$type('small_integer')->toDoctrine()],
        'soft_deletes_tz'         => $defaultTypesMap[$type('soft_deletes_tz')->toDoctrine()],
        'soft_deletes'            => $defaultTypesMap[$type('soft_deletes')->toDoctrine()],
        'time_tz'                 => $defaultTypesMap[$type('time_tz')->toDoctrine()],
        'time'                    => $defaultTypesMap[$type('time')->toDoctrine()],
        'timestamp_tz'            => $defaultTypesMap[$type('timestamp_tz')->toDoctrine()],
        'timestamp'               => $defaultTypesMap[$type('timestamp')->toDoctrine()],
        'tiny_integer'            => $defaultTypesMap[$type('tiny_integer')->toDoctrine()],
        'unsigned_big_integer'    => $defaultTypesMap[$type('unsigned_big_integer')->toDoctrine()],
        'unsigned_decimal'        => $defaultTypesMap[$type('unsigned_decimal')->toDoctrine()] . ":{$decimalPrecision}",
        'unsigned_integer'        => $defaultTypesMap[$type('unsigned_integer')->toDoctrine()],
        'unsigned_medium_integer' => $defaultTypesMap[$type('unsigned_medium_integer')->toDoctrine()],
        'unsigned_small_integer'  => $defaultTypesMap[$type('unsigned_small_integer')->toDoctrine()],
        'unsigned_tiny_integer'   => $defaultTypesMap[$type('unsigned_tiny_integer')->toDoctrine()],
        'year'                    => $defaultTypesMap[$type('year')->toDoctrine()],
    ]);
});

test('the model casts are correctly applied', function (): void {
    Carbon::setTestNow('2000-01-01 00:00:00');

    $now = now();

    $entity = (new Entity());

    /**
     * TODO: Improve on this somehow!
     *
     * Having to re-cast the "year" column of type `YEAR`
     * because Doctrine maps `YEAR` types into `DATE`
     * types, therefore the "year" column is cast as
     * being a `DATE`, turned into a Carbon instance,
     * stringified as a full on date string when inserted
     * into the DB, thus, creating an error.
     */
    $entity->mergeCasts([
        ...$entity->getCasts(),
        'year' => 'int',
    ]);

    /**
     * Re-casting the "id" column into an `INT`
     * because, "should not be overwritten" is
     * of course not a valid cast type.
     */
    $entity->mergeCasts([
        ...$entity->getCasts(),
        'id' => 'int',
    ]);

    $entity->forceFill([
        'big_integer'             => '123456789',
        'binary'                  => '010101',
        'boolean'                 => '0',
        'date_time_tz'            => $now,
        'date_time'               => $now,
        'date'                    => $now,
        'decimal'                 => '1.1',
        'double'                  => '4.56',
        'float'                   => '7.89',
        'integer'                 => '123',
        'json'                    => ['hey'],
        'jsonb'                   => ['hey'],
        'medium_integer'          => '456',
        'small_integer'           => '7',
        'soft_deletes_tz'         => $now,
        'soft_deletes'            => $now,
        'time_tz'                 => $now,
        'time'                    => $now,
        'timestamp_tz'            => $now,
        'timestamp'               => $now,
        'tiny_integer'            => '0',
        'unsigned_big_integer'    => '987654321',
        'unsigned_decimal'        => '12.3',
        'unsigned_integer'        => '789',
        'unsigned_medium_integer' => '456',
        'unsigned_small_integer'  => '123',
        'unsigned_tiny_integer'   => '1',
        'year'                    => '2023',

        // Ignored string columns
        'char'                    => 'char',
        'ip_address'              => '127.0.0.1',
        'long_text'               => 'long_text',
        'medium_text'             => 'medium_text',
        'string'                  => 'string',
        'text'                    => 'text',
        'tiny_text'               => 'tiny_text',
        'ulid'                    => 'ulid',
        'uuid'                    => 'uuid',
    ])->save();

    $actual = $entity->toArray();
    $expected = [
        'big_integer'             => 123456789,
        'binary'                  => '010101',
        'boolean'                 => false,
        'date_time_tz'            => $now->toJSON(),
        'date_time'               => $now->toJSON(),
        'date'                    => $now->toJSON(),
        'decimal'                 => '1.10000000',
        'double'                  => 4.56,
        'float'                   => 7.89,
        'integer'                 => 123,
        'json'                    => ['hey'],
        'jsonb'                   => ['hey'],
        'medium_integer'          => 456,
        'small_integer'           => 7,
        'soft_deletes_tz'         => $now->toJSON(),
        'soft_deletes'            => $now->toJSON(),
        'time_tz'                 => $now->toDateTimeString(),
        'time'                    => $now->toDateTimeString(),
        'timestamp_tz'            => $now->toJSON(),
        'timestamp'               => $now->toJSON(),
        'tiny_integer'            => false,
        'unsigned_big_integer'    => 987654321,
        'unsigned_decimal'        => '12.30000000',
        'unsigned_integer'        => 789,
        'unsigned_medium_integer' => 456,
        'unsigned_small_integer'  => 123,
        'unsigned_tiny_integer'   => true,
        'year'                    => 2023,

        // Unhandled columns (string & default timestamps)
        'char'                    => 'char',
        'ip_address'              => '127.0.0.1',
        'long_text'               => 'long_text',
        'medium_text'             => 'medium_text',
        'string'                  => 'string',
        'text'                    => 'text',
        'tiny_text'               => 'tiny_text',
        'ulid'                    => 'ulid',
        'uuid'                    => 'uuid',
        'updated_at'              => $entity->updated_at->toJSON(),
        'created_at'              => $entity->created_at->toJSON(),
        'id'                      => (int) $entity->id,
    ];

    expect($actual)->toEqual($expected);
    expect($actual)->toEqualCanonicalizing($expected);
    expect(json_encode($actual))->toEqual(json_encode($expected));
    expect($actual === $expected)->toEqual(true);

    // Reset
    Carbon::setTestNow();
});
