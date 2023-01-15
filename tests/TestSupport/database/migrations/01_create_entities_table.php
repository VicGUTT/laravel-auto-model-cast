<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entities', function (Blueprint $table): void {
            foreach ($this->availableColumnTypes() as $columnType) {
                if ($this->shouldIgnoreColumnType($columnType)) {
                    continue;
                }

                $columnName = $this->makeColumnNameOutOfColumnType($columnType);
                $columnParams = $this->makeColumnParams($columnType);

                $this->makeColumn($columnType, $columnName, $columnParams, $table);
            }

            $table->timestamps();
        });
    }

    /**
     * https://laravel.com/docs/9.x/migrations#available-column-types.
     */
    private function availableColumnTypes(): array
    {
        return [
            'bigIncrements',
            'bigInteger',
            'binary',
            'boolean',
            'char',
            'dateTimeTz',
            'dateTime',
            'date',
            'decimal',
            'double',
            'enum',
            'float',
            'foreignId',
            'foreignIdFor',
            'foreignUlid',
            'foreignUuid',
            'geometryCollection',
            'geometry',
            'id',
            'increments',
            'integer',
            'ipAddress',
            'json',
            'jsonb',
            'lineString',
            'longText',
            'macAddress',
            'mediumIncrements',
            'mediumInteger',
            'mediumText',
            'morphs',
            'multiLineString',
            'multiPoint',
            'multiPolygon',
            'nullableMorphs',
            'nullableTimestamps',
            'nullableUlidMorphs',
            'nullableUuidMorphs',
            'point',
            'polygon',
            'rememberToken',
            'set',
            'smallIncrements',
            'smallInteger',
            'softDeletesTz',
            'softDeletes',
            'string',
            'text',
            'timeTz',
            'time',
            'timestampTz',
            'timestamp',
            'timestampsTz',
            'timestamps',
            'tinyIncrements',
            'tinyInteger',
            'tinyText',
            'unsignedBigInteger',
            'unsignedDecimal',
            'unsignedInteger',
            'unsignedMediumInteger',
            'unsignedSmallInteger',
            'unsignedTinyInteger',
            'ulidMorphs',
            'uuidMorphs',
            'ulid',
            'uuid',
            'year',
        ];
    }

    private function columnParams(): array
    {
        return [
            // 'enum' => [
            //     'allowed' => [1, 2, 3],
            // ],
            // 'set' => [
            //     'allowed' => [1, 2, 3],
            // ],
        ];
    }

    private function columnTypesToIgnore(): array
    {
        return [
            'bigIncrements',
            'enum',
            'foreignId',
            'foreignIdFor',
            'foreignUlid',
            'foreignUuid',
            'geometryCollection',
            'geometry',
            'increments',
            'lineString',
            'macAddress',
            'mediumIncrements',
            'morphs',
            'multiLineString',
            'multiPoint',
            'multiPolygon',
            'nullableMorphs',
            'nullableTimestamps',
            'nullableUlidMorphs',
            'nullableUuidMorphs',
            'point',
            'polygon',
            'rememberToken',
            'set',
            'smallIncrements',
            'timestampsTz',
            'timestamps',
            'tinyIncrements',
            'ulidMorphs',
            'uuidMorphs',
        ];
    }

    private function shouldIgnoreColumnType(string $columnType): bool
    {
        return in_array($columnType, $this->columnTypesToIgnore(), true);
    }

    private function makeColumnNameOutOfColumnType(string $columnType): string
    {
        return Str::of($columnType)->snake()->value();
    }

    private function makeColumnParams(string $columnType): array
    {
        return $this->columnParams()[$columnType] ?? [];
    }

    private function makeColumn(string $columnType, string $columnName, array $columnParams, Blueprint $table): void
    {
        $table->{$columnType}(...['column' => $columnName, ...$columnParams]);
    }
};
