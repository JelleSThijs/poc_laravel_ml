<?php

namespace App\Livewire;

use App\Models\Food;
use App\Services\FruitRecommendationService;
use Livewire\Attributes\Locked;
use Livewire\Component;

class SwipeCard extends Component
{
    #[Locked]
    public ?int $foodId = null;

    #[Locked]
    public int $ratedCount = 0;

    #[Locked]
    public ?string $lastAction = null;

    #[Locked]
    public ?string $lastFoodName = null;

    #[Locked]
    public array $probabilitiesByFruitId = [];

    #[Locked]
    public bool $mlAvailable = false;

    public function mount(FruitRecommendationService $recommendations): void
    {
        $this->ratedCount = (int) session($this->feedbackCountKey(), 0);
        $this->loadNext($recommendations);
    }

    public function like(FruitRecommendationService $recommendations): void
    {
        $this->rate(1, $recommendations);
    }

    public function pass(FruitRecommendationService $recommendations): void
    {
        $this->rate(-1, $recommendations);
    }

    public function render(): \Illuminate\View\View
    {
        $food = $this->foodId === null
            ? null
            : Food::query()->find($this->foodId);

        $recommendationScores = Food::query()
            ->where('type', Food::TYPE_FRUIT)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Food $fruit): array => [
                'name' => $fruit->name,
                'recommendation' => $this->probabilitiesByFruitId[$fruit->id]['recommendation'] ?? null,
                'like' => $this->probabilitiesByFruitId[$fruit->id]['like'] ?? null,
            ])
            ->sortByDesc('recommendation')
            ->values();

        return view('livewire.swipe-card', [
            'food' => $food,
            'recommendationScores' => $recommendationScores,
        ]);
    }

    private function rate(int $reward, FruitRecommendationService $recommendations): void
    {
        abort_if($this->foodId === null, 409);

        $food = Food::query()
            ->where('type', Food::TYPE_FRUIT)
            ->findOrFail($this->foodId);

        $this->lastAction = $reward === 1 ? 'like' : 'pass';
        $this->lastFoodName = $food->name;
        $this->ratedCount++;
        session([$this->feedbackCountKey() => $this->ratedCount]);

        $recommendations->learn($this->profileId(), $food, $reward);
        $this->loadNext($recommendations);
    }

    private function loadNext(FruitRecommendationService $recommendations): void
    {
        $result = $recommendations->recommendFor($this->profileId());
        $this->foodId = $result['food']?->id;
        $this->probabilitiesByFruitId = $result['scores'];
        $this->mlAvailable = $result['mlAvailable'];
    }

    private function profileId(): string
    {
        return hash('sha256', session()->getId());
    }

    private function feedbackCountKey(): string
    {
        return 'fruitmatch.feedback_count';
    }
}
