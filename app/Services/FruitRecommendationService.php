<?php

namespace App\Services;

use App\Models\Food;

class FruitRecommendationService
{
    public function __construct(
        private readonly CleanRlClient $cleanRl,
    ) {
    }

    /**
     * @return array{food: ?Food, scores: array<int, array{recommendation: float, like: float}>, mlAvailable: bool}
     */
    public function recommendFor(string $profileId): array
    {
        $available = Food::query()
            ->where('type', Food::TYPE_FRUIT)
            ->orderBy('id')
            ->get();

        if ($available->isEmpty()) {
            return ['food' => null, 'scores' => [], 'mlAvailable' => false];
        }

        $result = $this->cleanRl->recommend([
            'profile_id' => $profileId,
            'fruits' => $available->map(fn (Food $food): array => [
                'id' => $food->id,
                'fruit_key' => str($food->name)->slug()->toString(),
                'name' => $food->name,
                'features' => [
                    'energy' => $food->energy,
                    'protein' => $food->protein,
                    'carbohydrates' => $food->carbohydrates,
                    'fat' => $food->fat,
                    'sugars' => $food->sugars,
                    'salt' => $food->salt,
                ],
            ])->all(),
        ]);

        $scores = [];
        foreach ($result['scores'] ?? [] as $score) {
            if (! is_array($score)) {
                continue;
            }

            $fruitId = $score['fruit_id'] ?? null;
            $recommendationProbability = $score['recommendation_probability'] ?? null;
            $likeProbability = $score['like_probability'] ?? null;
            if (
                is_int($fruitId)
                && is_numeric($recommendationProbability)
                && is_numeric($likeProbability)
            ) {
                $scores[$fruitId] = [
                    'recommendation' => max(0, min(1, (float) $recommendationProbability)),
                    'like' => max(0, min(1, (float) $likeProbability)),
                ];
            }
        }

        $recommendedFood = is_int($result['fruit_id'] ?? null)
            ? $available->firstWhere('id', $result['fruit_id'])
            : null;

        return [
            'food' => $recommendedFood instanceof Food
                ? $recommendedFood
                : $available->random(),
            'scores' => $scores,
            'mlAvailable' => $scores !== [],
        ];
    }

    /**
     * @return array{fruits: array<int, array{name: string, like: ?float}>, mlAvailable: bool, trained: bool, feedbackCount: int}
     */
    public function preferencesFor(string $profileId): array
    {
        $available = Food::query()
            ->where('type', Food::TYPE_FRUIT)
            ->orderBy('name')
            ->get();

        if ($available->isEmpty()) {
            return [
                'fruits' => [],
                'mlAvailable' => false,
                'trained' => false,
                'feedbackCount' => 0,
            ];
        }

        $result = $this->cleanRl->preferences([
            'profile_id' => $profileId,
            'fruits' => $this->fruitCandidates($available),
        ]);

        $scores = [];
        foreach ($result['scores'] ?? [] as $score) {
            if (
                is_array($score)
                && is_int($score['fruit_id'] ?? null)
                && is_numeric($score['like_probability'] ?? null)
            ) {
                $scores[$score['fruit_id']] = max(
                    0,
                    min(1, (float) $score['like_probability']),
                );
            }
        }

        return [
            'fruits' => $available->map(fn (Food $fruit): array => [
                'name' => $fruit->name,
                'like' => $scores[$fruit->id] ?? null,
            ])->all(),
            'mlAvailable' => $result !== null && count($scores) === $available->count(),
            'trained' => (bool) ($result['trained'] ?? false),
            'feedbackCount' => (int) ($result['feedback_count'] ?? 0),
        ];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection<int, Food>  $fruits
     * @return array<int, array<string, mixed>>
     */
    private function fruitCandidates(\Illuminate\Database\Eloquent\Collection $fruits): array
    {
        return $fruits->map(fn (Food $food): array => [
            'id' => $food->id,
            'fruit_key' => str($food->name)->slug()->toString(),
            'name' => $food->name,
            'features' => [
                'energy' => $food->energy,
                'protein' => $food->protein,
                'carbohydrates' => $food->carbohydrates,
                'fat' => $food->fat,
                'sugars' => $food->sugars,
                'salt' => $food->salt,
            ],
        ])->all();
    }

    public function learn(string $profileId, Food $food, int $reward): void
    {
        $this->cleanRl->submitFeedback($profileId, $food, $reward);
    }
}
