<?php

namespace JobMetric\Brand\Events;

class BrandableResourceEvent
{
    /**
     * The brandable model instance.
     *
     * @var mixed
     */
    public mixed $brandable;

    /**
     * The resource to be filled by the listener.
     *
     * @var mixed|null
     */
    public mixed $resource;

    /**
     * Create a new event instance.
     *
     * @param mixed $brandable
     */
    public function __construct(mixed $brandable)
    {
        $this->brandable = $brandable;
        $this->resource = null;
    }
}
