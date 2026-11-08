<?php

namespace App\Livewire;

use App\Models\Food;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AddFruit extends Component
{
    public string $name = '';

    public string $energy = '';

    public string $protein = '';

    public string $carbohydrates = '';

    public string $fat = '';

    public string $sugars = '';

    public string $salt = '';

    public function randomizeNutrition(): void
    {
        $fruits = Food::query()
            ->where('type', Food::TYPE_FRUIT)
            ->get(['energy', 'protein', 'carbohydrates', 'fat', 'sugars', 'salt']);

        $this->energy = $this->randomizedValue(
            $fruits->min('energy'),
            $fruits->max('energy'),
            30,
            89,
            0,
        );
        $this->protein = $this->randomizedValue(
            $fruits->min('protein'),
            $fruits->max('protein'),
            0.26,
            1.09,
            2,
        );
        $this->carbohydrates = $this->randomizedValue(
            $fruits->min('carbohydrates'),
            $fruits->max('carbohydrates'),
            7.55,
            22.84,
            2,
        );
        $this->fat = $this->randomizedValue(
            $fruits->min('fat'),
            $fruits->max('fat'),
            0.12,
            0.38,
            2,
        );
        $this->sugars = $this->randomizedValue(
            $fruits->min('sugars'),
            $fruits->max('sugars'),
            4.89,
            13.66,
            2,
        );
        $this->salt = $this->randomizedValue(
            $fruits->min('salt'),
            $fruits->max('salt'),
            0,
            0.001,
            3,
        );
        $this->resetValidation();
    }

    public function save(): mixed
    {
        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:40',
                'regex:/[A-Za-z0-9]/',
                Rule::unique('food', 'name'),
            ],
            'energy' => ['required', 'integer', 'min:0', 'max:1000'],
            'protein' => ['required', 'numeric', 'min:0', 'max:100'],
            'carbohydrates' => ['required', 'numeric', 'min:0', 'max:100'],
            'fat' => ['required', 'numeric', 'min:0', 'max:100'],
            'sugars' => ['required', 'numeric', 'min:0', 'max:100'],
            'salt' => ['required', 'numeric', 'min:0', 'max:10'],
        ]);

        Food::query()->create([
            ...$validated,
            'name' => trim($validated['name']),
            'type' => Food::TYPE_FRUIT,
        ]);

        session()->flash('status', "{$validated['name']} was added to the fruit catalog.");

        return redirect()->route('home');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.add-fruit');
    }

    private function randomizedValue(
        int|float|string|null $minimum,
        int|float|string|null $maximum,
        float $fallbackMinimum,
        float $fallbackMaximum,
        int $decimalPlaces,
    ): string {
        $scale = 10 ** $decimalPlaces;
        $scaledMinimum = (int) round(($minimum ?? $fallbackMinimum) * $scale);
        $scaledMaximum = (int) round(($maximum ?? $fallbackMaximum) * $scale);
        $value = random_int($scaledMinimum, $scaledMaximum);

        return number_format($value / $scale, $decimalPlaces, '.', '');
    }
}
