<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            'product_id' => Product::inRandomOrder()->first()->id ?? Product::factory(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph,
        ];
    }
}
