<?php

namespace App\Livewire;

use App\Services\FruitRecommendationService;
use Livewire\Attributes\Locked;
use Livewire\Component;

class MyPreferences extends Component
{
    #[Locked]
    public array $fruits = [];

    #[Locked]
    public bool $mlAvailable = false;

    #[Locked]
    public bool $trained = false;

    #[Locked]
    public int $feedbackCount = 0;

    public function mount(FruitRecommendationService $recommendations): void
    {
        $this->refreshPreferences($recommendations);
    }

    public function refreshPreferences(FruitRecommendationService $recommendations): void
    {
        $preferences = $recommendations->preferencesFor($this->profileId());
        $this->fruits = $preferences['fruits'];
        $this->mlAvailable = $preferences['mlAvailable'];
        $this->trained = $preferences['trained'];
        $this->feedbackCount = $preferences['feedbackCount'];
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.my-preferences');
    }

    private function profileId(): string
    {
        return hash('sha256', session()->getId());
    }
}
