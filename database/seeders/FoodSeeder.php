<?php

namespace Database\Seeders;

use App\Models\Food;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's fruit catalog.
     */
    public function run(): void
    {
        $fruits = [
            ['name' => 'Apple', 'energy' => 52, 'protein' => 0.26, 'carbohydrates' => 13.81, 'fat' => 0.17, 'sugars' => 10.39, 'salt' => 0.001, 'image_path' => 'images/fruits/apple.svg'],
            ['name' => 'Banana', 'energy' => 89, 'protein' => 1.09, 'carbohydrates' => 22.84, 'fat' => 0.33, 'sugars' => 12.23, 'salt' => 0.001, 'image_path' => 'images/fruits/banana.svg'],
            ['name' => 'Blueberry', 'energy' => 57, 'protein' => 0.74, 'carbohydrates' => 14.49, 'fat' => 0.33, 'sugars' => 9.96, 'salt' => 0, 'image_path' => 'images/fruits/blueberry.svg'],
            ['name' => 'Mango', 'energy' => 60, 'protein' => 0.82, 'carbohydrates' => 15, 'fat' => 0.38, 'sugars' => 13.66, 'salt' => 0.001, 'image_path' => 'images/fruits/mango.svg'],
            ['name' => 'Orange', 'energy' => 47, 'protein' => 0.94, 'carbohydrates' => 11.75, 'fat' => 0.12, 'sugars' => 9.35, 'salt' => 0, 'image_path' => 'images/fruits/orange.svg'],
            ['name' => 'Pineapple', 'energy' => 50, 'protein' => 0.54, 'carbohydrates' => 13.12, 'fat' => 0.12, 'sugars' => 9.85, 'salt' => 0.001, 'image_path' => 'images/fruits/pineapple.svg'],
            ['name' => 'Strawberry', 'energy' => 32, 'protein' => 0.67, 'carbohydrates' => 7.68, 'fat' => 0.30, 'sugars' => 4.89, 'salt' => 0.001, 'image_path' => 'images/fruits/strawberry.svg'],
            ['name' => 'Watermelon', 'energy' => 30, 'protein' => 0.61, 'carbohydrates' => 7.55, 'fat' => 0.15, 'sugars' => 6.20, 'salt' => 0.001, 'image_path' => 'images/fruits/watermelon.svg'],
        ];

        foreach ($fruits as $fruit) {
            Food::query()->updateOrCreate(
                ['name' => $fruit['name']],
                [...$fruit, 'type' => Food::TYPE_FRUIT],
            );
        }
    }
}