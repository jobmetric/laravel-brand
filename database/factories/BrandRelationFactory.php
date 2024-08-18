<?php

namespace JobMetric\Brand\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Brand\Models\BrandRelation;

/**
 * @extends Factory<BrandRelation>
 */
class BrandRelationFactory extends Factory
{
    protected $model = BrandRelation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_id' => null,
            'brandable_type' => null,
            'brandable_id' => null
        ];
    }

    /**
     * set brand_id
     *
     * @param int $brand_id
     *
     * @return static
     */
    public function setBrandId(int $brand_id): static
    {
        return $this->state(fn(array $attributes) => [
            'brand_id' => $brand_id
        ]);
    }

    /**
     * set brandable
     *
     * @param string $brandable_type
     * @param int $brandable_id
     *
     * @return static
     */
    public function setBrandable(string $brandable_type, int $brandable_id): static
    {
        return $this->state(fn(array $attributes) => [
            'brandable_type' => $brandable_type,
            'brandable_id' => $brandable_id
        ]);
    }
}
