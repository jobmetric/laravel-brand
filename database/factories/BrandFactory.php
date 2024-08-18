<?php

namespace JobMetric\Brand\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Brand\Models\Brand;

/**
 * @extends Factory<Brand>
 */
class BrandFactory extends Factory
{
    protected $model = Brand::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => $this->faker->boolean,
            'ordering' => $this->faker->randomNumber(3),
            'visits' => $this->faker->randomNumber(3),
            'likes' => $this->faker->randomNumber(3)
        ];
    }

    /**
     * set status
     *
     * @param bool $status
     *
     * @return static
     */
    public function setStatus(bool $status): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => $status
        ]);
    }

    /**
     * set ordering
     *
     * @param int $ordering
     *
     * @return static
     */
    public function setOrdering(int $ordering): static
    {
        return $this->state(fn(array $attributes) => [
            'ordering' => $ordering
        ]);
    }

    /**
     * set visit
     *
     * @param int $visits
     *
     * @return static
     */
    public function setVisits(int $visits): static
    {
        return $this->state(fn(array $attributes) => [
            'visits' => $visits
        ]);
    }

    /**
     * set likes
     *
     * @param int $likes
     *
     * @return static
     */
    public function setLikes(int $likes): static
    {
        return $this->state(fn(array $attributes) => [
            'likes' => $likes
        ]);
    }
}
