# Automatically cast model attributes based on database column types

[![GitHub Tests Action Status](https://github.com/vicgutt/laravel-auto-model-cast/actions/workflows/run-tests.yml/badge.svg)](https://github.com/vicgutt/laravel-auto-model-cast/actions/workflows/run-tests.yml)
[![GitHub PHPStan Action Status](https://github.com/vicgutt/laravel-auto-model-cast/actions/workflows/phpstan.yml/badge.svg)](https://github.com/vicgutt/laravel-auto-model-cast/actions/workflows/phpstan.yml)
[![GitHub Code Style Action Status](https://github.com/vicgutt/laravel-auto-model-cast/actions/workflows/fix-php-code-style-issues.yml/badge.svg)](https://github.com/vicgutt/laravel-auto-model-cast/actions/workflows/fix-php-code-style-issues.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/vicgutt/laravel-auto-model-cast.svg?style=flat-square)](https://packagist.org/packages/vicgutt/laravel-auto-model-cast)
[![Total Downloads](https://img.shields.io/packagist/dt/vicgutt/laravel-auto-model-cast.svg?style=flat-square)](https://packagist.org/packages/vicgutt/laravel-auto-model-cast)

---

This package allows you, by inspecting your database columns, to automatically cast your model attributes.

## Installation

You can install the package via composer:

```bash
composer require vicgutt/laravel-auto-model-cast
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-auto-model-cast-config"
```

The contents of the published config file can be seen here: [config/auto-model-cast.php](/config/auto-model-cast.php).

## How it works

The package works by finding all your project's models and determines their attributes cast types based on their database columns types.

Here's the steps taken:

-   Attempts to find all your project's models based on a given directory, base project path and base project namespace _(using [`vicgutt/laravel-models-finder`](https://github.com/vicgutt/laravel-models-finder))_.
-   For each model, extracts the database table name and the connection used.
-   For each table for a given connection, extracts all columns _(using [`vicgutt/laravel-inspect-db`](https://github.com/VicGUTT/laravel-inspect-db))_.
-   For each column, extracts the column's type based on it's schema _(using [`vicgutt/laravel-inspect-db`](https://github.com/VicGUTT/laravel-inspect-db), which in turn uses [`doctrine/dbal`](https://github.com/doctrine/dbal))_.
-   For each column type, [attempts to map](/src/Support/TypeMapper.php) the type into a Laravel [supported cast type](/src/Enums/CastTypeEnum.php).
-   Finally, prepends the cast types to the model's [`protected $casts`](https://laravel.com/docs/eloquent-mutators#attribute-casting) array.

> **Note**
>
> Please take a look at the "[Gotchas](#gotchas)" section at the end of the documentation.

### Caching

All the auto-casting fonctionality is done within the config file and the result of which is retrieved from the config file.
Meaning, the cast types for each models are stored in the config file and when a given model needs to retrieve it's casting information it get's it from the config file.

The main benefit of this behaviour is to prevent constantly inspecting the database for the same static and rarely changing information of column names and types.
But this also means, once the project's config files are cached, no newlly added columns will be taken into account unless the config cache is cleared.

## Usage

### Preparing your model

Your models need to opt-into the auto-casting behaviour by implementing the `AutoCastable` interface and using the `HasAutoCasting` trait:

```php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use VicGutt\AutoModelCast\Contracts\AutoCastable;
use VicGutt\AutoModelCast\Concerns\HasAutoCasting;

final class MyModel extends Model implements AutoCastable
{
    use HasAutoCasting;
}
```

### Overwritting auto-casts

You may continue casting your model attributes as usual via the `protected $casts` property array. Those values will take precedence over any determined auto-cast.

As an example, say you have the following column and defined casting:

```php
// a migration file
Schema::create('examples', function (Blueprint $table): void {
    $table->json('extras');
});

// a model file
use Illuminate\Database\Eloquent\Casts\AsCollection;

final class Example extends Model implements AutoCastable
{
    use HasAutoCasting;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'extras' => AsCollection::class,
    ];
}
```

By default, when using one of the provided types maps _(`TypeMapper::defaults()` or `TypeMapper::opinionated()`)_, any `json` column type will be auto-casted to `json`, or `Illuminate\Database\Eloquent\Casts\AsArrayObject`.
Essentially doing:

```php
protected $casts = [
    'extras' => 'json', // or AsArrayObject::class
];
```

But since the model specifies it's own casting for the `extras` attribute, the specied value will be used instead.
Therefore, the `extras` attribute of the `Example` model will be casted to `AsCollection`.

### Discovering models

Arguments may be passed to the underlaying `VicGutt\ModelsFinder\ModelsFinder` class, via the `discoverModelsUsing` method of the `Casts` class to customize how models are discovered.

Example:

```php
Casts::new()->discoverModelsUsing(
    directory: app_path('Models'),
    basePath: app_path(),
    baseNamespace: 'App',
);
```

By default, the package will search for models in the `app/Models` directory.

For more information on the `ModelsFinder` class, please refer to it's documentation here: [https://github.com/vicgutt/laravel-models-finder](https://github.com/vicgutt/laravel-models-finder).

### Creating a custom type mapper

A "type mapper" is simply a class responsable for mapping a given Doctrine column type into a built-in Laravel cast type.
The mapper takes a column in, and spits a cast type out.

To create your own mapper, create a class extending the provided `VicGutt\AutoModelCast\Support\TypeMapper` and overwrite any of the given methods as needed.
To use your newly created mapper, pass it's fully qualified class name to the `useTypeMapper` method of the `Casts` class.

Example:

```php
use VicGutt\AutoModelCast\Support\TypeMapper;

final class MyCustomTypeMapper extends TypeMapper
{
    //
}

Casts::new()->useTypeMapper(MyCustomTypeMapper::class);
```

### Creating custom casters

A "caster" is simply a class responsable for converting a given collection of database columns into an array of supported Laravel cast types.

To create your own caster, create a class extending the provided `VicGutt\AutoModelCast\Support\Casters\BaseCaster` and implement the `handle` method as instructed by the `BaseCaster`.

Example:

```php
use VicGutt\InspectDb\Entities\Column;
use Illuminate\Database\Eloquent\Model;
use VicGutt\AutoModelCast\Support\Casters\BaseCaster;

final class MyCustomCaster extends BaseCaster
{
    public function handle(Column $column, Model $model): ?string
    {
       //
    }
}
```

### Specifying a default caster

To use a custom caster by default, for all models, pass your caster's fully qualified class name to the `useDefaultCaster` method of the `Casts` class.

Example:

```php
Casts::new()->useDefaultCaster(MyCustomCaster::class);
```

### Specifying custom casters per model

To use a custom caster for a given model, provide an array to the `withCustomCasters` method of the `Casts` class.
The keys of the array should be fully qualified class name of models, and the values fully qualified class name of custom casters.

Any model not specied in the array will use the default caster.

Example:

```php
Casts::new()->withCustomCasters([
    \App\Models\User::class => \App\Support\AutoCast\Casters\UserCustomCaster::class,
]);
```

### Specifying a default types map

A "types map" is simply an array of Doctrine type as keys and Laravel cast type as values.
It is assentially a barebones version of "Mapper"s as explained above. It allows for easy and quick customization of the mappings between Doctrine types and Laravel cast types without needing to implement a custom class.

In fact, the mappers make use of the provided types map under the hood.

To provide your own types map to be used by mappers by defaul, simply pass the array to the `withDefaultTypesMap` method of the `Casts` class.

Example:

```php
Casts::new()->withDefaultTypesMap([
    DoctrineTypeEnum::BIGINT->value => CastTypeEnum::INT->value,
    DoctrineTypeEnum::DATE->value => CastTypeEnum::IMMUTABLE_DATE->value,
    DoctrineTypeEnum::JSON->value => CastTypeEnum::AS_ARRAY_OBJECT_CLASS->value,
]);
```

For convenience, two types maps are provided:

-   `VicGutt\AutoModelCast\Support\TypeMapper\TypeMapper::opinionated()`
-   `VicGutt\AutoModelCast\Support\TypeMapper\TypeMapper::defaults()`

#### Defaults

Here, the Doctrine column types are simply mapped to their homologous Laravel cast type.

#### Opinionated

Here, we extend the defaults while providing a sensible opinionated mapping.
Essentially casting any date related types to be immutable.

### Retrieving a model's auto-casts

If you'd like to retrieve the auto-casts for a given model, you have three options:

-   Use the `getAutoCasts` method from a model's instance
-   Use the `for` static method of the `Casts` class.
-   Use the config.

Example:

```php
$model = new MyModel;

$casts = $model->getAutoCasts();

// Or
$casts = Casts::for($model::class);

// Or
$casts = Casts::for($model);

// Or
$casts = config("auto-model-cast.casts.{$model::class}", []);
```

The returned array will have the model's attribute names as keys and the cast types as values.

> **Note**
>
> Under the hood the helper methods make use of the config file.
> The methods can therefore only be used after the config file has been set up.

## Gotchas

### Column types dependent on the database platform

Depending on the batabase platform being used, certain column types may be detected differrently by the underlaying `doctrine/dbal`, resulting in inconsistent attribute casting.
As an example, the `json` column type will most likely be cast as `string` in SQLite, whereas in MySQL or Postgres it will be cast as `array`.

This is obviously not an issue if you do not intend to change database platform mid-"project".

### `doctrine/dbal`'s type translation

In order to implement database independent applications, `doctrine/dbal` has a type translation system baked in that supports the conversion from and to PHP values from any database platform.
This works great in most cases but also results in data loss for our use-case as we don't have access to the actual native column type but only the translation.

As an example, using MySQL, the `TINYINT(1)` type will be translated to and returned as `boolean` which is usualy what we want and in this case we could easily figure out the native column type.
But an example of an undesired case is the translation of the `YEAR` column type and it being returned as `date`. This is problematique in our use-case as `YEAR` columns only accept four digit values _(or 2 digits before MySQL 8.0)_ either as strings or integers.

Example:

```php
Schema::create('...', function (Blueprint $table): void {
    $table->year('birth_year');
    $table->year('adulthood_year');
});

//

$model->create([
    'birth_year' => '2000',
    'adulthood_year' => 2018,
]);
```

In the example above however, if the two `birth_year` and `adulthood_year` attributes were to be auto-casted, they would be casted as `date`, turned into Carbon instances by Laravel with a value of `1970-01-01T00:00:00.000000Z` and would cause an exception to be rightfully thrown by the database when the values are trying to be inserted in.

This is an issue I would like to revisit at a later time.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

If you're interested in contributing to the project, please read our [contributing docs](https://github.com/vicgutt/laravel-auto-model-cast/blob/main/.github/CONTRIBUTING.md) **before submitting a pull request**.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Victor GUTT](https://github.com/vicgutt)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
