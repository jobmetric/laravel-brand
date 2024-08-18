<?php

namespace JobMetric\Brand;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use JobMetric\Brand\Exceptions\BrandNotFoundException;
use JobMetric\Brand\Http\Resources\BrandResource;
use JobMetric\Brand\Models\Brand;
use JobMetric\Brand\Models\BrandRelation;
use Throwable;

/**
 * Trait HasBrand
 *
 * @package JobMetric\Brand
 *
 * @property Brand[] brands
 *
 * @method MorphOne(string $class, string $string)
 */
trait HasBrand
{
    /**
     * brand relationship
     *
     * @return MorphOne
     */
    public function brand(): MorphOne
    {
        return $this->MorphOne(BrandRelation::class, 'brandable');
    }

    /**
     * attach brand
     *
     * @param int $brand_id
     *
     * @return array
     * @throws Throwable
     */
    public function attachBrand(int $brand_id): array
    {
        /**
         * @var Brand $brand
         */
        $brand = Brand::find($brand_id);

        if (!$brand) {
            throw new BrandNotFoundException($brand_id);
        }

        BrandRelation::query()->updateOrInsert([
            'brandable_id' => $this->id,
            'brandable_type' => get_class($this),
        ], [
            'brand_id' => $brand_id,
        ]);

        return [
            'ok' => true,
            'message' => trans('brand::base.messages.attached'),
            'data' => BrandResource::make($brand),
            'status' => 200
        ];
    }

    /**
     * detach brand
     *
     * @param int $brand_id
     *
     * @return array
     * @throws Throwable
     */
    public function detachBrand(int $brand_id): array
    {
        /**
         * @var Brand $brand
         */
        $brand = Brand::find($brand_id);

        if (!$brand) {
            throw new BrandNotFoundException($brand_id);
        }

        $data = BrandResource::make($brand);

        $this->brand()->delete();

        return [
            'ok' => true,
            'message' => trans('brand::base.messages.detached'),
            'data' => $data,
            'status' => 200
        ];
    }
}
