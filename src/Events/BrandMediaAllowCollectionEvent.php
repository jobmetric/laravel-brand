<?php

namespace JobMetric\Brand\Events;

class BrandMediaAllowCollectionEvent
{
    /**
     * The tay media allow the collection to be filled by the listener.
     *
     * @var array
     */
    public array $mediaAllowCollection = [];

    /**
     * Create a new event instance.
     *
     * @param array $defaultBrandMediaAllowCollection
     */
    public function __construct(array $defaultBrandMediaAllowCollection = [])
    {
        $this->mediaAllowCollection = $defaultBrandMediaAllowCollection;
    }

    /**
     * Add a media allow collection.
     *
     * @param array $mediaAllowCollection
     * Example: [
     *      'base' => [
     *          'media_collection' => 'public',
     *          'size' => [
     *              'default' => [
     *                  'w' => config('brand.default_image_size.width'),
     *                  'h' => config('brand.default_image_size.height'),
     *              ]
     *          ]
     *      ],
     *  ]
     *
     * @return static
     */
    public function AddMediaAllowCollection(array $mediaAllowCollection): static
    {
        if (!in_array($mediaAllowCollection, $this->mediaAllowCollection)) {
            $this->mediaAllowCollection = array_merge($this->mediaAllowCollection, $mediaAllowCollection);
        }

        return $this;
    }
}
