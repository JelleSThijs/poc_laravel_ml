<?php

namespace App\Models;

use Database\Factories\FoodFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    /** @var HasFactory<FoodFactory> */
    use HasFactory;

    public const TYPE_FRUIT = 'fruit';

    protected $table = 'food';

    protected $fillable = [
        'name',
        'type',
        'energy',
        'protein',
        'carbohydrates',
        'fat',
        'sugars',
        'salt',
        'image_path',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'energy' => 'integer',
            'protein' => 'decimal:2',
            'carbohydrates' => 'decimal:2',
            'fat' => 'decimal:2',
            'sugars' => 'decimal:2',
            'salt' => 'decimal:3',
        ];
    }
}