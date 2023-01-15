<?php

declare(strict_types=1);

namespace VicGutt\AutoModelCast\Enums;

use VicGutt\PhpEnhancedEnum\Concerns\Enumerable;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\AsStringable;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use VicGutt\PhpEnhancedEnum\Contracts\EnumerableContract;
use Illuminate\Database\Eloquent\Casts\AsEncryptedCollection;
use Illuminate\Database\Eloquent\Casts\AsEncryptedArrayObject;

/**
 * The built-in cast types supported by Eloquent.
 *
 * @see https://laravel.com/docs/9.x/eloquent-mutators#attribute-casting
 * @see vendor/laravel/framework/src/Illuminate/Database/Eloquent/Concerns/HasAttributes.php::primitiveCastTypes
 * @see vendor/laravel/framework/src/Illuminate/Database/Eloquent/Concerns/HasAttributes.php@castAttribute
 */
enum CastTypeEnum: string implements EnumerableContract
{
    use Enumerable;

    case ARRAY = 'array';
    case BOOLEAN = 'boolean';
    case COLLECTION = 'collection';
    case DATE = 'date';
    case DATETIME = 'datetime';
    case IMMUTABLE_DATE = 'immutable_date';
    case IMMUTABLE_DATETIME = 'immutable_datetime';
    case DOUBLE = 'double';
    case FLOAT = 'float';
    case INTEGER = 'integer';
    case OBJECT = 'object';
    case REAL = 'real';
    case STRING = 'string';
    case TIMESTAMP = 'timestamp';

    /**
     * Parametizables.
     */
    case DECIMAL = 'decimal'; // 'decimal:<precision>'
    case ENCRYPTED = 'encrypted';
    case ENCRYPTED_ARRAY = 'encrypted:array';
    case ENCRYPTED_JSON = 'encrypted:json';
    case ENCRYPTED_COLLECTION = 'encrypted:collection';
    case ENCRYPTED_OBJECT = 'encrypted:object';

    /**
     * Aliases.
     */
    case INT = 'int';
    case BOOL = 'bool';
    case JSON = 'json';
    case CUSTOM_DATETIME = 'custom_datetime';
    case IMMUTABLE_CUSTOM_DATETIME = 'immutable_custom_datetime';

    /**
     * Castables.
     */
    case AS_STRINGABLE_CLASS = AsStringable::class;
    case AS_ARRAY_OBJECT_CLASS = AsArrayObject::class;
    case AS_COLLECTION_CLASS = AsCollection::class;
    case AS_ENCRYPTED_ARRAY_OBJECT_CLASS = AsEncryptedArrayObject::class;
    case AS_ENCRYPTED_COLLECTION_CLASS = AsEncryptedCollection::class;
}
