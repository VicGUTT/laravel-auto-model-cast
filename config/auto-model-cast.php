<?php

declare(strict_types=1);

use VicGutt\AutoModelCast\Support\Casts;
use VicGutt\AutoModelCast\Support\TypeMapper;
use VicGutt\AutoModelCast\Support\Casters\DefaultCaster;

return Casts::new()
    ->discoverModelsUsing(
        directory: app_path('Models'),
        basePath: app_path(),
        baseNamespace: 'App',
    )
    ->useTypeMapper(TypeMapper::class)
    ->useDefaultCaster(DefaultCaster::class)
    ->withDefaultTypesMap(TypeMapper::opinionated())
    ->withCustomCasters([
        // \App\Models\User::class => \App\Support\AutoCast\Casters\UserCustomCaster::class
    ])
    ->toArray();
