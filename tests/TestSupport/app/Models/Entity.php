<?php

declare(strict_types=1);

namespace VicGutt\AutoModelCast\Tests\TestSupport\app\Models;

use Illuminate\Database\Eloquent\Model;
use VicGutt\AutoModelCast\Contracts\AutoCastable;
use VicGutt\AutoModelCast\Concerns\HasAutoCasting;

final class Entity extends Model implements AutoCastable
{
    use HasAutoCasting;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'should not be overwritten',
    ];
}
