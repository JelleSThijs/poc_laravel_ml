<?php

namespace Database\Factories;

use App\Models\Food;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Food>
 */
class FoodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fruit = fake()->unique()->randomElement([
            ['name' => 'Apple', 'image_path' => 'images/fruits/apple.svg'],
            ['name' => 'Banana', 'image_path' => 'images/fruits/banana.svg'],
            ['name' => 'Blueberry', 'image_path' => 'images/fruits/blueberry.svg'],
            ['name' => 'Mango', 'image_path' => 'images/fruits/mango.svg'],
            ['name' => 'Orange', 'image_path' => 'images/fruits/orange.svg'],
            ['name' => 'Pineapple', 'image_path' => 'images/fruits/pineapple.svg'],
            ['name' => 'Strawberry', 'image_path' => 'images/fruits/strawberry.svg'],
            ['name' => 'Watermelon', 'image_path' => 'images/fruits/watermelon.svg'],
        ]);

        return [
            'name' => $fruit['name'],
            'type' => Food::TYPE_FRUIT,
            'energy' => fake()->numberBetween(25, 100),
            'protein' => fake()->randomFloat(2, 0, 2),
            'carbohydrates' => fake()->randomFloat(2, 5, 25),
            'fat' => fake()->randomFloat(2, 0, 1),
            'sugars' => fake()->randomFloat(2, 3, 20),
            'salt' => fake()->randomFloat(3, 0, 0.01),
            'image_path' => $fruit['image_path'],
        ];
    }
}