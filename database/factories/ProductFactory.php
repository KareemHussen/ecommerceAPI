<?php

namespace Database\Factories;

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
            'name' => $this->faker->name,
            "image" => $this->faker->image,
            "description" => $this->faker->paragraph,
            "price" => $this->faker->randomFloat(2 , 1 , 5000),
            "priceBefore" => $this->faker->randomFloat(2 , 1 , 5000),
            "user_id" => 1
        ];
    }
}
