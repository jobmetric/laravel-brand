<?php

namespace JobMetric\Brand\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use JobMetric\Brand\Events\BrandAllowMemberCollectionEvent;
use JobMetric\Brand\Events\BrandMediaAllowCollectionEvent;
use JobMetric\Layout\Contracts\LayoutContract;
use JobMetric\Layout\HasLayout;
use JobMetric\Like\HasLike;
use JobMetric\Media\Contracts\MediaContract;
use JobMetric\Media\HasFile;
use JobMetric\Membership\Contracts\MemberContract;
use JobMetric\Membership\HasMember;
use JobMetric\Metadata\Contracts\MetaContract;
use JobMetric\Metadata\HasMeta;
use JobMetric\Metadata\Metaable;
use JobMetric\PackageCore\Models\HasBooleanStatus;
use JobMetric\Translation\Contracts\TranslationContract;
use JobMetric\Translation\HasTranslation;
use JobMetric\Url\HasUrl;

/**
 * JobMetric\Brand\Models\Brand
 *
 * @property mixed id
 * @property mixed status
 * @property mixed ordering
 * @property mixed visits
 * @property mixed likes
 * @property mixed created_at
 * @property mixed updated_at
 *
 * @property BrandRelation[] brandRelations
 *
 * @method static find(int $brand_id)
 */
class Brand extends Model implements TranslationContract, MediaContract, MetaContract, MemberContract, LayoutContract
{
    use HasFactory,
        SoftDeletes,
        HasBooleanStatus,
        HasTranslation,
        HasFile,
        HasMeta,
        Metaable,
        HasMember,
        HasLike,
        HasLayout,
        HasUrl;

    protected $fillable = [
        'status',
        'ordering',
        'visits',
        'likes',
    ];

    protected $casts = [
        'status' => 'boolean',
        'ordering' => 'integer',
        'visits' => 'integer',
        'likes' => 'integer',
    ];

    public function getTable()
    {
        return config('brand.tables.brand', parent::getTable());
    }

    public function translationAllowFields(): array
    {
        return [
            'name',
            'description',
            'meta_title',
            'meta_description',
            'meta_keywords',
        ];
    }

    /**
     * media allow collections.
     *
     * @return array
     */
    public function mediaAllowCollections(): array
    {
        $event = new BrandMediaAllowCollectionEvent([
            'base' => [
                'media_collection' => 'public',
                'size' => [
                    'default' => [
                        'w' => config('brand.default_image_size.width'),
                        'h' => config('brand.default_image_size.height'),
                    ]
                ]
            ],
        ]);

        event($event);

        return $event->mediaAllowCollection;
    }

    /**
     * allow the member collection.
     *
     * @return array
     */
    public function allowMemberCollection(): array
    {
        $event = new BrandAllowMemberCollectionEvent([
            'owner' => 'single',
        ]);

        event($event);

        return $event->allowMemberCollection;
    }

    /**
     * Layout page type.
     *
     * @return string
     */
    public function layoutPageType(): string
    {
        return 'brand';
    }

    /**
     * Layout collection field.
     *
     * @return string|null
     */
    public function layoutCollectionField(): ?string
    {
        return null;
    }

    public function brandRelations(): HasMany
    {
        return $this->hasMany(BrandRelation::class, 'brand_id', 'id');
    }
}
