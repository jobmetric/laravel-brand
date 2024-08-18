<?php

namespace JobMetric\Brand\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model getObject(int $brand_id)
 * @method static \Spatie\QueryBuilder\QueryBuilder query(array $filter = [], array $with = [])
 * @method static \Illuminate\Http\Resources\Json\AnonymousResourceCollection paginate(array $filter = [], int $page_limit = 15, array $with = [])
 * @method static \Illuminate\Http\Resources\Json\AnonymousResourceCollection all(array $filter = [], array $with = [])
 * @method static array get(int $brand_id, array $with = [], string $locale = null)
 * @method static array store(array $data)
 * @method static array update(int $brand_id, array $data)
 * @method static array delete(int $brand_id)
 * @method static array restore(int $brand_id)
 * @method static array forceDelete(int $brand_id)
 * @method static array usedIn(int $brand_id)
 * @method static bool hasUsed(int $brand_id)
 *
 * @see \JobMetric\Brand\Brand
 */
class Brand extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \JobMetric\Brand\Brand::class;
    }
}
