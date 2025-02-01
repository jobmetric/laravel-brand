<?php

namespace JobMetric\Brand\Tests;

use JobMetric\Brand\Exceptions\BrandNotFoundException;
use JobMetric\Brand\Facades\Brand;
use JobMetric\Brand\Http\Resources\BrandRelationResource;
use JobMetric\Brand\Http\Resources\BrandResource;
use Throwable;

class BrandTest extends BaseBrand
{
    /**
     * @throws Throwable
     */
    public function test_store()
    {
        // store a brand
        $brand = $this->create_brand();

        $this->assertIsArray($brand);
        $this->assertTrue($brand['ok']);
        $this->assertEquals($brand['message'], trans('brand::base.messages.created'));
        $this->assertInstanceOf(BrandResource::class, $brand['data']);
        $this->assertEquals(201, $brand['status']);

        $this->assertDatabaseHas('brands', [
            'id' => $brand['data']->id,
            'status' => true,
            'ordering' => 0
        ]);

        $this->assertDatabaseHas('translations', [
            'translatable_type' => 'JobMetric\Brand\Models\Brand',
            'translatable_id' => $brand['data']->id,
            'locale' => app()->getLocale(),
            'key' => 'name',
            'value' => 'apple',
        ]);

        // store duplicate name
        $brand = $this->create_brand();

        $this->assertIsArray($brand);
        $this->assertFalse($brand['ok']);
        $this->assertEquals($brand['message'], trans('package-core::base.validation.errors'));
        $this->assertEquals(422, $brand['status']);
    }

    /**
     * @throws Throwable
     */
    public function test_update()
    {
        // brand not found
        try {
            $brand = Brand::update(1000, [
                'status' => true,
            ]);

            $this->assertIsArray($brand);
        } catch (Throwable $e) {
            $this->assertInstanceOf(BrandNotFoundException::class, $e);
        }

        // store a brand
        $brandStore = $this->create_brand();

        // update with another name
        $brand = Brand::update($brandStore['data']->id, [
            'status' => false,
            'translation' => [
                'name' => 'LG'
            ],
        ]);

        $this->assertIsArray($brand);
        $this->assertTrue($brand['ok']);
        $this->assertEquals($brand['message'], trans('brand::base.messages.updated'));
        $this->assertInstanceOf(BrandResource::class, $brand['data']);
        $this->assertEquals(200, $brand['status']);

        $this->assertDatabaseHas('brands', [
            'id' => $brand['data']->id,
            'status' => false,
        ]);

        $this->assertDatabaseHas('translations', [
            'translatable_type' => 'JobMetric\Brand\Models\Brand',
            'translatable_id' => $brand['data']->id,
            'locale' => app()->getLocale(),
            'key' => 'name',
            'value' => 'LG',
        ]);
    }

    /**
     * @throws Throwable
     */
    public function test_get()
    {
        // store a brand
        $brandStore = $this->create_brand();

        // get the brand
        $brand = Brand::get($brandStore['data']->id);

        $this->assertIsArray($brand);
        $this->assertTrue($brand['ok']);
        $this->assertEquals($brand['message'], trans('brand::base.messages.found'));
        $this->assertInstanceOf(BrandResource::class, $brand['data']);
        $this->assertEquals(200, $brand['status']);

        $this->assertEquals($brand['data']->id, $brandStore['data']->id);
        $this->assertTrue($brand['data']->status);

        // get the brand with a wrong id
        try {
            $brand = Brand::get(1000);

            $this->assertIsArray($brand);
        } catch (Throwable $e) {
            $this->assertInstanceOf(BrandNotFoundException::class, $e);
        }
    }

    /**
     * @throws Throwable
     */
    public function test_all()
    {
        // Store a brand
        $this->create_brand();

        // Get the brands
        $getBrands = Brand::all();

        $this->assertCount(1, $getBrands);

        $getBrands->each(function ($brand) {
            $this->assertInstanceOf(BrandResource::class, $brand);
        });
    }

    /**
     * @throws Throwable
     */
    public function test_delete()
    {
        // store brand
        $brandStore = $this->create_brand();

        // delete the brand
        $brand = Brand::delete($brandStore['data']->id);

        $this->assertIsArray($brand);
        $this->assertTrue($brand['ok']);
        $this->assertEquals($brand['message'], trans('brand::base.messages.deleted'));
        $this->assertEquals(200, $brand['status']);

        $this->assertSoftDeleted('brands', [
            'id' => $brandStore['data']->id,
        ]);

        // delete the brand again
        try {
            $brand = Brand::delete($brandStore['data']->id);

            $this->assertIsArray($brand);
        } catch (Throwable $e) {
            $this->assertInstanceOf(BrandNotFoundException::class, $e);
        }
    }

