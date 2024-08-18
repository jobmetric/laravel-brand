<?php

namespace JobMetric\Brand\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JobMetric\Brand\Models\Brand;

/**
 * @property mixed brand_id
 * @property mixed brandable_id
 * @property mixed brandable_type
 * @property mixed created_at
 *
 * @property Brand brand
 * @property mixed brandable
 * @property mixed brandable_resource
 */
class BrandRelationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'brand_id' => $this->brand_id,
            'brandable_id' => $this->brandable_id,
            'brandable_type' => $this->brandable_type,
            'created_at' => $this->created_at,

            'brand' => $this->whenLoaded('brand', function () {
                return new BrandResource($this->brand);
            }),

            'brandable' => $this?->brandable_resource
        ];
    }
}
