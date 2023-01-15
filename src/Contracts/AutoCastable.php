<?php

declare(strict_types=1);

namespace VicGutt\AutoModelCast\Contracts;

interface AutoCastable
{
    /**
     * Initialize the "HasAutoCasting" trait for an instance.
     */
    public function initializeHasAutoCasting(): void;

    /**
     * Retrieves the auto casts for a given model.
     */
    public function getAutoCasts(): array;
}
