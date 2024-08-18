<?php

namespace JobMetric\Brand\Events;

use JobMetric\Brand\Models\Brand;

class BrandUpdateEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Brand $brand,
        public readonly array $data
    )
    {
    }
}
