<?php

declare(strict_types=1);

namespace VicGutt\AutoModelCast\Tests;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use VicGutt\AutoModelCast\Support\Casts;
use VicGutt\AutoModelCast\Support\TypeMapper;
use Orchestra\Testbench\TestCase as Orchestra;
use VicGutt\AutoModelCast\AutoModelCastServiceProvider;
use VicGutt\AutoModelCast\Support\Casters\DefaultCaster;

abstract class TestCase extends Orchestra
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'mysql');
        config()->set('database.connections.mysql', [
            ...config('database.connections.mysql'),
            'database' => env('DB_MYSQL_DATABASE', 'laravel_auto_model_cast_testing'),
            'username' => env('DB_MYSQL_USER', 'root'),
            'password' => env('DB_MYSQL_PASSWORD', null),
        ]);

        $this->initPackageConfig();
        $this->loadMigrations();
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            AutoModelCastServiceProvider::class,
        ];
    }

    protected function initPackageConfig(): void
    {
        config()->set(
            'auto-model-cast',
            Casts::new()
                ->discoverModelsUsing(...$this->modelsFinderParams())
                ->useTypeMapper(TypeMapper::class)
                ->useDefaultCaster(DefaultCaster::class)
                ->withDefaultTypesMap(TypeMapper::opinionated())
                ->withCustomCasters([
                    // \App\Models\User::class => \App\Support\AutoCast\Casters\UserCustomCaster::class
                ])
                ->toArray(),
        );
    }

    protected function loadMigrations(): void
    {
        // Schema::dropAllTables();

        foreach (File::files($this->getTestSupportDirectory('/database/migrations')) as $file) {
            $tableName = Str::between($file->getFilename(), 'create_', '_table');

            if (Schema::hasTable($tableName)) {
                continue;
            }

            (include $file->getRealPath())->up();
        }
    }

    protected function getModelsDirectory(string $path = ''): string
    {
        return $this->getAppDirectory("/Models/{$path}");
    }

    protected function getAppDirectory(string $path = ''): string
    {
        return $this->getTestSupportDirectory("/app/{$path}");
    }

    protected function getTestSupportDirectory(string $path = ''): string
    {
        return $this->getTestDirectory("/TestSupport/{$path}");
    }

    protected function getTestDirectory(string $path = ''): string
    {
        return str_replace(['\\', '//'], '/', realpath(__DIR__) . '/' . $path);
    }

    protected function modelsFinderParams(): array
    {
        return [
            'directory' => $this->getModelsDirectory(),
            'basePath' => $this->getAppDirectory(),
            'baseNamespace' => 'VicGutt\AutoModelCast\Tests\TestSupport\app',
        ];
    }
}