    /**
     * @throws Throwable
     */
    public function test_restore()
    {
        // store brand
        $brandStore = $this->create_brand();

        // delete the brand
        $brand = Brand::delete($brandStore['data']->id);

        // restore the brand
        $brand = Brand::restore($brandStore['data']->id);

        $this->assertIsArray($brand);
        $this->assertTrue($brand['ok']);
        $this->assertEquals($brand['message'], trans('brand::base.messages.restored'));
        $this->assertEquals(200, $brand['status']);

        $this->assertDatabaseHas('brands', [
            'id' => $brandStore['data']->id,
        ]);

        // restore the brand again
        try {
            $brand = Brand::restore($brandStore['data']->id);

            $this->assertIsArray($brand);
        } catch (Throwable $e) {
            $this->assertInstanceOf(BrandNotFoundException::class, $e);
        }
    }

    /**
     * @throws Throwable
     */
    public function test_force_delete()
    {
        // store brand
        $brandStore = $this->create_brand();

        // delete the brand
        $brand = Brand::delete($brandStore['data']->id);

        // force deletes the brand
        $brand = Brand::forceDelete($brandStore['data']->id);

        $this->assertIsArray($brand);
        $this->assertTrue($brand['ok']);
        $this->assertEquals($brand['message'], trans('brand::base.messages.permanently_deleted'));
        $this->assertEquals(200, $brand['status']);

        $this->assertDatabaseMissing('brands', [
            'id' => $brandStore['data']->id,
        ]);

        // restore the brand again
        try {
            $brand = Brand::forceDelete($brandStore['data']->id);

            $this->assertIsArray($brand);
        } catch (Throwable $e) {
            $this->assertInstanceOf(BrandNotFoundException::class, $e);
        }
    }

    /**
     * @throws Throwable
     */
    public function test_pagination()
    {
        // Store a brand
        $this->create_brand();

        // Paginate the brands
        $paginateBrands = Brand::paginate();

        $this->assertCount(1, $paginateBrands);

        $paginateBrands->each(function ($brand) {
            $this->assertInstanceOf(BrandResource::class, $brand);
        });

        $this->assertIsInt($paginateBrands->total());
        $this->assertIsInt($paginateBrands->perPage());
        $this->assertIsInt($paginateBrands->currentPage());
        $this->assertIsInt($paginateBrands->lastPage());
        $this->assertIsArray($paginateBrands->items());
    }

    /**
     * @throws Throwable
     */
    public function test_used_in()
    {
        $product = $this->create_product();

        // Store a brand
        $brandStore = $this->create_brand();

        // Attach the brand to the product
        $attachBrand = $product->attachBrand($brandStore['data']->id);

        $this->assertIsArray($attachBrand);
        $this->assertTrue($attachBrand['ok']);
        $this->assertEquals($attachBrand['message'], trans('brand::base.messages.attached'));
        $this->assertInstanceOf(BrandResource::class, $attachBrand['data']);
        $this->assertEquals(200, $attachBrand['status']);

        // Get the brand used in the product
        $usedIn = Brand::usedIn($brandStore['data']->id);

        $this->assertIsArray($usedIn);
        $this->assertTrue($usedIn['ok']);
        $this->assertEquals($usedIn['message'], trans('brand::base.messages.used_in', [
            'count' => 1
        ]));
        $usedIn['data']->each(function ($dataUsedIn) {
            $this->assertInstanceOf(BrandRelationResource::class, $dataUsedIn);
        });
        $this->assertEquals(200, $usedIn['status']);

        // Get the brand used in the product with a wrong brand id
        try {
            $usedIn = Brand::usedIn(1000);

            $this->assertIsArray($usedIn);
        } catch (Throwable $e) {
            $this->assertInstanceOf(BrandNotFoundException::class, $e);
        }
    }

    /**
     * @throws Throwable
     */
    public function test_has_used()
    {
        $product = $this->create_product();

        // Store a brand
        $brandStore = $this->create_brand();

        // Attach the brand to the product
        $attachBrand = $product->attachBrand($brandStore['data']->id);

        $this->assertIsArray($attachBrand);
        $this->assertTrue($attachBrand['ok']);
        $this->assertEquals($attachBrand['message'], trans('brand::base.messages.attached'));
        $this->assertInstanceOf(BrandResource::class, $attachBrand['data']);
        $this->assertEquals(200, $attachBrand['status']);

        // check has used in
        $usedIn = Brand::hasUsed($brandStore['data']->id);

        $this->assertTrue($usedIn);

        // check with wrong brand id
        try {
            $usedIn = Brand::hasUsed(1000);
        } catch (Throwable $e) {
            $this->assertInstanceOf(BrandNotFoundException::class, $e);
        }
    }
}
