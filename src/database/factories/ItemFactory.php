<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->word(),
            'brand' => $this->faker->optional()->word(),
            'price' => 1000,
            'description' => $this->faker->sentence(),
            'condition' => array_key_first(config('conditions')) ?? 'good',
            'image_path' => 'items/dummy.jpg',
            'is_sold' => false,
        ];
    }
}
