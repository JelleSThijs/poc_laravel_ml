<?php

namespace App\Services;

use App\Models\Food;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CleanRlClient
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function recommend(array $payload): ?array
    {
        $response = $this->send('/recommend', $payload);
        if ($response === null || ! $response->successful()) {
            return null;
        }

        $recommendation = $response->json();
        if (! is_array($recommendation)) {
            return null;
        }

        $fruitId = $recommendation['fruit_id'] ?? null;

        return is_int($fruitId) ? $recommendation : null;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>|null
     */
    public function preferences(array $payload): ?array
    {
        $response = $this->send('/preferences', $payload);
        if ($response === null || ! $response->successful()) {
            return null;
        }

        $preferences = $response->json();

        return is_array($preferences) ? $preferences : null;
    }

    public function submitFeedback(string $profileId, Food $fruit, int $reward): void
    {
        $this->send('/feedback', [
            'profile_id' => $profileId,
            'feedback_id' => (string) Str::uuid(),
            'fruit_id' => $fruit->id,
            'fruit_key' => Str::slug($fruit->name),
            'action' => $reward === 1 ? 'like' : 'dislike',
            'reward' => $reward,
            'features' => [
                'energy' => $fruit->energy,
                'protein' => $fruit->protein,
                'carbohydrates' => $fruit->carbohydrates,
                'fat' => $fruit->fat,
                'sugars' => $fruit->sugars,
                'salt' => $fruit->salt,
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function send(string $path, array $payload): ?\Illuminate\Http\Client\Response
    {
        try {
            $response = Http::baseUrl(config('fruitmatch.cleanrl_url'))
                ->acceptJson()
                ->timeout(30)
                ->post($path, $payload);

            if (! $response->successful()) {
                Log::warning('CleanRL FastAPI request failed.', [
                    'path' => $path,
                    'status' => $response->status(),
                ]);
            }

            return $response;
        } catch (Throwable $exception) {
            Log::info('CleanRL FastAPI service unavailable.', [
                'path' => $path,
                'exception' => $exception::class,
            ]);

            return null;
        }
    }
}
