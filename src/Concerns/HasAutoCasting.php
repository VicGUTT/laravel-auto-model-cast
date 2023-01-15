<?php

declare(strict_types=1);

namespace VicGutt\AutoModelCast\Concerns;

use VicGutt\AutoModelCast\Support\Casts;

trait HasAutoCasting
{
    /**
     * Initialize the "HasAutoCasting" trait for an instance.
     */
    public function initializeHasAutoCasting(): void
    {
        $this->mergeAutoCastsIntoCasts();
    }

    /**
     * Retrieves the auto casts for a given model.
     */
    public function getAutoCasts(): array
    {
        return Casts::forModel($this);
    }

    /**
     * Merges the retrieved auto casts for a given model
     * into the model's existing casts.
     */
    protected function mergeAutoCastsIntoCasts(): void
    {
        $this->casts = [
            ...$this->getAutoCasts(),
            ...$this->casts,
        ];
    }
}
