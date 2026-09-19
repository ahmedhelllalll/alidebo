<?php

namespace Database\Factories;

use App\Models\BusinessProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_profile_id' => BusinessProfile::first()->id ?? 1,
            'sku' => $this->faker->unique()->numerify('SKU-#####'),
            'unit' => $this->faker->randomElement(['piece', 'kg', 'carton', 'box']),
            'moq' => $this->faker->numberBetween(1, 100),
            'base_price' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => 'EGP',
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
