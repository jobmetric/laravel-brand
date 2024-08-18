<?php

namespace JobMetric\Brand;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use JobMetric\Brand\Events\BrandDeleteEvent;
use JobMetric\Brand\Events\BrandForceDeleteEvent;
use JobMetric\Brand\Events\BrandRestoreEvent;
use JobMetric\Brand\Events\BrandStoreEvent;
use JobMetric\Brand\Events\BrandUpdateEvent;
use JobMetric\Brand\Exceptions\BrandNotFoundException;
use JobMetric\Brand\Exceptions\BrandUsedInException;
use JobMetric\Brand\Http\Requests\StoreBrandRequest;
use JobMetric\Brand\Http\Requests\UpdateBrandRequest;
use JobMetric\Brand\Http\Resources\BrandRelationResource;
use JobMetric\Brand\Http\Resources\BrandResource;
use JobMetric\Brand\Models\Brand as BrandModel;
use JobMetric\Brand\Models\BrandRelation;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;

class Brand
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected Application $app;

    /**
     * Create a new Translation instance.
     *
     * @param Application $app
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the object brand.
     *
     * @param int $brand_id
     *
     * @return Builder|Model
     * @throws Throwable
     */
    public function getObject(int $brand_id): Builder|Model
    {
        $brand = BrandModel::query()->where('id', $brand_id)->first();

        if (!$brand) {
            throw new BrandNotFoundException($brand_id);
        }

        return $brand;
    }

    /**
     * Get the specified brand.
     *
     * @param array $filter
     * @param array $with
     *
     * @return QueryBuilder
     */
    public function query(array $filter = [], array $with = []): QueryBuilder
    {
        $fields = [
            'id',
            'status',
            'ordering',
            'visits',
            'likes',
            'created_at',
            'updated_at'
        ];

        $query = QueryBuilder::for(BrandModel::class)
            ->allowedFields($fields)
            ->allowedSorts($fields)
            ->allowedFilters($fields)
            ->defaultSort('-id')
            ->where($filter);

        if (!empty($with)) {
            $query->with($with);
        }

        return $query;
    }

    /**
     * Paginate the specified brand.
     *
     * @param array $filter
     * @param int $page_limit
     * @param array $with
     *
     * @return AnonymousResourceCollection
     */
    public function paginate(array $filter = [], int $page_limit = 15, array $with = []): AnonymousResourceCollection
    {
        return BrandResource::collection(
            $this->query($filter, $with)->paginate($page_limit)
        );
    }

    /**
     * Get all brands.
     *
     * @param array $filter
     * @param array $with
     *
     * @return AnonymousResourceCollection
     */
    public function all(array $filter = [], array $with = []): AnonymousResourceCollection
    {
        return BrandResource::collection(
            $this->query($filter, $with)->get()
        );
    }

    /**
     * Get the specified brand.
     *
     * @param int $brand_id
     * @param array $with
     * @param string|null $locale
     *
     * @return array
     * @throws Throwable
     */
    public function get(int $brand_id, array $with = [], string $locale = null): array
    {
        $query = BrandModel::query()
            ->where('id', $brand_id);

        if (!empty($with)) {
            $query->with($with);
        }

        if (!in_array('translations', $with)) {
            $query->with('translations');
        }

        $brand = $query->first();

        if (!$brand) {
            throw new BrandNotFoundException($brand_id);
        }

        global $translationLocale;
        if (!is_null($locale)) {
            $translationLocale = $locale;
        }

        return [
            'ok' => true,
            'message' => trans('brand::base.messages.found'),
            'data' => BrandResource::make($brand),
            'status' => 200
        ];
    }

    /**
     * Store the specified brand.
     *
     * @param array $data
     *
     * @return array
     * @throws Throwable
     */
    public function store(array $data): array
    {
        $validator = Validator::make($data, (new StoreBrandRequest)->rules());
        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return [
                'ok' => false,
                'message' => trans('brand::base.validation.errors'),
                'errors' => $errors,
                'status' => 422
            ];
        } else {
            $data = $validator->validated();
        }

        return DB::transaction(function () use ($data) {
            $brand = new BrandModel;
            $brand->status = $data['status'] ?? true;
            $brand->ordering = $data['ordering'] ?? 0;
            $brand->save();

            $brand->translate(app()->getLocale(), [
                'name' => $data['translation']['name'],
                'description' => $data['translation']['description'] ?? null,
                'meta_title' => $data['translation']['meta_title'] ?? null,
                'meta_description' => $data['translation']['meta_description'] ?? null,
                'meta_keywords' => $data['translation']['meta_keywords'] ?? null,
            ]);

            event(new BrandStoreEvent($brand, $data));

            return [
                'ok' => true,
                'message' => trans('brand::base.messages.created'),
                'data' => BrandResource::make($brand),
                'status' => 201
            ];
        });
    }

    /**
     * Update the specified brand.
     *
     * @param int $brand_id
     * @param array $data
     *
     * @return array
     * @throws Throwable
     */
    public function update(int $brand_id, array $data): array
    {
        $validator = Validator::make($data, (new UpdateBrandRequest)->setBrandId($brand_id)->rules());
        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return [
                'ok' => false,
                'message' => trans('brand::base.validation.errors'),
                'errors' => $errors,
                'status' => 422
            ];
        } else {
            $data = $validator->validated();
        }

        return DB::transaction(function () use ($brand_id, $data) {
            /**
             * @var BrandModel $brand
             */
            $brand = BrandModel::find($brand_id);

            if (!$brand) {
                throw new BrandNotFoundException($brand_id);
            }

            if (array_key_exists('status', $data)) {
                $brand->status = $data['status'];
            }

            if (array_key_exists('ordering', $data)) {
                $brand->ordering = $data['ordering'];
            }

            if (array_key_exists('translation', $data)) {
                $trnas = [];
                if (array_key_exists('name', $data['translation'])) {
                    $trnas['name'] = $data['translation']['name'];
                }

                if (array_key_exists('description', $data['translation'])) {
                    $trnas['description'] = $data['translation']['description'];
                }

                if (array_key_exists('meta_title', $data['translation'])) {
                    $trnas['meta_title'] = $data['translation']['meta_title'];
                }

                if (array_key_exists('meta_description', $data['translation'])) {
                    $trnas['meta_description'] = $data['translation']['meta_description'];
                }

                if (array_key_exists('meta_keywords', $data['translation'])) {
                    $trnas['meta_keywords'] = $data['translation']['meta_keywords'];
                }

                $brand->translate(app()->getLocale(), $trnas);
            }

            $brand->save();

            event(new BrandUpdateEvent($brand, $data));

            return [
                'ok' => true,
                'message' => trans('brand::base.messages.updated'),
                'data' => BrandResource::make($brand),
                'status' => 200
            ];
        });
    }

    /**
     * Delete the specified brand.
     *
     * @param int $brand_id
     *
     * @return array
     * @throws Throwable
     */
    public function delete(int $brand_id): array
    {
        return DB::transaction(function () use ($brand_id) {
            /**
             * @var BrandModel $brand
             */
            $brand = BrandModel::find($brand_id);

            if (!$brand) {
                throw new BrandNotFoundException($brand_id);
            }

            $check_used = $this->hasUsed($brand_id);

            if ($check_used) {
                $count = BrandRelation::query()->where([
                    'brand_id' => $brand_id
                ])->count();

                throw new BrandUsedInException($brand_id, $count);
            }

            event(new BrandDeleteEvent($brand));

            $data = BrandResource::make($brand);

            $brand->translations()->delete();

            $brand->delete();

            return [
                'ok' => true,
                'data' => $data,
                'message' => trans('brand::base.messages.deleted'),
                'status' => 200
            ];
        });
    }

    /**
     * Restore the specified brand.
     *
     * @param int $brand_id
     *
     * @return array
     * @throws Throwable
     */
    public function restore(int $brand_id): array
    {
        return DB::transaction(function () use ($brand_id) {
            /**
             * @var BrandModel $brand
             */
            $brand = BrandModel::onlyTrashed()->find($brand_id);

            if (!$brand) {
                throw new BrandNotFoundException($brand_id);
            }

            event(new BrandRestoreEvent($brand));

            $data = BrandResource::make($brand);

            $brand->restore();

            return [
                'ok' => true,
                'data' => $data,
                'message' => trans('brand::base.messages.restored'),
                'status' => 200
            ];
        });
    }

    /**
     * Force Delete the specified brand.
     *
     * @param int $brand_id
     *
     * @return array
     * @throws Throwable
     */
    public function forceDelete(int $brand_id): array
    {
        return DB::transaction(function () use ($brand_id) {
            /**
             * @var BrandModel $brand
             */
            $brand = BrandModel::onlyTrashed()->find($brand_id);

            if (!$brand) {
                throw new BrandNotFoundException($brand_id);
            }

            event(new BrandForceDeleteEvent($brand));

            $data = BrandResource::make($brand);

            $brand->forceDelete();

            return [
                'ok' => true,
                'data' => $data,
                'message' => trans('brand::base.messages.permanently_deleted'),
                'status' => 200
            ];
        });
    }

    /**
     * Used In brand
     *
     * @param int $brand_id
     *
     * @return array
     * @throws Throwable
     */
    public function usedIn(int $brand_id): array
    {
        /**
         * @var BrandModel $brand
         */
        $brand = BrandModel::find($brand_id);

        if (!$brand) {
            throw new BrandNotFoundException($brand_id);
        }

        $brand_relations = BrandRelation::query()->where([
            'brand_id' => $brand_id
        ])->get();

        return [
            'ok' => true,
            'message' => trans('brand::base.messages.used_in', [
                'count' => $brand_relations->count()
            ]),
            'data' => BrandRelationResource::collection($brand_relations),
            'status' => 200
        ];
    }

    /**
     * Has Used brand
     *
     * @param int $brand_id
     *
     * @return bool
     * @throws Throwable
     */
    public function hasUsed(int $brand_id): bool
    {
        /**
         * @var BrandModel $brand
         */
        $brand = BrandModel::find($brand_id);

        if (!$brand) {
            throw new BrandNotFoundException($brand_id);
        }

        return BrandRelation::query()->where([
            'brand_id' => $brand_id
        ])->exists();
    }
}
