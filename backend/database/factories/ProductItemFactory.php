<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductItem>
 */
class ProductItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'project_id' => \App\Models\Project::inRandomOrder()->first()->id ?? \App\Models\Project::factory(),
        'name' => $this->faker->word,
        'version' => $this->faker->randomElement(['v1.0', 'v2.0', 'beta']),
        'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'blocked']),
    ];
}

}
