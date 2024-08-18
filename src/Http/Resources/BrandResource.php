<?php

namespace JobMetric\Brand\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JobMetric\Brand\Models\BrandRelation;

/**
 * @property mixed id
 * @property mixed status
 * @property mixed ordering
 * @property mixed visits
 * @property mixed likes
 * @property mixed deleted_at
 * @property mixed created_at
 * @property mixed updated_at
 *
 * @property mixed translations
 * @property BrandRelation brandRelations
 */
class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        global $translationLocale;

        return [
            'id' => $this->id,
            'status' => $this->status,
            'ordering' => $this->ordering,
            'visits' => $this->visits,
            'likes' => $this->likes,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'translations' => translationResourceData($this->translations, $translationLocale),

            'brandRelations' => $this->whenLoaded('brandRelations', function () {
                return BrandRelationResource::collection($this->brandRelations);
            }),
        ];
    }
}
