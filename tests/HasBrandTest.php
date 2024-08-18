<?php

namespace JobMetric\Brand\Tests;

use App\Models\Product;
use Throwable;

class HasBrandTest extends BaseBrand
{
    /**
     * @throws Throwable
     */
    public function test_brands_trait_relationship()
    {
        $product = new Product();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphOne::class, $product->brand());
    }

    /**
     * @throws Throwable
     */
    public function test_attach(): void
    {
        $product = $this->create_product();

        $brand = $this->create_brand_for_has();

        $attach = $product->attachBrand($brand->id);

        $this->assertIsArray($attach);

        $this->assertDatabaseHas(config('brand.tables.brand_relation'), [
            'brand_id' => $brand->id,
            'brandable_id' => $product->id,
            'brandable_type' => Product::class,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function test_detach(): void
    {
        $product = $this->create_product();

        $brand = $this->create_brand_for_has();

        $product->attachBrand($brand->id);

        $detach = $product->detachBrand($brand->id);

        $this->assertIsArray($detach);

        $this->assertDatabaseMissing(config('brand.tables.brand_relation'), [
            'brand_id' => $brand->id,
            'brandable_id' => $product->id,
            'brandable_type' => Product::class
        ]);
    }
}
