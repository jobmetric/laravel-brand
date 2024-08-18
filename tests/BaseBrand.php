<?php

namespace JobMetric\Brand\Tests;

use App\Models\Product;
use JobMetric\Brand\Facades\Brand;
use JobMetric\Brand\Models\Brand as BrandModels;
use Tests\BaseDatabaseTestCase as BaseTestCase;

class BaseBrand extends BaseTestCase
{
    /**
     * create a fake product
     *
     * @return Product
     */
    public function create_product(): Product
    {
        return Product::factory()->create();
    }

    /**
     * create a fake brand
     *
     * @return array
     */
    public function create_brand(): array
    {
        return Brand::store([
            'status' => true,
            'ordering' => 0,
            'translation' => [
                'name' => 'apple',
                'description' => 'apple description',
                'meta_title' => 'apple meta title',
                'meta_description' => 'apple meta description',
                'meta_keywords' => 'apple meta keywords',
            ],
        ]);
    }

    /**
     * create a fake brand
     *
     * @return BrandModels
     */
    public function create_brand_for_has(): BrandModels
    {
        Brand::store([
            'status' => true,
            'ordering' => 0,
            'translation' => [
                'name' => 'apple',
                'description' => 'apple description',
                'meta_title' => 'apple meta title',
                'meta_description' => 'apple meta description',
                'meta_keywords' => 'apple meta keywords',
            ],
        ]);

        return BrandModels::find(1);
    }
}
