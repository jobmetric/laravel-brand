<?php

namespace JobMetric\Brand\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use JobMetric\Brand\Events\BrandableResourceEvent;

/**
 * JobMetric\Brand\Models\BrandRelation
 *
 * @property mixed brand_id
 * @property mixed brandable_type
 * @property mixed brandable_id
 * @property mixed created_at
 *
 * @property Brand brand
 * @property mixed brandable
 * @property mixed brandable_resource
 */
class BrandRelation extends Pivot
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'brand_id',
        'brandable_type',
        'brandable_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'brand_id' => 'integer',
        'brandable_type' => 'string',
        'brandable_id' => 'integer'
    ];

    public function getTable()
    {
        return config('brand.tables.brand_relation', parent::getTable());
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function brandable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the brandable resource attribute.
     */
    public function getBrandableResourceAttribute()
    {
        $event = new BrandableResourceEvent($this->brandable);
        event($event);

        return $event->resource;
    }
}
